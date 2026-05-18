<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\UserStreak;
use App\Services\BadgeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourseController extends Controller
{
    // ── Catalogue ───────────────────────────────────────────────────────────
    public function index(Request $request): View
    {
        $query = Course::published()->with(['teacher', 'reviews'])->withCount('enrollments');

        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('title', 'like', '%'.$request->search.'%')
                  ->orWhere('description', 'like', '%'.$request->search.'%')
            );
        }
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('level'))    $query->where('level', $request->level);
        if ($request->filled('free'))     $query->where('is_free', true);

        $sort = $request->get('sort', 'latest');
        match($sort) {
            'popular'   => $query->orderByDesc('enrollments_count'),
            'price_asc' => $query->orderBy('price'),
            'rating'    => $query->orderByDesc('id'), // à remplacer par avg rating
            default     => $query->latest(),
        };

        $courses    = $query->paginate(12);
        $categories = Course::published()->distinct()->pluck('category')->filter();
        $enrolledIds = Enrollment::where('user_id', Auth::id())->pluck('course_id');

        return view('student.courses.index', compact('courses', 'categories', 'enrolledIds'));
    }

    // ── Détail cours ─────────────────────────────────────────────────────────
    public function show(string $slug): View
    {
        $course = Course::published()
            ->with(['teacher', 'chapters.lessons', 'quizzes', 'reviews.student'])
            ->where('slug', $slug)
            ->firstOrFail();

        $isEnrolled = Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->exists();
        $enrollment = $isEnrolled
            ? Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->first()
            : null;

        return view('student.courses.show', compact('course', 'isEnrolled', 'enrollment'));
    }

    // ── Inscription ──────────────────────────────────────────────────────────
   public function enroll(Course $course): RedirectResponse
{
    $user = Auth::user();

    if (Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
        return redirect()->route('student.courses.learn', $course->slug)
            ->with('info', 'Vous êtes déjà inscrit à ce cours.');
    }

 

    Enrollment::create([
        'user_id'     => $user->id,
        'course_id'   => $course->id,
        'enrolled_at' => now(),
    ]);

    app(BadgeService::class)->checkAndAward($user);

    $message = $course->is_free
        ? '🎉 Inscription réussie ! Bonne formation.'
        : '🎉 Inscription réussie ! (Mode démo — paiement Mobile Money bientôt disponible)';

    return redirect()->route('student.courses.learn', $course->slug)
        ->with('success', $message);
}

    // ── Lecteur de leçon ─────────────────────────────────────────────────────
    public function learn(string $slug, Request $request): View
    {
        $user   = Auth::user();
        $course = Course::published()
            ->with(['chapters.lessons.resources', 'chapters.lessons.quizzes.questions'])
            ->where('slug', $slug)
            ->firstOrFail();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Leçon courante
        $lessonId      = $request->get('lesson');
        $currentLesson = $lessonId
            ? $course->chapters->flatMap->lessons->firstWhere('id', $lessonId)
            : $course->chapters->first()?->lessons->first();

        // IDs des leçons complétées
        $completedLessonIds = LessonProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->whereIn('lesson_id', $course->chapters->flatMap->lessons->pluck('id'))
            ->pluck('lesson_id');

        return view('student.courses.learn', compact(
            'course', 'enrollment', 'currentLesson', 'completedLessonIds'
        ));
    }

    // ── Marquer leçon complétée (AJAX) ───────────────────────────────────────
    public function completeLesson(Request $request, int $lessonId): \Illuminate\Http\JsonResponse
    {
        $user   = Auth::user();
        $lesson = \App\Models\Lesson::with('chapter.course')->findOrFail($lessonId);
        $course = $lesson->chapter->course;

        // Vérifier l'inscription
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lessonId],
            ['is_completed' => true, 'completed_at' => now(), 'watch_time' => $request->watch_time ?? 0]
        );

        // Recalculer progression
        $newPercent = $enrollment->recalculateProgress();

        // Streak
        $streak = UserStreak::firstOrCreate(['user_id' => $user->id]);
        $streak->updateStreak();

        // Badges
        $newBadges = app(BadgeService::class)->checkAndAward($user);

        return response()->json([
            'success'    => true,
            'progress'   => $newPercent,
            'streak'     => $streak->current_streak,
            'new_badges' => $newBadges,
        ]);
    }

    // ── Mes cours inscrits ────────────────────────────────────────────────────
    public function myCourses(Request $request): View
    {
        $query = Enrollment::with(['course.teacher', 'course.chapters.lessons'])
            ->where('user_id', Auth::id());

        match($request->get('status')) {
            'completed' => $query->whereNotNull('completed_at'),
            'ongoing'   => $query->whereNull('completed_at'),
            default     => null,
        };

        $enrollments = $query->latest()->paginate(9);
        return view('student.courses.my-courses', compact('enrollments'));
    }
}