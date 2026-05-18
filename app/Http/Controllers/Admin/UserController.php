<?php
// app/Http/Controllers/Admin/UserController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\RevenueRecord;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    // ── Liste des utilisateurs ──────────────────────────────────────────────
    public function index(Request $request): View
    {
        $query = User::query();

        // Filtres
        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('first_name', 'like', '%'.$request->search.'%')
                  ->orWhere('last_name',  'like', '%'.$request->search.'%')
                  ->orWhere('email',      'like', '%'.$request->search.'%')
                  ->orWhere('country',    'like', '%'.$request->search.'%')
            );
        }
        if ($request->filled('role'))   $query->where('role', $request->role);
        if ($request->filled('status')) {
            $request->status === 'active'
                ? $query->where('is_active', true)
                : $query->where('is_active', false);
        }

        // Tri
        match($request->get('sort', 'latest')) {
            'oldest'      => $query->oldest(),
            'name'        => $query->orderBy('first_name')->orderBy('last_name'),
            'enrollments' => $query->withCount('enrollments')->orderByDesc('enrollments_count'),
            default       => $query->latest(),
        };

        $users = $query->paginate(20)->appends(request()->query());

        // Stats sidebar
        $stats = [
            'students' => User::where('role', 'student')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'admins'   => User::where('role', 'admin')->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    // ── Détail d'un utilisateur ─────────────────────────────────────────────
    public function show(User $user): View
    {
        // Stats selon le rôle
        if ($user->isStudent()) {
            $activityStats = [
                'courses_enrolled'  => Enrollment::where('user_id', $user->id)->count(),
                'courses_completed' => Enrollment::where('user_id', $user->id)->whereNotNull('completed_at')->count(),
                'quiz_attempts'     => QuizAttempt::where('user_id', $user->id)->count(),
                'badges_count'      => UserBadge::where('user_id', $user->id)->count(),
            ];
            $enrollments = Enrollment::with(['course.teacher'])
                ->where('user_id', $user->id)->latest()->take(10)->get();
            $courses = collect();
        } elseif ($user->isTeacher()) {
            $courseIds = Course::where('user_id', $user->id)->pluck('id');
            $activityStats = [
                'courses_created'   => Course::where('user_id', $user->id)->count(),
                'courses_published' => Course::where('user_id', $user->id)->where('status', 'published')->count(),
                'total_students'    => Enrollment::whereIn('course_id', $courseIds)->distinct('user_id')->count('user_id'),
                'total_revenue'     => (int) RevenueRecord::where('teacher_id', $user->id)->completed()->sum('net_amount'),
            ];
            $courses     = Course::where('user_id', $user->id)->withCount('enrollments')->latest()->take(10)->get();
            $enrollments = collect();
        } else {
            $activityStats = ['courses_enrolled'=>0,'courses_completed'=>0,'quiz_attempts'=>0,'badges_count'=>0];
            $enrollments   = collect();
            $courses       = collect();
        }

        // Activité récente
        $recentActivity = $this->getUserActivity($user);

        return view('admin.users.show', compact(
            'user', 'activityStats', 'enrollments', 'courses', 'recentActivity'
        ));
    }

    // ── Formulaire création ─────────────────────────────────────────────────
    public function create(): View
    {
        return view('admin.users.create');
    }

    // ── Enregistrer un utilisateur ──────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name'  => ['required', 'string', 'max:50'],
            'email'      => ['required', 'email', 'unique:users'],
            'role'       => ['required', 'in:student,teacher,admin'],
            'password'   => ['required', 'confirmed', Password::defaults()],
            'phone'      => ['nullable', 'string', 'max:20'],
            'country'    => ['nullable', 'string', 'max:5'],
            'bio'        => ['nullable', 'string', 'max:500'],
            'is_active'  => ['boolean'],
        ]);

        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        $user = User::create($data);

        return redirect()->route('admin.users.show', $user)
            ->with('success', "✅ Utilisateur {$user->full_name} créé avec succès !");
    }

    // ── Formulaire modification ─────────────────────────────────────────────
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    // ── Mettre à jour ───────────────────────────────────────────────────────
    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name'  => ['required', 'string', 'max:50'],
            'email'      => ['required', 'email', "unique:users,email,{$user->id}"],
            'role'       => ['required', 'in:student,teacher,admin'],
            'password'   => ['nullable', 'confirmed', Password::defaults()],
            'phone'      => ['nullable', 'string', 'max:20'],
            'country'    => ['nullable', 'string', 'max:5'],
            'bio'        => ['nullable', 'string', 'max:500'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active');

        $user->update($data);

        return redirect()->route('admin.users.show', $user)
            ->with('success', "✅ Profil de {$user->full_name} mis à jour !");
    }

    // ── Activer / Désactiver ────────────────────────────────────────────────
    public function toggle(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'Vous ne pouvez pas désactiver votre propre compte.');

        $user->update(['is_active' => !$user->is_active]);

        $action = $user->is_active ? 'activé' : 'désactivé';

        return back()->with('success', "Compte de {$user->full_name} {$action}.");
    }

    // ── Supprimer un utilisateur ────────────────────────────────────────────
    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'Vous ne pouvez pas supprimer votre propre compte.');

        $name = $user->full_name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur {$name} supprimé.");
    }

    // ── Actions groupées ────────────────────────────────────────────────────
    public function bulkToggle(Request $request): RedirectResponse
    {
        $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['exists:users,id']]);
        User::whereIn('id', $request->ids)->whereNotIn('id', [auth()->id()])->update(['is_active' => true]);

        return back()->with('success', count($request->ids) . ' utilisateur(s) activé(s).');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['exists:users,id']]);
        User::whereIn('id', $request->ids)->whereNotIn('id', [auth()->id()])->delete();

        return back()->with('success', count($request->ids) . ' utilisateur(s) supprimé(s).');
    }

    // ── Activité récente d'un utilisateur ───────────────────────────────────
    private function getUserActivity(User $user): array
    {
        $activities = [];

        if ($user->isStudent()) {
            LessonProgress::where('user_id', $user->id)->where('is_completed', true)
                ->latest('completed_at')->take(4)->get()
                ->each(fn($p) => $activities[] = [
                    'icon'=>'✅','action'=>'Leçon complétée',
                    'detail'=>$p->lesson->title ?? '—','time'=>$p->completed_at,'color'=>'#25c26e',
                ]);

            QuizAttempt::where('user_id', $user->id)->latest()->take(3)->get()
                ->each(fn($a) => $activities[] = [
                    'icon'=>'📝','action'=>'Quiz passé ('.$a->score.'%)',
                    'detail'=>$a->quiz->title ?? '—','time'=>$a->created_at,'color'=>'#3b82f6',
                ]);
        }

        if ($user->isTeacher()) {
            Course::where('user_id', $user->id)->latest()->take(3)->get()
                ->each(fn($c) => $activities[] = [
                    'icon'=>'📚','action'=>'Cours '.$c->status_label,
                    'detail'=>$c->title,'time'=>$c->created_at,'color'=>'#e8b84b',
                ]);
        }

        usort($activities, fn($a, $b) => $b['time'] <=> $a['time']);
        return array_slice($activities, 0, 8);
    }
}