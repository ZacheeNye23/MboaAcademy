<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ForumThread;
use App\Models\ForumReply;
use App\Models\UserBadge;
use App\Models\Badge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ForumController extends Controller
{
    /** Liste des threads d'un cours */
    public function index(Course $course): View
    {
        $threads = ForumThread::with(['author', 'replies'])
            ->where('course_id', $course->id)
            ->orderByDesc('is_pinned')
            ->latest()
            ->paginate(15);

        return view('forum.index', compact('course', 'threads'));
    }

    /** Créer un thread */
    public function store(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:150'],
            'body'  => ['required', 'string', 'min:10'],
        ]);

        $thread = ForumThread::create([
            'course_id' => $course->id,
            'user_id'   => Auth::id(),
            'title'     => $request->title,
            'body'      => $request->body,
        ]);

        // Badge social : premier post
        $badge = Badge::where('type', 'social')->first();
        if ($badge) UserBadge::firstOrCreate(['user_id' => Auth::id(), 'badge_id' => $badge->id]);

        return redirect()->route('forum.show', [$course->slug, $thread->id])
            ->with('success', 'Discussion créée avec succès !');
    }

    /** Voir un thread + ses réponses */
    public function show(Course $course, ForumThread $thread): View
    {
        $thread->incrementViews();
        $thread->load(['author', 'replies.author', 'replies.children.author']);

        return view('forum.show', compact('course', 'thread'));
    }

    /** Répondre à un thread */
    public function reply(Request $request, ForumThread $thread): RedirectResponse
    {
        $request->validate(['body' => ['required', 'string', 'min:3']]);

        ForumReply::create([
            'thread_id' => $thread->id,
            'user_id'   => Auth::id(),
            'body'      => $request->body,
            'parent_id' => $request->parent_id,
        ]);

        return back()->with('success', 'Réponse ajoutée !');
    }

    /** Marquer une réponse comme solution (formateur ou auteur du thread) */
    public function markSolution(ForumReply $reply): RedirectResponse
    {
        $thread = $reply->thread;
        abort_unless(Auth::id() === $thread->user_id || Auth::user()->isTeacher(), 403);

        // Désactiver les autres solutions du thread
        ForumReply::where('thread_id', $thread->id)->update(['is_solution' => false]);
        $reply->update(['is_solution' => true]);
        $thread->update(['is_solved' => true]);

        return back()->with('success', 'Solution marquée !');
    }
}