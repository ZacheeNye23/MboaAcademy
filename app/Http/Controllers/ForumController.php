<?php
namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Course;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\UserBadge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ForumController extends Controller
{
    // ── Vue d'ensemble de tous les forums ──────────────────────────────────────
    public function overview(): \Illuminate\View\View
    {
        $user = auth()->user();

        // FIX : le teacher voit ses propres cours, pas ses inscriptions
        if ($user->isTeacher()) {
            $enrolledCourses = \App\Models\Course::with(['teacher', 'forumThreads'])
                ->where('user_id', $user->id)
                ->latest()
                ->get()
                ->map(function ($course) {
                    // On simule la même structure qu'une enrollment pour la vue
                    return (object)[
                        'course' => $course,
                    ];
                });
        } else {
            $enrolledCourses = \App\Models\Enrollment::with(['course.teacher', 'course.forumThreads'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return view('forum.overview', compact('enrolledCourses'));
    }

    // ── Vérification accès au forum ─────────────────────────────────────────────
    // FIX : le teacher accède à tous les cours (les siens + potentiellement tous)
    // L'étudiant doit être inscrit.
    private function checkAccess(Course $course): void
    {
        $user = Auth::user();

        if ($user->isStudent()) {
            abort_unless(
                $course->enrollments()->where('user_id', $user->id)->exists(),
                403, 'Vous devez être inscrit à ce cours pour accéder au forum.'
            );
        }
        // Les teachers et admins passent librement
    }

    // ── Liste des threads d'un cours ────────────────────────────────────────
    public function index(Course $course, Request $request): View
    {
        $this->checkAccess($course);

        $query = ForumThread::with(['author', 'replies'])
            ->where('course_id', $course->id)
            ->withCount('replies');

        match($request->get('filter', 'all')) {
            'solved'   => $query->where('is_solved', true),
            'unsolved' => $query->where('is_solved', false)->where('is_closed', false),
            'mine'     => $query->where('user_id', Auth::id()),
            'recent'   => $query->where('created_at', '>=', now()->subDays(7)),
            default    => null,
        };

        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('title', 'like', '%'.$request->search.'%')
                  ->orWhere('body', 'like', '%'.$request->search.'%')
            );
        }

        $threads = $query->orderByDesc('is_pinned')
            ->latest()
            ->paginate(15);

        return view('forum.index', compact('course', 'threads'));
    }

    // ── Formulaire création thread ───────────────────────────────────────────
    public function create(Course $course): View
    {
        $this->checkAccess($course);
        return view('forum.create', compact('course'));
    }

    // ── Enregistrer un nouveau thread ────────────────────────────────────────
    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->checkAccess($course);

        $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:150'],
            'body'  => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'title.required' => 'Le titre est obligatoire.',
            'title.min'      => 'Le titre doit contenir au moins 5 caractères.',
            'body.required'  => 'Le message est obligatoire.',
            'body.min'       => 'Votre message est trop court (min. 10 caractères).',
        ]);

        $thread = ForumThread::create([
            'course_id' => $course->id,
            'user_id'   => Auth::id(),
            'title'     => $request->title,
            'body'      => $request->body,
        ]);

        // Badge "Contributeur" — premier post dans le forum
        $badge = Badge::where('type', 'social')->first();
        if ($badge) {
            UserBadge::firstOrCreate([
                'user_id'  => Auth::id(),
                'badge_id' => $badge->id,
            ], ['earned_at' => now()]);
        }

        // FIX : utilise le prefix dynamique pour la redirection
        $prefix = Auth::user()->isTeacher() ? 'teacher.' : 'student.';

        return redirect()->route($prefix.'forum.show', [$course->slug, $thread->id])
            ->with('success', '💬 Discussion créée ! Les membres seront notifiés.');
    }

    // ── Voir un thread + ses réponses ────────────────────────────────────────
    public function show(Course $course, ForumThread $thread): View
    {
        $this->checkAccess($course);
        $thread->increment('views');

        $thread->load([
            'author',
            'replies' => fn($q) => $q->whereNull('parent_id')->with(['author', 'children.author']),
        ]);

        return view('forum.show', compact('course', 'thread'));
    }

    // ── Répondre à un thread ─────────────────────────────────────────────────
    public function reply(Request $request, Course $course, ForumThread $thread): RedirectResponse
    {
        $this->checkAccess($course);

        abort_if($thread->is_closed, 403, 'Cette discussion est fermée.');

        $request->validate([
            'body'      => ['required', 'string', 'min:3', 'max:3000'],
            'parent_id' => ['nullable', 'exists:forum_replies,id'],
        ], [
            'body.required' => 'Votre réponse ne peut pas être vide.',
            'body.min'      => 'Votre réponse est trop courte.',
        ]);

        ForumReply::create([
            'thread_id' => $thread->id,
            'user_id'   => Auth::id(),
            'body'      => $request->body,
            'parent_id' => $request->parent_id,
        ]);

        return back()->with('success', '✅ Réponse publiée !');
    }

    // ── Marquer une réponse comme solution ──────────────────────────────────
    public function markSolution(ForumReply $reply): RedirectResponse
    {
        $thread = $reply->thread;

        abort_unless(
            Auth::id() === $thread->user_id || Auth::user()->isTeacher(),
            403, 'Seul l\'auteur ou le formateur peut marquer une solution.'
        );

        ForumReply::where('thread_id', $thread->id)->update(['is_solution' => false]);

        $reply->update(['is_solution' => true]);
        $thread->update(['is_solved' => true]);

        return back()->with('success', '✅ Réponse marquée comme solution !');
    }

    // ── Épingler / désépingler un thread (teacher only) ─────────────────────
    public function pin(Course $course, ForumThread $thread): RedirectResponse
    {
        abort_unless(Auth::user()->isTeacher(), 403, 'Accès réservé aux formateurs.');

        $thread->update(['is_pinned' => !$thread->is_pinned]);

        $msg = $thread->is_pinned ? '📌 Discussion épinglée.' : '📌 Discussion désépinglée.';
        return back()->with('success', $msg);
    }

    // ── Fermer / rouvrir un thread (teacher only) ───────────────────────────
    public function close(Course $course, ForumThread $thread): RedirectResponse
    {
        abort_unless(Auth::user()->isTeacher(), 403, 'Accès réservé aux formateurs.');

        $thread->update(['is_closed' => !$thread->is_closed]);

        $msg = $thread->is_closed ? '🔒 Discussion fermée.' : '🔓 Discussion rouverte.';
        return back()->with('success', $msg);
    }

    // ── Supprimer une réponse ────────────────────────────────────────────────
    public function destroyReply(Course $course, ForumReply $reply): RedirectResponse
    {
        abort_unless(
            Auth::id() === $reply->user_id || Auth::user()->isTeacher(),
            403, 'Vous ne pouvez pas supprimer cette réponse.'
        );

        $reply->delete();

        return back()->with('success', '🗑 Réponse supprimée.');
    }

    // ── Supprimer un thread ──────────────────────────────────────────────────
    public function destroyThread(Course $course, ForumThread $thread): RedirectResponse
    {
        abort_unless(
            Auth::id() === $thread->user_id || Auth::user()->isTeacher(),
            403, 'Vous ne pouvez pas supprimer cette discussion.'
        );

        $prefix = Auth::user()->isTeacher() ? 'teacher.' : 'student.';

        $thread->replies()->delete();
        $thread->delete();

        return redirect()->route($prefix.'forum.index', $course->slug)
            ->with('success', '🗑 Discussion supprimée.');
    }

    // ── Indicateur de frappe (optionnel, retourne 200) ───────────────────────
    public function typing(Request $request, Course $course, ForumThread $thread)
    {
        // Peut être utilisé avec Pusher/broadcasting si besoin
        return response()->json(['ok' => true]);
    }
}