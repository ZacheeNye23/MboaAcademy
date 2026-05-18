<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Badge;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\UserBadge;
use App\Services\BadgeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuizController extends Controller
{
    // ── Liste des quiz disponibles ──────────────────────────────────────────
    public function index(): View
    {
        $user = Auth::user();

        $quizzes = Quiz::whereHas('course.enrollments', fn($q) =>
                $q->where('user_id', $user->id))
            ->with(['course', 'questions', 'attempts' => fn($q) => $q->where('user_id', $user->id)])
            ->get()
            ->map(fn($quiz) => [
                'quiz'        => $quiz,
                'attempts'    => $quiz->attempts->count(),
                'can_attempt' => $quiz->canAttempt($user->id),
                'best_score'  => $quiz->bestScoreForUser($user->id),
                'passed'      => $quiz->attempts->where('passed', true)->count() > 0,
            ]);

        return view('student.quizzes.index', compact('quizzes'));
    }

    // ── Afficher un quiz à passer ────────────────────────────────────────────
    public function show(Quiz $quiz): View
    {
        $user = Auth::user();

        // Vérifier l'accès (inscrit au cours)
        abort_unless(
            $quiz->course->enrollments()->where('user_id', $user->id)->exists(),
            403, 'Vous n\'êtes pas inscrit à ce cours.'
        );

        abort_unless($quiz->canAttempt($user->id), 403, 'Nombre maximum de tentatives atteint.');

        $quiz->load('questions.answers');
        // Mélanger les réponses pour éviter la mémorisation
        $quiz->questions->each(fn($q) => $q->setRelation('answers', $q->answers->shuffle()));

        $attemptNumber = $quiz->attemptsForUser($user->id)->count() + 1;

        return view('student.quizzes.show', compact('quiz', 'attemptNumber'));
    }

    // ── Soumettre les réponses ───────────────────────────────────────────────
    public function submit(Request $request, Quiz $quiz): RedirectResponse
    {
        $user = Auth::user();

        abort_unless(
            $quiz->course->enrollments()->where('user_id', $user->id)->exists(),
            403
        );
        abort_unless($quiz->canAttempt($user->id), 403, 'Tentatives épuisées.');

        $quiz->load('questions.answers');

        $answersGiven = $request->input('answers', []);
        $totalPoints  = 0;
        $earnedPoints = 0;
        $details      = [];

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $given        = array_map('intval', (array) ($answersGiven[$question->id] ?? []));
            $correct      = $question->correctAnswers()->pluck('id')->map(fn($id) => (int)$id)->toArray();

            sort($given); sort($correct);
            $isCorrect = $given === $correct;

            if ($isCorrect) $earnedPoints += $question->points;

            $details[$question->id] = [
                'given'      => $given,
                'correct'    => $correct,
                'is_correct' => $isCorrect,
            ];
        }

        $score  = $totalPoints > 0 ? (int) round(($earnedPoints / $totalPoints) * 100) : 0;
        $passed = $score >= $quiz->passing_score;

        $attemptNumber = $quiz->attemptsForUser($user->id)->count() + 1;

        $attempt = QuizAttempt::create([
            'user_id'        => $user->id,
            'quiz_id'        => $quiz->id,
            'score'          => $score,
            'total_points'   => $totalPoints,
            'earned_points'  => $earnedPoints,
            'passed'         => $passed,
            'attempt_number' => $attemptNumber,
            'answers_given'  => $details,
            'time_spent'     => $request->input('time_spent'),
            'finished_at'    => now(),
        ]);

        // Badge "Quiz Master" si score parfait
        if ($score === 100) {
            $badge = Badge::where('type', 'quiz_master')->first();
            if ($badge) UserBadge::firstOrCreate(['user_id' => $user->id, 'badge_id' => $badge->id]);
        }

        // Vérifier les autres badges
        app(BadgeService::class)->checkAndAward($user);

        $msg = $passed
            ? '🎉 Félicitations ! Vous avez réussi le quiz avec ' . $score . '% !'
            : '💪 Score : ' . $score . '%. Le score requis est ' . $quiz->passing_score . '%. Réessayez !';

        return redirect()->route('student.quizzes.result', $attempt->id)->with('success', $msg);
    }

    // ── Résultats d'une tentative ────────────────────────────────────────────
    public function result(QuizAttempt $attempt): View
    {
        abort_unless($attempt->user_id === Auth::id(), 403);
        $attempt->load('quiz.questions.answers');

        return view('student.quizzes.result', compact('attempt'));
    }
}