<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ForumReply;
use App\Models\ForumThread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ForumController extends Controller
{
    // ── Vue d'ensemble : tous les forums par cours ───────────────────────────
    public function overview(): View
    {
        // Stats globales
        $stats = [
            'total_threads'  => ForumThread::count(),
            'total_replies'  => ForumReply::count(),
            'solved'         => ForumThread::where('is_solved', true)->count(),
            'open'           => ForumThread::where('is_closed', false)->count(),
            'this_week'      => ForumThread::where('created_at', '>=', now()->subDays(7))->count(),
            'active_courses' => ForumThread::distinct('course_id')->count('course_id'),
        ];

        // Cours avec leurs stats forum
        $courses = Course::with('teacher')
            ->withCount([
                'forumThreads',
                'forumThreads as solved_threads_count'  => fn($q) => $q->where('is_solved', true),
                'forumThreads as open_threads_count'    => fn($q) => $q->where('is_closed', false)->where('is_solved', false),
                'forumThreads as recent_threads_count'  => fn($q) => $q->where('created_at', '>=', now()->subDays(7)),
            ])
            ->having('forum_threads_count', '>', 0)
            ->orderByDesc('forum_threads_count')
            ->paginate(12);

        // Threads récents (toutes activités)
        $recentThreads = ForumThread::with(['author', 'course'])
            ->latest()
            ->take(8)
            ->get();

        // Top contributeurs
        $topContributors = DB::table('forum_replies')
            ->join('users', 'forum_replies.user_id', '=', 'users.id')
            ->select('users.id', 'users.first_name', 'users.last_name', DB::raw('COUNT(*) as replies_count'))
            ->groupBy('users.id', 'users.first_name', 'users.last_name')
            ->orderByDesc('replies_count')
            ->take(5)
            ->get();

        return view('admin.forum.overview', compact('stats', 'courses', 'recentThreads', 'topContributors'));
    }

    // ── Liste des threads d'un cours ─────────────────────────────────────────
    public function index(Course $course, Request $request): View
    {
        $query = ForumThread::with(['author', 'replies'])
            ->where('course_id', $course->id)
            ->withCount('replies');

        // Filtres
        match($request->get('filter', 'all')) {
            'solved'   => $query->where('is_solved', true),
            'unsolved' => $query->where('is_solved', false)->where('is_closed', false),
            'closed'   => $query->where('is_closed', true),
            'pinned'   => $query->where('is_pinned', true),
            'recent'   => $query->where('created_at', '>=', now()->subDays(7)),
            default    => null,
        };

        // Recherche
        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('title', 'like', '%'.$request->search.'%')
                  ->orWhere('body',  'like', '%'.$request->search.'%')
            );
        }

        $threads = $query->orderByDesc('is_pinned')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Stats du cours
        $courseStats = [
            'total'   => ForumThread::where('course_id', $course->id)->count(),
            'solved'  => ForumThread::where('course_id', $course->id)->where('is_solved', true)->count(),
            'closed'  => ForumThread::where('course_id', $course->id)->where('is_closed', true)->count(),
            'pinned'  => ForumThread::where('course_id', $course->id)->where('is_pinned', true)->count(),
            'replies' => ForumReply::whereHas('thread', fn($q) => $q->where('course_id', $course->id))->count(),
        ];

        return view('admin.forum.index', compact('course', 'threads', 'courseStats'));
    }

    // ── Détail d'un thread ───────────────────────────────────────────────────
    public function show(Course $course, ForumThread $thread): View
    {
        abort_unless($thread->course_id === $course->id, 404);

        $thread->load([
            'author',
            'replies' => fn($q) => $q->whereNull('parent_id')->with(['author', 'children.author']),
        ]);

        return view('admin.forum.show', compact('course', 'thread'));
    }

    // ── Épingler / Désépingler ───────────────────────────────────────────────
    public function pin(ForumThread $thread): RedirectResponse
    {
        $thread->update(['is_pinned' => !$thread->is_pinned]);
        $msg = $thread->is_pinned ? '📌 Thread épinglé.' : '📌 Thread désépinglé.';
        return back()->with('success', $msg);
    }

    // ── Fermer / Réouvrir ────────────────────────────────────────────────────
    public function close(ForumThread $thread): RedirectResponse
    {
        $thread->update(['is_closed' => !$thread->is_closed]);
        $msg = $thread->is_closed ? '🔒 Thread fermé.' : '🔓 Thread réouvert.';
        return back()->with('success', $msg);
    }

    // ── Supprimer un thread ──────────────────────────────────────────────────
    public function destroyThread(ForumThread $thread): RedirectResponse
    {
        $courseSlug = $thread->course->slug;
        $thread->replies()->delete();
        $thread->delete();

        return redirect()
            ->route('admin.forum.index', $courseSlug)
            ->with('success', '🗑 Thread supprimé.');
    }

    // ── Supprimer une réponse ────────────────────────────────────────────────
    public function destroyReply(ForumReply $reply): RedirectResponse
    {
        // Supprimer les enfants (réponses imbriquées)
        $reply->children()->delete();
        $reply->delete();

        return back()->with('success', '🗑 Réponse supprimée.');
    }
}