<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuizController extends Controller
{
    // ── Liste tous les quiz ─────────────────────────────────────────────────
    public function index(Request $request): View
    {
        $query = Quiz::with(['course.teacher', 'lesson'])
            ->withCount(['questions', 'attempts'])
            ->withAvg('attempts', 'score')
            ->addSelect([
                'pass_rate' => QuizAttempt::selectRaw(
                    'ROUND(SUM(passed) / COUNT(*) * 100)'
                )->whereColumn('quiz_id', 'quizzes.id'),
            ]);

        // Filtres
        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('title', 'like', '%'.$request->search.'%')
                  ->orWhereHas('course', fn($c) => $c->where('title', 'like', '%'.$request->search.'%'))
            );
        }

        if ($request->filled('type')) {
            match($request->type) {
                'course' => $query->whereNull('lesson_id')->whereNotNull('course_id'),
                'lesson' => $query->whereNotNull('lesson_id'),
                default  => null,
            };
        }

        if ($request->filled('difficulty')) {
            match($request->difficulty) {
                'easy'   => $query->where('passing_score', '<', 60),
                'medium' => $query->whereBetween('passing_score', [60, 79]),
                'hard'   => $query->where('passing_score', '>=', 80),
                default  => null,
            };
        }

        // Tri
        match($request->get('sort', 'latest')) {
            'attempts'  => $query->orderByDesc('attempts_count'),
            'pass_rate' => $query->orderByDesc('pass_rate'),
            'score'     => $query->orderByDesc('attempts_avg_score'),
            default     => $query->latest(),
        };

        $quizzes = $query->paginate(12)->withQueryString();

        // Calculer pass_rate et avg_score dynamiquement
        $quizzes->getCollection()->transform(function ($quiz) {
            $attempts         = QuizAttempt::where('quiz_id', $quiz->id);
            $quiz->pass_rate  = $attempts->count() > 0
                ? round($attempts->where('passed', true)->count() / $attempts->count() * 100)
                : 0;
            $quiz->avg_score  = (int) ($attempts->avg('score') ?? 0);
            return $quiz;
        });

        // Stats globales
        $globalStats = [
            'total'          => Quiz::count(),
            'total_attempts' => QuizAttempt::count(),
            'pass_rate'      => QuizAttempt::count() > 0
                ? round(QuizAttempt::where('passed', true)->count() / QuizAttempt::count() * 100)
                : 0,
            'avg_score'      => (int) QuizAttempt::avg('score'),
        ];

        return view('admin.quizzes.index', compact('quizzes', 'globalStats'));
    }

    // ── Détail d'un quiz ────────────────────────────────────────────────────
    public function show(Quiz $quiz): View
    {
        $quiz->load(['course.teacher', 'lesson', 'questions.answers']);

        // Stats globales du quiz
        $totalAttempts = QuizAttempt::where('quiz_id', $quiz->id)->count();
        $passedCount   = QuizAttempt::where('quiz_id', $quiz->id)->where('passed', true)->count();
        $avgScore      = (int) QuizAttempt::where('quiz_id', $quiz->id)->avg('score');
        $bestScore     = (int) QuizAttempt::where('quiz_id', $quiz->id)->max('score');
        $passRate      = $totalAttempts > 0 ? round($passedCount / $totalAttempts * 100) : 0;

        // Distribution des scores
        $scoreDist = [
            'excellent' => QuizAttempt::where('quiz_id', $quiz->id)->where('score', '>=', 90)->count(),
            'good'      => QuizAttempt::where('quiz_id', $quiz->id)->whereBetween('score', [70, 89])->count(),
            'average'   => QuizAttempt::where('quiz_id', $quiz->id)->whereBetween('score', [50, 69])->count(),
            'poor'      => QuizAttempt::where('quiz_id', $quiz->id)->where('score', '<', 50)->count(),
        ];

        $stats = [
            'total_attempts' => $totalAttempts,
            'pass_rate'      => $passRate,
            'avg_score'      => $avgScore,
            'best_score'     => $bestScore,
            'score_dist'     => $scoreDist,
        ];

        // Stats par question (taux de bonne réponse)
        $questionStats = [];
        foreach ($quiz->questions as $question) {
            $attempts = QuizAttempt::where('quiz_id', $quiz->id)
                ->whereNotNull('answers_given')
                ->get();

            $total   = $attempts->count();
            $correct = 0;

            foreach ($attempts as $attempt) {
                $given = $attempt->answers_given[$question->id] ?? null;
                if ($given && ($given['is_correct'] ?? false)) {
                    $correct++;
                }
            }

            $questionStats[$question->id] = [
                'total'   => $total,
                'correct' => $correct,
                'rate'    => $total > 0 ? round($correct / $total * 100) : 0,
            ];
        }

        // Dernières tentatives
        $recentAttempts = QuizAttempt::with('user')
            ->where('quiz_id', $quiz->id)
            ->latest()
            ->take(15)
            ->get();

        return view('admin.quizzes.show', compact(
            'quiz', 'stats', 'questionStats', 'recentAttempts'
        ));
    }

    // ── Supprimer un quiz ───────────────────────────────────────────────────
    public function destroy(Quiz $quiz): RedirectResponse
    {
        $title = $quiz->title;
        $quiz->delete(); // cascade sur questions, answers, attempts

        return redirect()->route('admin.quizzes.index')
            ->with('success', "Quiz \"{$title}\" supprimé avec toutes ses tentatives.");
    }
}