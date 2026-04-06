<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreQuizRequest;
use App\Http\Requests\Teacher\StoreQuestionRequest;
use App\Models\Answer;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuizController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    //  LISTE DES QUIZ
    // ─────────────────────────────────────────────────────────────────────────
    public function index(): View
    {
        $teacher = Auth::user();

        // Cours du formateur avec leurs quiz
        $courses = Course::byTeacher($teacher->id)
            ->with(['quizzes' => fn($q) => $q
                ->withCount('questions')
                ->withCount('attempts')
                ->with('lesson')
                ->latest()])
            ->whereHas('quizzes')
            ->orWhere('user_id', $teacher->id) // inclure cours sans quiz
            ->get();

        // Stats globales quiz
        $quizIds    = $courses->flatMap->quizzes->pluck('id');
        $globalStats = [
            'total'        => $courses->flatMap->quizzes->count(),
            'total_attempts'=> QuizAttempt::whereIn('quiz_id', $quizIds)->count(),
            'avg_score'    => round(QuizAttempt::whereIn('quiz_id', $quizIds)->avg('score') ?? 0, 1),
            'pass_rate'    => $this->globalPassRate($quizIds),
        ];

        // Cours disponibles pour créer un quiz (dropdown)
        $availableCourses = Course::byTeacher($teacher->id)
            ->select('id', 'title', 'status')
            ->latest()
            ->get();

        return view('teacher.quizzes.index', compact('courses', 'globalStats', 'availableCourses'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  CRÉER UN QUIZ
    // ─────────────────────────────────────────────────────────────────────────
    public function store(StoreQuizRequest $request): RedirectResponse
    {
        // Vérifier que le cours appartient au formateur
        $course = Course::findOrFail($request->course_id);
        abort_unless($course->user_id === Auth::id(), 403);

        $quiz = Quiz::create([
            'course_id'        => $request->course_id,
            'lesson_id'        => $request->lesson_id ?: null,
            'title'            => $request->title,
            'description'      => $request->description,
            'passing_score'    => $request->passing_score,
            'max_attempts'     => $request->max_attempts,
            'duration_minutes' => $request->duration_minutes ?: null,
            'show_answers'     => $request->boolean('show_answers'),
        ]);

        return redirect()
            ->route('teacher.quizzes.edit', $quiz)
            ->with('success', 'Quiz créé ! Ajoutez maintenant vos questions.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  ÉDITER UN QUIZ (page principale avec questions)
    // ─────────────────────────────────────────────────────────────────────────
    public function edit(Quiz $quiz): View
    {
        $this->authorizeTeacher($quiz);

        $quiz->load(['questions' => fn($q) => $q->orderBy('order')->with('answers')]);

        // Leçons du cours pour le sélecteur
        $lessons = Lesson::whereHas('chapter', fn($q) => $q->where('course_id', $quiz->course_id))
            ->orderBy('order')
            ->get(['id', 'title']);

        // Stats de ce quiz
        $stats = $this->quizStats($quiz);

        return view('teacher.quizzes.edit', compact('quiz', 'lessons', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  METTRE À JOUR LES PARAMÈTRES DU QUIZ
    // ─────────────────────────────────────────────────────────────────────────
    public function update(StoreQuizRequest $request, Quiz $quiz): RedirectResponse
    {
        $this->authorizeTeacher($quiz);

        $quiz->update([
            'title'            => $request->title,
            'description'      => $request->description,
            'lesson_id'        => $request->lesson_id ?: null,
            'passing_score'    => $request->passing_score,
            'max_attempts'     => $request->max_attempts,
            'duration_minutes' => $request->duration_minutes ?: null,
            'show_answers'     => $request->boolean('show_answers'),
        ]);

        return back()->with('success', 'Paramètres du quiz mis à jour !');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  SUPPRIMER UN QUIZ
    // ─────────────────────────────────────────────────────────────────────────
    public function destroy(Quiz $quiz): RedirectResponse
    {
        $this->authorizeTeacher($quiz);
        $quiz->delete();

        return redirect()
            ->route('teacher.quizzes.index')
            ->with('success', 'Quiz supprimé.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  AJOUTER UNE QUESTION (AJAX → JSON)
    // ─────────────────────────────────────────────────────────────────────────
    public function addQuestion(StoreQuestionRequest $request, Quiz $quiz): JsonResponse
    {
        $this->authorizeTeacher($quiz);

        DB::transaction(function () use ($request, $quiz, &$question) {
            $question = $quiz->questions()->create([
                'question'    => $request->question,
                'type'        => $request->type,
                'explanation' => $request->explanation,
                'points'      => $request->points,
                'order'       => $quiz->questions()->count(),
            ]);

            foreach ($request->answers as $i => $ans) {
                $question->answers()->create([
                    'answer_text' => $ans['text'],
                    'is_correct'  => !empty($ans['correct']),
                    'order'       => $i,
                ]);
            }
        });

        $question->load('answers');

        return response()->json([
            'success'  => true,
            'question' => [
                'id'          => $question->id,
                'question'    => $question->question,
                'type'        => $question->type,
                'type_label'  => $this->typeLabel($question->type),
                'explanation' => $question->explanation,
                'points'      => $question->points,
                'order'       => $question->order,
                'answers'     => $question->answers->map(fn($a) => [
                    'id'          => $a->id,
                    'answer_text' => $a->answer_text,
                    'is_correct'  => $a->is_correct,
                    'order'       => $a->order,
                ]),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  METTRE À JOUR UNE QUESTION (AJAX)
    // ─────────────────────────────────────────────────────────────────────────
    public function updateQuestion(StoreQuestionRequest $request, Question $question): JsonResponse
    {
        $this->authorizeTeacher($question->quiz);

        DB::transaction(function () use ($request, $question) {
            $question->update([
                'question'    => $request->question,
                'type'        => $request->type,
                'explanation' => $request->explanation,
                'points'      => $request->points,
            ]);

            // Supprimer et recréer les réponses
            $question->answers()->delete();
            foreach ($request->answers as $i => $ans) {
                $question->answers()->create([
                    'answer_text' => $ans['text'],
                    'is_correct'  => !empty($ans['correct']),
                    'order'       => $i,
                ]);
            }
        });

        $question->load('answers');

        return response()->json(['success' => true, 'question' => $question]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  SUPPRIMER UNE QUESTION (AJAX)
    // ─────────────────────────────────────────────────────────────────────────
    public function destroyQuestion(Question $question): JsonResponse
    {
        $this->authorizeTeacher($question->quiz);
        $question->delete();

        // Réordonner les questions restantes
        $question->quiz->questions()->orderBy('order')->get()
            ->each(fn($q, $i) => $q->update(['order' => $i]));

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  RÉORDONNER LES QUESTIONS (AJAX drag & drop)
    // ─────────────────────────────────────────────────────────────────────────
    public function reorderQuestions(Request $request, Quiz $quiz): JsonResponse
    {
        $this->authorizeTeacher($quiz);
        $request->validate(['order' => ['required', 'array']]);

        foreach ($request->order as $index => $questionId) {
            Question::where('id', $questionId)
                    ->where('quiz_id', $quiz->id)
                    ->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  STATISTIQUES D'UN QUIZ
    // ─────────────────────────────────────────────────────────────────────────
    public function stats(Quiz $quiz): View
    {
        $this->authorizeTeacher($quiz);
        $quiz->load(['attempts.user', 'questions.answers']);

        $stats = $this->quizStats($quiz);

        // Distribution des scores (tranches de 10%)
        $distribution = [];
        for ($i = 0; $i < 10; $i++) {
            $min = $i * 10;
            $max = $min + 9;
            $distribution[] = [
                'range' => "{$min}-{$max}%",
                'count' => $quiz->attempts->filter(fn($a) => $a->score >= $min && $a->score <= $max)->count(),
            ];
        }

        // Questions les plus ratées
        $questionStats = $quiz->questions->map(function ($q) use ($quiz) {
            $total    = $quiz->attempts->count();
            $answers  = collect($quiz->attempts->pluck('answers_given')->flatten(1));
            $correct  = $quiz->attempts->filter(fn($a) =>
                isset($a->answers_given[$q->id]) &&
                $a->answers_given[$q->id]['is_correct'] === true
            )->count();

            return [
                'question'     => $q->question,
                'correct_rate' => $total > 0 ? round($correct / $total * 100) : 0,
            ];
        })->sortBy('correct_rate');

        return view('teacher.quizzes.stats', compact('quiz', 'stats', 'distribution', 'questionStats'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  HELPERS PRIVÉS
    // ─────────────────────────────────────────────────────────────────────────

    private function authorizeTeacher(Quiz $quiz): void
    {
        abort_unless($quiz->course->user_id === Auth::id(), 403, 'Accès non autorisé.');
    }

    private function typeLabel(string $type): string
    {
        return match($type) {
            'single'     => 'QCM (réponse unique)',
            'multiple'   => 'QCM (réponses multiples)',
            'true_false' => 'Vrai / Faux',
            default      => $type,
        };
    }

    private function quizStats(Quiz $quiz): array
    {
        $attempts = $quiz->attempts ?? $quiz->attempts()->get();
        $total    = $attempts->count();

        return [
            'total_attempts'  => $total,
            'avg_score'       => $total > 0 ? round($attempts->avg('score'), 1) : 0,
            'pass_rate'       => $total > 0 ? round($attempts->where('passed', true)->count() / $total * 100) : 0,
            'best_score'      => $total > 0 ? $attempts->max('score') : 0,
            'total_questions' => $quiz->questions_count ?? $quiz->questions()->count(),
            'total_points'    => $quiz->questions()->sum('points'),
        ];
    }

    private function globalPassRate($quizIds): int
    {
        $total  = QuizAttempt::whereIn('quiz_id', $quizIds)->count();
        $passed = QuizAttempt::whereIn('quiz_id', $quizIds)->where('passed', true)->count();
        return $total > 0 ? round($passed / $total * 100) : 0;
    }
}