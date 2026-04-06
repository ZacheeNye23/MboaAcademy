<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuizStatsController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    //  PAGE STATS D'UN QUIZ SPÉCIFIQUE
    // ─────────────────────────────────────────────────────────────────────────
    public function show(Quiz $quiz): View
    {
        $this->authorizeTeacher($quiz);

        $quiz->load([
            'questions.answers',
            'course',
            'attempts' => fn($q) => $q->with('user')->latest(),
        ]);

        // ── Stats globales ────────────────────────────────────────────────────
        $attempts     = $quiz->attempts;
        $total        = $attempts->count();
        $passed       = $attempts->where('passed', true)->count();
        $failed       = $total - $passed;

        $globalStats = [
            'total_attempts'   => $total,
            'unique_students'  => $attempts->pluck('user_id')->unique()->count(),
            'passed'           => $passed,
            'failed'           => $failed,
            'pass_rate'        => $total > 0 ? round($passed / $total * 100) : 0,
            'avg_score'        => $total > 0 ? round($attempts->avg('score'), 1) : 0,
            'best_score'       => $total > 0 ? $attempts->max('score') : 0,
            'worst_score'      => $total > 0 ? $attempts->min('score') : 0,
            'avg_time_seconds' => $total > 0 ? round($attempts->whereNotNull('time_spent')->avg('time_spent')) : 0,
            'total_points'     => $quiz->questions->sum('points'),
        ];

        // ── Distribution des scores par tranches de 10% ───────────────────────
        $distribution = collect(range(0, 9))->map(function ($i) use ($attempts, $total) {
            $min   = $i * 10;
            $max   = $i === 9 ? 100 : $min + 9;
            $count = $attempts->filter(fn($a) => $a->score >= $min && $a->score <= $max)->count();
            return [
                'range' => $min . '–' . $max . '%',
                'min'   => $min,
                'max'   => $max,
                'count' => $count,
                'pct'   => $total > 0 ? round($count / $total * 100) : 0,
            ];
        });

        // ── Évolution des scores dans le temps (7 derniers jours) ────────────
        $scoreEvolution = QuizAttempt::where('quiz_id', $quiz->id)
            ->whereNotNull('finished_at')
            ->where('finished_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(finished_at) as date'),
                DB::raw('AVG(score) as avg_score'),
                DB::raw('COUNT(*) as attempts')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($r) => [
                'date'       => $r->date,
                'avg_score'  => round($r->avg_score, 1),
                'attempts'   => $r->attempts,
            ]);

        // ── Analyse par question ──────────────────────────────────────────────
        $questionStats = $quiz->questions->map(function ($question) use ($attempts, $total) {
            $correctCount = 0;
            $answerCounts = []; // combien de fois chaque réponse a été choisie

            foreach ($attempts as $attempt) {
                $given = $attempt->answers_given[$question->id] ?? null;
                if (!$given) continue;

                // Vérifier si la réponse était correcte
                if ($given['is_correct'] ?? false) $correctCount++;

                // Compter les réponses choisies
                $chosenIds = (array) ($given['given'] ?? []);
                foreach ($chosenIds as $id) {
                    $answerCounts[$id] = ($answerCounts[$id] ?? 0) + 1;
                }
            }

            $attempted    = $attempts->filter(fn($a) => isset($a->answers_given[$question->id]))->count();
            $correctRate  = $attempted > 0 ? round($correctCount / $attempted * 100) : 0;

            return [
                'id'            => $question->id,
                'question'      => $question->question,
                'type'          => $question->type,
                'points'        => $question->points,
                'correct_rate'  => $correctRate,
                'attempted'     => $attempted,
                'correct_count' => $correctCount,
                'answer_counts' => $answerCounts,
                'answers'       => $question->answers->map(fn($a) => [
                    'id'         => $a->id,
                    'text'       => $a->answer_text,
                    'is_correct' => $a->is_correct,
                    'count'      => $answerCounts[$a->id] ?? 0,
                    'pct'        => $attempted > 0 ? round(($answerCounts[$a->id] ?? 0) / $attempted * 100) : 0,
                ]),
            ];
        })->sortBy('correct_rate'); // les plus ratées en premier

        // ── Meilleurs et pires résultats par apprenant ────────────────────────
        $studentStats = $attempts
            ->groupBy('user_id')
            ->map(function ($userAttempts) {
                $user = $userAttempts->first()->user;
                return [
                    'user'          => $user,
                    'attempts'      => $userAttempts->count(),
                    'best_score'    => $userAttempts->max('score'),
                    'avg_score'     => round($userAttempts->avg('score'), 1),
                    'passed'        => $userAttempts->where('passed', true)->count() > 0,
                    'last_attempt'  => $userAttempts->sortByDesc('created_at')->first()->created_at,
                ];
            })
            ->sortByDesc('best_score')
            ->values();

        // ── Tentatives récentes ───────────────────────────────────────────────
        $recentAttempts = $attempts->take(10);

        return view('teacher.quizzes.stats', compact(
            'quiz', 'globalStats', 'distribution',
            'scoreEvolution', 'questionStats',
            'studentStats', 'recentAttempts'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  VUE D'ENSEMBLE STATS DE TOUS LES QUIZ
    // ─────────────────────────────────────────────────────────────────────────
    public function overview(): View
    {
        $teacher   = Auth::user();
        $courseIds = Course::byTeacher($teacher->id)->pluck('id');
        $quizIds   = Quiz::whereIn('course_id', $courseIds)->pluck('id');

        $overallStats = [
            'total_quizzes'   => $quizIds->count(),
            'total_attempts'  => QuizAttempt::whereIn('quiz_id', $quizIds)->count(),
            'overall_pass_rate' => $this->computePassRate($quizIds),
            'overall_avg'     => round(QuizAttempt::whereIn('quiz_id', $quizIds)->avg('score') ?? 0, 1),
        ];

        $quizSummaries = Quiz::whereIn('course_id', $courseIds)
            ->withCount('attempts')
            ->withCount('questions')
            ->with('course:id,title')
            ->get()
            ->map(fn($q) => [
                'quiz'         => $q,
                'pass_rate'    => $this->computePassRate([$q->id]),
                'avg_score'    => round(QuizAttempt::where('quiz_id', $q->id)->avg('score') ?? 0, 1),
            ]);

        return view('teacher.quizzes.stats-overview', compact('overallStats', 'quizSummaries'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function authorizeTeacher(Quiz $quiz): void
    {
        abort_unless($quiz->course->user_id === Auth::id(), 403);
    }

    private function computePassRate($quizIds): int
    {
        $total  = QuizAttempt::whereIn('quiz_id', $quizIds)->count();
        $passed = QuizAttempt::whereIn('quiz_id', $quizIds)->where('passed', true)->count();
        return $total > 0 ? round($passed / $total * 100) : 0;
    }

    private function formatTime(int $seconds): string
    {
        if ($seconds < 60) return $seconds . 's';
        $m = intdiv($seconds, 60);
        $s = $seconds % 60;
        return $s > 0 ? "{$m}min {$s}s" : "{$m}min";
    }
}