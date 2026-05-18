<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CourseController extends Controller
{
    // ── Liste des cours ──────────────────────────────────────────────────────
    public function index(Request $request): View
    {
        $query = Course::with('teacher')
            ->withCount(['enrollments', 'lessons', 'chapters']);

        // Filtre statut
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Recherche
        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('title', 'like', '%'.$request->search.'%')
                  ->orWhereHas('teacher', fn($t) =>
                      $t->where('first_name', 'like', '%'.$request->search.'%')
                        ->orWhere('last_name',  'like', '%'.$request->search.'%')
                  )
            );
        }

        // Tri
        match($request->get('sort', 'latest')) {
            'title'       => $query->orderBy('title'),
            'enrollments' => $query->orderByDesc('enrollments_count'),
            'price_asc'   => $query->orderBy('price'),
            'price_desc'  => $query->orderByDesc('price'),
            default       => $query->latest(),
        };

        $courses = $query->paginate(20)->appends(request()->query());

        // Compteurs
        $counts = [
            'all'       => Course::count(),
            'published' => Course::where('status', 'published')->count(),
            'pending'   => Course::where('status', 'pending')->count(),
            'draft'     => Course::where('status', 'draft')->count(),
            'rejected'  => Course::where('status', 'rejected')->count(),
        ];

        return view('admin.courses.index', compact('courses', 'counts'));
    }

    // ── Détail d'un cours ────────────────────────────────────────────────────
    public function show(Course $course): View
    {
        $course->load([
            'teacher',
            'chapters.lessons',
            'enrollments.user',
            'quizzes',
        ]);
        $course->loadCount(['enrollments', 'lessons', 'chapters', 'quizzes']);

        $recentEnrollments = $course->enrollments()
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        $completionRate = $course->enrollments_count > 0
            ? round($course->enrollments()->whereNotNull('completed_at')->count() / $course->enrollments_count * 100)
            : 0;

        return view('admin.courses.show', compact('course', 'recentEnrollments', 'completionRate'));
    }

    // ── Formulaire édition ───────────────────────────────────────────────────
    public function edit(Course $course): View
    {
        return view('admin.courses.edit', compact('course'));
    }

    // ── Mettre à jour ────────────────────────────────────────────────────────
    public function update(Request $request, Course $course): RedirectResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:3000'],
            'price'       => ['required', 'numeric', 'min:0'],
            'is_free'     => ['boolean'],
            'level'       => ['required', 'in:beginner,intermediate,advanced'],
            'status'      => ['required', 'in:draft,pending,published,rejected'],
        ]);

        $course->update($data);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', '✅ Cours mis à jour.');
    }

    // ── Valider un cours ─────────────────────────────────────────────────────
    public function approve(Course $course): RedirectResponse
    {
        abort_unless($course->status === 'pending', 422, 'Ce cours n\'est pas en attente.');

        $course->update(['status' => 'published']);

        // TODO: notifier le formateur
        // Notification::send($course->teacher, new CourseApprovedNotification($course));

        return back()->with('success', '✅ Cours publié et validé !');
    }

    // ── Rejeter un cours ─────────────────────────────────────────────────────
    public function reject(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'reason.required' => 'Veuillez indiquer la raison du rejet.',
            'reason.min'      => 'La raison doit contenir au moins 10 caractères.',
        ]);

        $course->update([
            'status'          => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        // TODO: notifier le formateur
        // Notification::send($course->teacher, new CourseRejectedNotification($course, $request->reason));

        return back()->with('success', '🚫 Cours rejeté. Le formateur sera notifié.');
    }

    // ── Supprimer un cours ───────────────────────────────────────────────────
    public function destroy(Course $course): RedirectResponse
    {
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', '🗑 Cours supprimé.');
    }

    // ── Forcer la dépublication ──────────────────────────────────────────────
    public function unpublish(Course $course): RedirectResponse
    {
        $course->update(['status' => 'draft']);

        return back()->with('success', '⏸ Cours dépublié et repassé en brouillon.');
    }
}