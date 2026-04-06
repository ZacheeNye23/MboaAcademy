<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ForumThread;
use App\Models\RevenueRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $teacher = Auth::user();

        // Cours du formateur
        $courses = Course::byTeacher($teacher->id)
            ->withCount('enrollments')
            ->with(['reviews', 'chapters.lessons'])
            ->latest()
            ->get();

        $courseIds = $courses->pluck('id');

        // Stats globales
        $stats = [
            'total_students' => Enrollment::whereIn('course_id', $courseIds)->distinct('user_id')->count('user_id'),
            'total_courses'  => $courses->count(),
            'published'      => $courses->where('status', 'published')->count(),
            'drafts'         => $courses->where('status', 'draft')->count(),
            'pending'        => $courses->where('status', 'pending')->count(),
            'avg_rating'     => round($courses->flatMap->reviews->avg('rating') ?? 0, 1),
            'total_reviews'  => $courses->flatMap->reviews->count(),
        ];

        // Revenus
        $revenues = [
            'this_month' => RevenueRecord::where('teacher_id', $teacher->id)->completed()->thisMonth()->sum('net_amount'),
            'last_month' => RevenueRecord::where('teacher_id', $teacher->id)->completed()
                ->whereMonth('paid_at', now()->subMonth()->month)->whereYear('paid_at', now()->subMonth()->year)
                ->sum('net_amount'),
            'total'      => RevenueRecord::where('teacher_id', $teacher->id)->completed()->sum('net_amount'),
            'monthly'    => RevenueRecord::where('teacher_id', $teacher->id)->completed()
                ->whereYear('paid_at', now()->year)
                ->selectRaw('MONTH(paid_at) as month, SUM(net_amount) as total')
                ->groupBy('month')->orderBy('month')
                ->pluck('total', 'month')->toArray(),
        ];

        $revenues['variation'] = $revenues['last_month'] > 0
            ? round((($revenues['this_month'] - $revenues['last_month']) / $revenues['last_month']) * 100, 1)
            : 0;

        // Apprenants récents
        $recentStudents = Enrollment::with(['user', 'course'])
            ->whereIn('course_id', $courseIds)->latest()->take(8)->get();

        // Forum
        $forumThreads = ForumThread::with(['author', 'replies', 'course'])
            ->whereIn('course_id', $courseIds)->latest()->take(5)->get();

        $unreadForumCount = $forumThreads->filter(fn($t) =>
            $t->replies->where('user_id', '!=', $teacher->id)->count() > 0
        )->count();

        // Top cours
        $topCourses = $courses->sortByDesc('enrollments_count')->take(3);

        // Activité récente
        $recentActivity = $this->getRecentActivity($teacher->id, $courseIds);

        return view('teacher.dashboard', compact(
            'teacher', 'courses', 'stats', 'revenues',
            'recentStudents', 'forumThreads', 'unreadForumCount',
            'topCourses', 'recentActivity'
        ));
    }

    private function getRecentActivity($teacherId, $courseIds): array
    {
        $activities = [];

        Enrollment::with(['user', 'course'])->whereIn('course_id', $courseIds)->latest()->take(5)->get()
            ->each(fn($e) => $activities[] = [
                'icon'   => '🆕', 'action' => 'Nouvelle inscription',
                'detail' => ($e->user->full_name ?? '—').' → '.($e->course->title ?? '—'),
                'time'   => $e->created_at, 'color' => '#25c26e',
            ]);

        Enrollment::with(['user', 'course'])->whereIn('course_id', $courseIds)
            ->whereNotNull('completed_at')->latest('completed_at')->take(3)->get()
            ->each(fn($e) => $activities[] = [
                'icon'   => '🎓', 'action' => 'Cours terminé',
                'detail' => ($e->user->full_name ?? '—').' a terminé '.($e->course->title ?? '—'),
                'time'   => $e->completed_at, 'color' => '#3b82f6',
            ]);

        \App\Models\CourseReview::with(['student','course'])->whereIn('course_id', $courseIds)->latest()->take(3)->get()
            ->each(fn($r) => $activities[] = [
                'icon'   => '⭐', 'action' => "Avis {$r->rating}/5",
                'detail' => ($r->student->full_name ?? '—').' sur '.($r->course->title ?? '—'),
                'time'   => $r->created_at, 'color' => '#e8b84b',
            ]);

        RevenueRecord::where('teacher_id', $teacherId)->latest('paid_at')->take(3)->get()
            ->each(fn($r) => $activities[] = [
                'icon'   => '💰', 'action' => 'Paiement reçu',
                'detail' => $r->net_amount_formatted,
                'time'   => $r->paid_at, 'color' => '#e8b84b',
            ]);

        usort($activities, fn($a, $b) => $b['time'] <=> $a['time']);
        return array_slice($activities, 0, 10);
    }
}