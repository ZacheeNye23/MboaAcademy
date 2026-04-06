<?php
// routes/web.php

use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\CertificateController;
use App\Http\Controllers\Student\BadgeController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\Teacher\QuizController as TeacherQuizController;
use App\Http\Controllers\Teacher\StudentController as TeacherStudentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\ForumController;
use Illuminate\Support\Facades\Route;

// ── Landing ──────────────────────────────────────────────────────────────────
Route::get('/', fn() => view('welcome'))->name('welcome');

// ══════════════════════════════════════════════════════════════════════════════
//  ESPACE APPRENANT
// ══════════════════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {

    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');

    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/',                          [StudentCourseController::class, 'index'])->name('index');
        Route::get('/mes-cours',                 [StudentCourseController::class, 'myCourses'])->name('mine');
        Route::get('/{slug}',                    [StudentCourseController::class, 'show'])->name('show');
        Route::post('/{course}/enroll',          [StudentCourseController::class, 'enroll'])->name('enroll');
        Route::get('/{slug}/learn',              [StudentCourseController::class, 'learn'])->name('learn');
        Route::post('/lesson/{id}/complete',     [StudentCourseController::class, 'completeLesson'])->name('lesson.complete');
    });

    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/',                          [StudentQuizController::class, 'index'])->name('index');
        Route::get('/{quiz}',                    [StudentQuizController::class, 'show'])->name('show');
        Route::post('/{quiz}/submit',            [StudentQuizController::class, 'submit'])->name('submit');
        Route::get('/result/{attempt}',          [StudentQuizController::class, 'result'])->name('result');
    });

    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/',                          [CertificateController::class, 'index'])->name('index');
        Route::get('/{certificate}/download',    [CertificateController::class, 'download'])->name('download');
    });

    Route::get('/badges', [BadgeController::class, 'index'])->name('badges.index');

    Route::prefix('forum/{course:slug}')->name('forum.')->group(function () {
        Route::get('/',                          [ForumController::class, 'index'])->name('index');
        Route::post('/threads',                  [ForumController::class, 'store'])->name('store');
        Route::get('/threads/{thread}',          [ForumController::class, 'show'])->name('show');
        Route::post('/threads/{thread}/reply',   [ForumController::class, 'reply'])->name('reply');
    });
});

// ══════════════════════════════════════════════════════════════════════════════
//  ESPACE FORMATEUR
// ══════════════════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
 
    Route::get('/dashboard', [TeacherDashboard::class, 'index'])->name('dashboard');
 
    // ── Cours ─────────────────────────────────────────────────────────────────
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/',                                        [TeacherCourseController::class, 'index'])->name('index');
        Route::get('/create',                                  [TeacherCourseController::class, 'create'])->name('create');
        Route::post('/',                                       [TeacherCourseController::class, 'store'])->name('store');
        Route::get('/{course}/edit',                           [TeacherCourseController::class, 'edit'])->name('edit');
        Route::put('/{course}',                                [TeacherCourseController::class, 'update'])->name('update');
        Route::patch('/{course}/submit',                       [TeacherCourseController::class, 'submit'])->name('submit');
        Route::delete('/{course}',                             [TeacherCourseController::class, 'destroy'])->name('destroy');
        Route::post('/{course}/thumbnail',                     [TeacherCourseController::class, 'uploadThumbnail'])->name('thumbnail');
 
        // Chapitres
        Route::post('/{course}/chapters',                      [TeacherCourseController::class, 'storeChapter'])->name('chapters.store');
        Route::put('/chapters/{chapter}',                      [TeacherCourseController::class, 'updateChapter'])->name('chapters.update');
        Route::delete('/chapters/{chapter}',                   [TeacherCourseController::class, 'destroyChapter'])->name('chapters.destroy');
        Route::post('/{course}/chapters/reorder',              [TeacherCourseController::class, 'reorderChapters'])->name('chapters.reorder');
 
        // Leçons
        Route::post('/chapters/{chapter}/lessons',             [TeacherCourseController::class, 'storeLesson'])->name('lessons.store');
        Route::put('/lessons/{lesson}',                        [TeacherCourseController::class, 'updateLesson'])->name('lessons.update');
        Route::delete('/lessons/{lesson}',                     [TeacherCourseController::class, 'destroyLesson'])->name('lessons.destroy');
        Route::post('/chapters/{chapter}/lessons/reorder',     [TeacherCourseController::class, 'reorderLessons'])->name('lessons.reorder');
    });
 
    // Forum (partagé, + marquer solution)
    Route::prefix('forum/{course:slug}')->name('forum.')->group(function () {
        Route::get('/',                              [ForumController::class, 'index'])->name('index');
        Route::get('/threads/{thread}',              [ForumController::class, 'show'])->name('show');
        Route::post('/threads/{thread}/reply',       [ForumController::class, 'reply'])->name('reply');
        Route::patch('/replies/{reply}/solution',    [ForumController::class, 'markSolution'])->name('solution');
    });
    
    Route::prefix('quizzes')->name('quizzes.')->group(function () {
 
    // CRUD Quiz
    Route::get('/',                [TeacherQuizController::class, 'index'])->name('index');
    Route::post('/',               [TeacherQuizController::class, 'store'])->name('store');
    Route::get('/{quiz}/edit',     [TeacherQuizController::class, 'edit'])->name('edit');
    Route::put('/{quiz}',          [TeacherQuizController::class, 'update'])->name('update');
    Route::delete('/{quiz}',       [TeacherQuizController::class, 'destroy'])->name('destroy');
 
    // Stats
    Route::get('/{quiz}/stats', [QuizStatsController::class, 'show'])
     ->name('stats');
 
// Vue d'ensemble de tous les quiz (optionnel)
Route::get('/stats/overview', [QuizStatsController::class, 'overview'])
     ->name('stats.overview');
 
    // Questions (AJAX → JSON)
    Route::post('/{quiz}/questions',               [TeacherQuizController::class, 'addQuestion'])->name('questions.store');
    Route::put('/questions/{question}',            [TeacherQuizController::class, 'updateQuestion'])->name('questions.update');
    Route::delete('/questions/{question}',         [TeacherQuizController::class, 'destroyQuestion'])->name('questions.destroy');
    Route::post('/{quiz}/questions/reorder',       [TeacherQuizController::class, 'reorderQuestions'])->name('questions.reorder');
});
Route::prefix('students')->name('students.')->group(function () {
    Route::get('/',                    [TeacherStudentController::class, 'index'])->name('index');
    Route::get('/export',              [TeacherStudentController::class, 'export'])->name('export');
    Route::get('/{enrollment}',        [TeacherStudentController::class, 'show'])->name('show');
});
 

});

// ══════════════════════════════════════════════════════════════════════════════
//  ESPACE ADMIN
// ══════════════════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
});

require __DIR__ . '/auth.php';