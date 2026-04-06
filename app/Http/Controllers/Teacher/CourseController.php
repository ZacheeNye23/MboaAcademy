<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreCourseRequest;
use App\Http\Requests\Teacher\StoreChapterRequest;
use App\Http\Requests\Teacher\StoreLessonRequest;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\RevenueRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CourseController extends Controller
{
    // ── Constantes catégories & niveaux ──────────────────────────────────────
    const CATEGORIES = [
        'Développement web', 'Développement mobile', 'Data Science',
        'Design UI/UX', 'Marketing digital', 'Cybersécurité',
        'Intelligence artificielle', 'Gestion de projet',
        'Entrepreneuriat', 'Langues', 'Bureautique', 'Autre',
    ];

    const LEVELS = [
        'beginner'     => 'Débutant',
        'intermediate' => 'Intermédiaire',
        'advanced'     => 'Avancé',
    ];

    const LANGUAGES = [
        'fr' => 'Français',
        'en' => 'Anglais',
        'ar' => 'Arabe',
    ];

    // ── Liste des cours du formateur ─────────────────────────────────────────
    public function index(Request $request): View
{
    $teacher = Auth::user();
 
    // ── Filtre par statut ────────────────────────────────────────────────────
    $statusFilter = $request->get('status', 'all');
    $search       = $request->get('search', '');
 
    $query = Course::byTeacher($teacher->id)
        ->withCount(['enrollments', 'reviews'])
        ->with(['chapters' => fn($q) => $q->withCount('lessons')])
        ->withAvg('reviews', 'rating');
 
    if ($statusFilter !== 'all') {
        $query->where('status', $statusFilter);
    }
 
    if ($search) {
        $query->where('title', 'like', '%' . $search . '%');
    }
 
    $courses = $query->latest()->paginate(9)->withQueryString();
 
    // ── Stats globales du formateur ──────────────────────────────────────────
    $allCourses  = Course::byTeacher($teacher->id)->withCount('enrollments')->get();
    $courseIds   = $allCourses->pluck('id');
 
    $globalStats = [
        'total'      => $allCourses->count(),
        'published'  => $allCourses->where('status', 'published')->count(),
        'draft'      => $allCourses->where('status', 'draft')->count(),
        'pending'    => $allCourses->where('status', 'pending')->count(),
        'rejected'   => $allCourses->where('status', 'rejected')->count(),
        'students'   => \App\Models\Enrollment::whereIn('course_id', $courseIds)
                            ->distinct('user_id')->count('user_id'),
        'revenue'    => \App\Models\RevenueRecord::where('teacher_id', $teacher->id)
                            ->completed()->sum('net_amount'),
        'avg_rating' => round(\App\Models\CourseReview::whereIn('course_id', $courseIds)
                            ->avg('rating') ?? 0, 1),
    ];
 
    // ── Revenus par cours (pour les cards) ───────────────────────────────────
    $revenuesByCourse = \App\Models\RevenueRecord::where('teacher_id', $teacher->id)
        ->completed()
        ->select('course_id', DB::raw('SUM(net_amount) as total'))
        ->groupBy('course_id')
        ->pluck('total', 'course_id');
 
    return view('teacher.courses.index', compact(
        'courses', 'globalStats', 'statusFilter',
        'search', 'revenuesByCourse'
    ));
}
 

    // ── Formulaire de création ────────────────────────────────────────────────
    public function create(): View
    {
        return view('teacher.courses.create', [
            'categories' => self::CATEGORIES,
            'levels'     => self::LEVELS,
            'languages'  => self::LANGUAGES,
        ]);
    }

    // ── Enregistrer le cours (étape 1 — infos générales) ─────────────────────
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Upload thumbnail
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')
                ->store('thumbnails', 'public');
        }

        $data['user_id'] = Auth::id();
        $data['status']  = 'draft';
        $data['is_free'] = $request->boolean('is_free');

        $course = Course::create($data);

        return redirect()
            ->route('teacher.courses.edit', $course)
            ->with('success', 'Cours créé ! Ajoutez maintenant vos chapitres et leçons.');
    }

    // ── Formulaire d'édition (étape 2 — chapitres & leçons) ──────────────────
    public function edit(Course $course): View
    {
        $this->authorizeTeacher($course);
        $course->load('chapters.lessons.resources');

        return view('teacher.courses.edit', [
            'course'     => $course,
            'categories' => self::CATEGORIES,
            'levels'     => self::LEVELS,
            'languages'  => self::LANGUAGES,
        ]);
    }

    // ── Mettre à jour les infos du cours ──────────────────────────────────────
    public function update(StoreCourseRequest $request, Course $course): RedirectResponse
    {
        $this->authorizeTeacher($course);
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) Storage::disk('public')->delete($course->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $data['is_free'] = $request->boolean('is_free');
        $course->update($data);

        return back()->with('success', 'Informations du cours mises à jour !');
    }

    // ── Soumettre pour validation (draft → pending) ───────────────────────────
    public function submit(Course $course): RedirectResponse
    {
        $this->authorizeTeacher($course);

        if ($course->status !== 'draft') {
            return back()->with('error', 'Ce cours ne peut pas être soumis dans son état actuel.');
        }

        if (!$course->chapters()->exists()) {
            return back()->with('error', 'Ajoutez au moins un chapitre avant de soumettre.');
        }

        $hasLessons = $course->chapters->every(fn($c) => $c->lessons()->exists());
        if (!$hasLessons) {
            return back()->with('error', 'Chaque chapitre doit contenir au moins une leçon.');
        }

        $course->update(['status' => 'pending']);

        return redirect()
            ->route('teacher.courses.index')
            ->with('success', 'Cours soumis pour validation ! L\'administrateur le reviewera bientôt. 🎉');
    }

    // ── Supprimer un cours ────────────────────────────────────────────────────
    public function destroy(Course $course): RedirectResponse
    {
        $this->authorizeTeacher($course);

        if (!in_array($course->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Impossible de supprimer un cours publié ou en révision.');
        }

        if ($course->thumbnail) Storage::disk('public')->delete($course->thumbnail);
        $course->delete();

        return redirect()
            ->route('teacher.courses.index')
            ->with('success', 'Cours supprimé.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  CHAPITRES
    // ─────────────────────────────────────────────────────────────────────────

    public function storeChapter(StoreChapterRequest $request, Course $course): JsonResponse
    {
        $this->authorizeTeacher($course);

        $chapter = $course->chapters()->create([
            'title' => $request->title,
            'order' => $course->chapters()->count(),
        ]);

        return response()->json([
            'success' => true,
            'chapter' => [
                'id'    => $chapter->id,
                'title' => $chapter->title,
                'order' => $chapter->order,
            ],
        ]);
    }

    public function updateChapter(Request $request, Chapter $chapter): JsonResponse
    {
        $this->authorizeTeacher($chapter->course);

        $request->validate(['title' => ['required', 'string', 'max:255']]);
        $chapter->update([
            'title' => $request->title,
            'order' => $request->order ?? $chapter->order,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroyChapter(Chapter $chapter): JsonResponse
    {
        $this->authorizeTeacher($chapter->course);

        // Supprimer les vidéos des leçons
        foreach ($chapter->lessons as $lesson) {
            if ($lesson->video_path) Storage::disk('public')->delete($lesson->video_path);
        }
        $chapter->delete();

        return response()->json(['success' => true]);
    }

    // Réordonner les chapitres (drag & drop)
    public function reorderChapters(Request $request, Course $course): JsonResponse
    {
        $this->authorizeTeacher($course);
        $request->validate(['order' => ['required', 'array']]);

        foreach ($request->order as $index => $chapterId) {
            Chapter::where('id', $chapterId)
                   ->where('course_id', $course->id)
                   ->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  LEÇONS
    // ─────────────────────────────────────────────────────────────────────────

    public function storeLesson(StoreLessonRequest $request, Chapter $chapter): JsonResponse
    {
        $this->authorizeTeacher($chapter->course);

        $data = $request->validated();
        $data['order']   = $chapter->lessons()->count();
        $data['is_free'] = $request->boolean('is_free');

        // Upload vidéo locale
        if ($request->hasFile('video')) {
            $data['video_path'] = $request->file('video')
                ->store('videos/' . $chapter->course_id, 'public');

            // Durée auto si non fournie (approximation par taille)
            if (!$data['duration'] && $request->file('video')->getSize()) {
                $sizeMo = $request->file('video')->getSize() / (1024 * 1024);
                $data['duration'] = (int) ($sizeMo * 8); // ~1 min par 8 Mo
            }
        }

        $lesson = $chapter->lessons()->create($data);

        // Recalculer la durée totale du cours
        $totalMinutes = (int) ($chapter->course->lessons()->sum('duration') / 60);
        $chapter->course->update(['duration_minutes' => $totalMinutes]);

        return response()->json([
            'success' => true,
            'lesson'  => [
                'id'          => $lesson->id,
                'title'       => $lesson->title,
                'type'        => $lesson->type,
                'duration'    => $lesson->duration_formatted,
                'is_free'     => $lesson->is_free,
                'video_path'  => $lesson->video_path,
                'video_url'   => $lesson->video_url,
                'order'       => $lesson->order,
            ],
        ]);
    }

    public function updateLesson(Request $request, Lesson $lesson): JsonResponse
    {
        $this->authorizeTeacher($lesson->chapter->course);

        $lesson->update($request->only(['title', 'content', 'is_free', 'order', 'duration']));
        return response()->json(['success' => true]);
    }

    public function destroyLesson(Lesson $lesson): JsonResponse
    {
        $this->authorizeTeacher($lesson->chapter->course);

        if ($lesson->video_path) Storage::disk('public')->delete($lesson->video_path);
        $lesson->delete();

        return response()->json(['success' => true]);
    }

    public function reorderLessons(Request $request, Chapter $chapter): JsonResponse
    {
        $this->authorizeTeacher($chapter->course);
        $request->validate(['order' => ['required', 'array']]);

        foreach ($request->order as $index => $lessonId) {
            Lesson::where('id', $lessonId)
                  ->where('chapter_id', $chapter->id)
                  ->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  UPLOAD THUMBNAIL AJAX
    // ─────────────────────────────────────────────────────────────────────────

    public function uploadThumbnail(Request $request, Course $course): JsonResponse
    {
        $this->authorizeTeacher($course);
        $request->validate(['thumbnail' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']]);

        if ($course->thumbnail) Storage::disk('public')->delete($course->thumbnail);
        $path = $request->file('thumbnail')->store('thumbnails', 'public');
        $course->update(['thumbnail' => $path]);

        return response()->json([
            'success' => true,
            'url'     => asset('storage/' . $path),
        ]);
    }

    // ── Helper ────────────────────────────────────────────────────────────────
    private function authorizeTeacher(Course $course): void
    {
        abort_unless($course->user_id === Auth::id(), 403, 'Accès non autorisé.');
    }
}