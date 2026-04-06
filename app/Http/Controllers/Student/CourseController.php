<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\UserStreak;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourseController extends Controller
{
    /** Liste de tous les cours publiés (catalogue) */
    public function index(Request $request): View
    {
        $query = Course::published()->with('teacher');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $courses     = $query->paginate(12);
        $categories  = Course::published()->distinct()->pluck('category')->filter();
        $enrolledIds = Enrollment::where('user_id', Auth::id())->pluck('course_id');

        return view('student.courses.index', compact('courses', 'categories', 'enrolledIds'));
    }

    /** Détail d'un cours avant inscription */
    public function show(string $slug): View
    {
        $course = Course::published()
            ->with(['teacher', 'chapters.lessons', 'quizzes'])
            ->where('slug', $slug)
            ->firstOrFail();

        $isEnrolled = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->exists();

        $enrollment = $isEnrolled
            ? Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->first()
            : null;

        return view('student.courses.show', compact('course', 'isEnrolled', 'enrollment'));
    }

    /** S'inscrire à un cours */
    public function enroll(Course $course): RedirectResponse
    {
        $user = Auth::user();

        // Déjà inscrit ?
        if (Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
            return redirect()->route('student.courses.learn', $course->slug)
                ->with('info', 'Vous êtes déjà inscrit à ce cours.');
        }

        // Cours payant ? (à gérer avec paiement plus tard)
        if (!$course->is_free && $course->price > 0) {
            return redirect()->route('student.courses.checkout', $course->slug);
        }

        Enrollment::create([
            'user_id'     => $user->id,
            'course_id'   => $course->id,
            'enrolled_at' => now(),
        ]);

        return redirect()->route('student.courses.learn', $course->slug)
            ->with('success', "Inscription réussie ! Bonne formation 🎉");
    }

    /** Interface d'apprentissage (lecteur vidéo + leçons) */
    public function learn(string $slug, Request $request): View
    {
        $user   = Auth::user();
        $course = Course::published()
            ->with(['chapters.lessons.resources', 'chapters.lessons.quizzes'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Vérifier l'inscription
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Leçon courante
        $lessonId      = $request->get('lesson');
        $currentLesson = $lessonId
            ? $course->lessons()->where('id', $lessonId)->firstOrFail()
            : $course->chapters->first()?->lessons->first();

        // Progression des leçons
        $completedLessonIds = LessonProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->pluck('lesson_id');

        return view('student.courses.learn', compact(
            'course', 'enrollment', 'currentLesson', 'completedLessonIds'
        ));
    }

    /** Marquer une leçon comme terminée (appelé en AJAX ou form POST) */
    public function completeLesson(Request $request, int $lessonId): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        $progress = LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lessonId],
            ['is_completed' => true, 'completed_at' => now(), 'watch_time' => $request->watch_time ?? 0]
        );

        // Recalculer la progression du cours
        $lesson     = \App\Models\Lesson::findOrFail($lessonId);
        $courseId   = $lesson->chapter->course_id;
        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $courseId)->first();
        $newPercent = $enrollment?->recalculateProgress() ?? 0;

        // Mettre à jour le streak
        $streak = UserStreak::firstOrCreate(['user_id' => $user->id]);
        $streak->updateStreak();

        return response()->json([
            'success'  => true,
            'progress' => $newPercent,
            'streak'   => $streak->current_streak,
        ]);
    }

    /** Mes cours (inscrits) */
    public function myCourses(): View
    {
        $enrollments = Enrollment::with(['course.teacher', 'course.chapters.lessons'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(9);

        return view('student.courses.my-courses', compact('enrollments'));
    }
}