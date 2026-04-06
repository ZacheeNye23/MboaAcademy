<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\UserBadge;
use App\Models\Badge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuizController extends Controller
{
    /** Liste des quiz disponibles pour l'étudiant */
    public function index(): View
    {
        $user = Auth::user();

        $quizzes = Quiz::whereHas('course.enrollments', fn($q) =>
                $q->where('user_id', $user->id))
            ->with(['course', 'questions'])
            ->get()
            ->map(fn($quiz) => [
                'quiz'         => $quiz,
                'attempts'     => $quiz->attemptsForUser($user->id)->count(),
                'can_attempt'  => $quiz->canAttempt($user->id),
                'best_score'   => $quiz->bestScoreForUser($user->id),
                'passed'       => $quiz->attemptsForUser($user->id)->where('passed', true)->exists(),
            ]);

        return view('student.quizzes.index', compact('quizzes'));
    }

    /** Afficher le quiz à passer */
    public function show(Quiz $quiz): View
    {
        $user = Auth::user();

        abort_unless($quiz->canAttempt($user->id), 403, 'Nombre maximum de tentatives atteint.');

        $quiz->load('questions.answers');
        $attemptNumber = $quiz->attemptsForUser($user->id)->count() + 1;

        return view('student.quizzes.show', compact('quiz', 'attemptNumber'));
    }

    /** Soumettre les réponses et calculer le score */
    public function submit(Request $request, Quiz $quiz): RedirectResponse
    {
        $user = Auth::user();

        abort_unless($quiz->canAttempt($user->id), 403);

        $quiz->load('questions.answers');

        $answersGiven  = $request->input('answers', []); // ['question_id' => [answer_id, ...]]
        $totalPoints   = 0;
        $earnedPoints  = 0;
        $details       = [];

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $given        = (array) ($answersGiven[$question->id] ?? []);
            $correct      = $question->correctAnswers()->pluck('id')->toArray();
            $isCorrect    = empty(array_diff($correct, $given)) && empty(array_diff($given, $correct));

            if ($isCorrect) $earnedPoints += $question->points;

            $details[$question->id] = [
                'given'      => $given,
                'correct'    => $correct,
                'is_correct' => $isCorrect,
            ];
        }

        $score  = $totalPoints > 0 ? (int) round(($earnedPoints / $totalPoints) * 100) : 0;
        $passed = $score >= $quiz->passing_score;

        $attempt = QuizAttempt::create([
            'user_id'        => $user->id,
            'quiz_id'        => $quiz->id,
            'score'          => $score,
            'total_points'   => $totalPoints,
            'earned_points'  => $earnedPoints,
            'passed'         => $passed,
            'attempt_number' => $quiz->attemptsForUser($user->id)->count(),
            'answers_given'  => $details,
            'time_spent'     => $request->input('time_spent'),
            'finished_at'    => now(),
        ]);

        // Décerner badge "Quiz Master" si score parfait
        if ($score === 100) {
            $badge = Badge::where('type', 'quiz_master')->first();
            if ($badge) UserBadge::firstOrCreate(['user_id' => $user->id, 'badge_id' => $badge->id]);
        }

        return redirect()->route('student.quizzes.result', $attempt->id)
            ->with('success', $passed ? '🎉 Félicitations, vous avez réussi !' : '💪 Continuez à vous entraîner !');
    }

    /** Résultats d'une tentative */
    public function result(QuizAttempt $attempt): View
    {
        abort_unless($attempt->user_id === Auth::id(), 403);
        $attempt->load('quiz.questions.answers');

        return view('student.quizzes.result', compact('attempt'));
    }
}