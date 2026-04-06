<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    //  LISTE DES APPRENANTS
    // ─────────────────────────────────────────────────────────────────────────
    public function index(Request $request): View
    {
        $teacher   = Auth::user();
        $courseIds = Course::byTeacher($teacher->id)->pluck('id');

        // ── Filtres ───────────────────────────────────────────────────────────
        $courseFilter  = $request->get('course', 'all');
        $statusFilter  = $request->get('status', 'all'); // all | completed | in_progress | not_started
        $search        = $request->get('search', '');
        $sortBy        = $request->get('sort', 'enrolled_at');
        $sortDir       = $request->get('dir', 'desc');

        // ── Requête principale ────────────────────────────────────────────────
        $query = Enrollment::with(['user', 'course'])
            ->whereIn('course_id', $courseIds);

        // Filtre cours
        if ($courseFilter !== 'all') {
            $query->where('course_id', $courseFilter);
        }

        // Filtre statut
        match ($statusFilter) {
            'completed'   => $query->whereNotNull('completed_at'),
            'in_progress' => $query->whereNull('completed_at')->where('progress_percent', '>', 0),
            'not_started' => $query->where('progress_percent', 0),
            default       => null,
        };

        // Recherche
        if ($search) {
            $query->whereHas('user', fn($q) =>
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name',  'like', "%{$search}%")
                  ->orWhere('email',      'like', "%{$search}%")
            );
        }

        // Tri
        $allowedSorts = ['enrolled_at', 'progress_percent', 'completed_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $enrollments = $query->paginate(15)->withQueryString();

        // ── Stats globales ────────────────────────────────────────────────────
        $allEnrollments = Enrollment::whereIn('course_id', $courseIds);

        $globalStats = [
            'total'        => $allEnrollments->count(),
            'unique'       => $allEnrollments->distinct('user_id')->count('user_id'),
            'completed'    => (clone $allEnrollments)->whereNotNull('completed_at')->count(),
            'in_progress'  => (clone $allEnrollments)->whereNull('completed_at')->where('progress_percent', '>', 0)->count(),
            'not_started'  => (clone $allEnrollments)->where('progress_percent', 0)->count(),
            'avg_progress' => (int) $allEnrollments->avg('progress_percent'),
        ];

        // ── Cours pour le filtre ──────────────────────────────────────────────
        $courses = Course::byTeacher($teacher->id)
            ->select('id', 'title', 'status')
            ->withCount('enrollments')
            ->get();

        return view('teacher.students.index', compact(
            'enrollments', 'globalStats', 'courses',
            'courseFilter', 'statusFilter', 'search',
            'sortBy', 'sortDir'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  FICHE DÉTAIL D'UN APPRENANT
    // ─────────────────────────────────────────────────────────────────────────
    public function show(Enrollment $enrollment): View
    {
        $courseIds = Course::byTeacher(Auth::id())->pluck('id');
        abort_unless($courseIds->contains($enrollment->course_id), 403);

        $enrollment->load(['user', 'course.chapters.lessons.resources']);

        // Progression par leçon
        $completedLessonIds = LessonProgress::where('user_id', $enrollment->user_id)
            ->where('is_completed', true)
            ->whereIn('lesson_id', $enrollment->course->lessons->pluck('id'))
            ->pluck('lesson_id');

        // Tentatives quiz
        $quizAttempts = QuizAttempt::with('quiz')
            ->where('user_id', $enrollment->user_id)
            ->whereHas('quiz', fn($q) => $q->where('course_id', $enrollment->course_id))
            ->latest()
            ->get();

        // Toutes les inscriptions de cet apprenant dans les cours du formateur
        $otherEnrollments = Enrollment::with('course')
            ->where('user_id', $enrollment->user_id)
            ->whereIn('course_id', $courseIds)
            ->where('id', '!=', $enrollment->id)
            ->get();

        return view('teacher.students.show', compact(
            'enrollment', 'completedLessonIds', 'quizAttempts', 'otherEnrollments'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  EXPORT CSV
    // ─────────────────────────────────────────────────────────────────────────
    public function export(Request $request): Response
    {
        $teacher   = Auth::user();
        $courseIds = Course::byTeacher($teacher->id)->pluck('id');

        $courseFilter = $request->get('course', 'all');
        $statusFilter = $request->get('status', 'all');

        $query = Enrollment::with(['user', 'course'])
            ->whereIn('course_id', $courseIds);

        if ($courseFilter !== 'all') $query->where('course_id', $courseFilter);

        match ($statusFilter) {
            'completed'   => $query->whereNotNull('completed_at'),
            'in_progress' => $query->whereNull('completed_at')->where('progress_percent', '>', 0),
            'not_started' => $query->where('progress_percent', 0),
            default       => null,
        };

        $enrollments = $query->latest()->get();

        // Construire le CSV
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="apprenants_' . now()->format('Y-m-d') . '.csv"',
        ];

        $rows   = [];
        $rows[] = ['Prénom', 'Nom', 'Email', 'Cours', 'Progression (%)', 'Inscrit le', 'Terminé le', 'Statut'];

        foreach ($enrollments as $e) {
            $rows[] = [
                $e->user->first_name ?? '',
                $e->user->last_name  ?? '',
                $e->user->email      ?? '',
                $e->course->title    ?? '',
                $e->progress_percent,
                $e->enrolled_at ? $e->enrolled_at->format('d/m/Y') : '',
                $e->completed_at ? $e->completed_at->format('d/m/Y') : '',
                $e->completed_at ? 'Terminé' : ($e->progress_percent > 0 ? 'En cours' : 'Non commencé'),
            ];
        }

        $csv = '';
        $csv .= "\xEF\xBB\xBF"; // BOM UTF-8 pour Excel
        foreach ($rows as $row) {
            $csv .= implode(';', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\r\n";
        }

        return response($csv, 200, $headers);
    }
}