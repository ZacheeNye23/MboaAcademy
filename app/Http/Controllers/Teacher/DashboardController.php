<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\RevenueRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $teacher   = Auth::user();
        $courseIds = Course::byTeacher($teacher->id)->pluck('id');

        // ── Variables $stats (KPIs dashboard) ────────────────────────────────
        $stats = [
            'total_students' => Enrollment::whereIn('course_id', $courseIds)
                                    ->distinct('user_id')->count('user_id'),
            'total_courses'  => Course::byTeacher($teacher->id)->count(),
            'published'      => Course::byTeacher($teacher->id)->where('status', 'published')->count(),
            'drafts'         => Course::byTeacher($teacher->id)->where('status', 'draft')->count(),
            'avg_rating'     => round(
                \App\Models\CourseReview::whereIn('course_id', $courseIds)->avg('rating') ?? 0, 1
            ),
            'total_reviews'  => \App\Models\CourseReview::whereIn('course_id', $courseIds)->count(),
        ];

        // ── Variables $revenues ───────────────────────────────────────────────
        $thisMonth  = (int) RevenueRecord::where('teacher_id', $teacher->id)->completed()
                            ->whereMonth('paid_at', now()->month)
                            ->whereYear('paid_at',  now()->year)
                            ->sum('net_amount');
        $lastMonth  = (int) RevenueRecord::where('teacher_id', $teacher->id)->completed()
                            ->whereMonth('paid_at', now()->subMonth()->month)
                            ->whereYear('paid_at',  now()->subMonth()->year)
                            ->sum('net_amount');
        $variation  = $lastMonth > 0 ? round(($thisMonth - $lastMonth) / $lastMonth * 100, 1) : 0;

        // monthly[1..12] → pour le mini graphe
        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthly[$m] = (int) RevenueRecord::where('teacher_id', $teacher->id)->completed()
                ->whereMonth('paid_at', $m)
                ->whereYear('paid_at', now()->year)
                ->sum('net_amount');
        }

        $revenues = [
            'this_month' => $thisMonth,
            'last_month' => $lastMonth,
            'variation'  => $variation,
            'total'      => (int) RevenueRecord::where('teacher_id', $teacher->id)->completed()->sum('net_amount'),
            'monthly'    => $monthly,
        ];

        // ── $courses : liste des cours pour la section "Mes cours" du dashboard ─
        $courses = Course::byTeacher($teacher->id)
            ->withCount('enrollments')
            ->withAvg('reviews', 'rating')
            ->withCount(['lessons as total_lessons'])
            ->latest()
            ->take(5)
            ->get();
        $topCourses = Course::byTeacher($teacher->id)
    ->withCount('enrollments')
    ->orderByDesc('enrollments_count')
    ->take(5)
    ->get();

        // ── $recentStudents ───────────────────────────────────────────────────
        $recentStudents = Enrollment::with(['user', 'course'])
            ->whereIn('course_id', $courseIds)
            ->latest('enrolled_at')
            ->take(6)
            ->get();

        // ── Forum : threads récents ───────────────────────────────────────────
        $forumThreads = ForumThread::with(['author', 'course', 'replies'])
            ->withCount('replies')
            ->whereIn('course_id', $courseIds)
            ->latest()
            ->take(5)
            ->get();

        // ── Unread count ──────────────────────────────────────────────────────
        // Thread non lu = a des réponses d'apprenants ET la dernière réponse n'est pas du formateur
        $unreadForumCount = ForumThread::whereIn('course_id', $courseIds)
            ->whereHas('replies', fn($q) => $q->where('user_id', '!=', $teacher->id))
            ->get()
            ->filter(function ($thread) use ($teacher) {
                $lastReply = $thread->replies()->latest()->first();
                return $lastReply && $lastReply->user_id !== $teacher->id;
            })
            ->count();

        // ── Cours pour le menu sidebar forum (publiés avec des threads) ───────
        // Utilisé dans le @foreach de la sidebar : route('teacher.forum.index', $course)
        $coursesWithForum = Course::byTeacher($teacher->id)
            ->where('status', 'published')
            ->whereHas('forumThreads')
            ->get(['id', 'title', 'slug'])
            ->map(function ($course) use ($teacher) {
                $course->unread_count = ForumThread::where('course_id', $course->id)
                    ->whereHas('replies', fn($q) => $q->where('user_id', '!=', $teacher->id))
                    ->get()
                    ->filter(function ($t) use ($teacher) {
                        $last = $t->replies()->latest()->first();
                        return $last && $last->user_id !== $teacher->id;
                    })
                    ->count();
                return $course;
            })
            ->sortByDesc('unread_count')
            ->values();

        // ── Activité récente ──────────────────────────────────────────────────
        $recentActivity = $this->buildActivity($courseIds, $teacher->id);

        return view('teacher.dashboard', compact(
            'teacher',
            'stats',
            'revenues',
            'courses',
            'topCourses',
            'recentStudents',
            'forumThreads',
            'unreadForumCount',
            'coursesWithForum',
            'recentActivity'
        ));
    }

    private function buildActivity($courseIds, int $teacherId): array
    {
        $feed = [];

        foreach (Enrollment::with(['user', 'course'])
            ->whereIn('course_id', $courseIds)
            ->latest()->take(5)->get() as $e) {
            $feed[] = [
                'icon'   => '🎓',
                'color'  => '#25c26e',
                'action' => 'Nouvelle inscription',
                'detail' => ($e->user->full_name ?? '?') . ' → ' . Str::limit($e->course->title ?? '', 25),
                'time'   => $e->created_at,
            ];
        }

        foreach (ForumReply::with(['author', 'thread.course'])
            ->whereHas('thread', fn($q) => $q->whereIn('course_id', $courseIds))
            ->where('user_id', '!=', $teacherId)
            ->latest()->take(5)->get() as $r) {
            $feed[] = [
                'icon'   => '💬',
                'color'  => '#a78bfa',
                'action' => 'Réponse forum',
                'detail' => ($r->author->full_name ?? '?') . ' sur « ' . Str::limit($r->thread->title ?? '', 22) . ' »',
                'time'   => $r->created_at,
            ];
        }

        usort($feed, fn($a, $b) => $b['time'] <=> $a['time']);
        return array_slice($feed, 0, 8);
    }
}