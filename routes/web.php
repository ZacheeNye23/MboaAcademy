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
use App\Http\Controllers\Teacher\QuizStatsController as TeacherQuizStatsController;
use App\Http\Controllers\Teacher\StudentController as TeacherStudentController;
use App\Http\Controllers\Teacher\StudentShowController as TeacherStudentShowController;
use App\Http\Controllers\Teacher\RevenueController as TeacherRevenueController;
use App\Http\Controllers\Teacher\StatisticsController as TeacherStatisticsController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\Admin\ForumController as AdminForumController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\RevenueController  as AdminRevenueController;
use App\Http\Controllers\Admin\PaymentController  as AdminPaymentController;
use App\Http\Controllers\Admin\PayoutController   as AdminPayoutController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// ── Landing ──────────────────────────────────────────────────────────────────
Route::get('/', fn() => view('welcome'))->name('welcome');
Route::get('/about', fn() => view('about'))->name('about');

Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::patch('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
});

Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

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
        Route::get('/{certificate}',             [CertificateController::class, 'show'])->name('show');
        Route::get('/{certificate}/download',    [CertificateController::class, 'download'])->name('download');
    });

    Route::prefix('badges')->name('badges.')->group(function () {
        Route::get('/',            [BadgeController::class, 'index'])->name('index');
        Route::get('/leaderboard', [BadgeController::class, 'leaderboard'])->name('leaderboard');
    });

    // ── Forum Student (dans le groupe student) ────────────────────────────────
    // IMPORTANT : overview AVANT {course:slug} pour éviter le conflit de routing
    Route::get('/forum', [ForumController::class, 'overview'])->name('forum.overview');

    Route::prefix('forum/{course:slug}')->name('forum.')->group(function () {
        Route::get('/',                              [ForumController::class, 'index'])->name('index');
        Route::get('/create',                        [ForumController::class, 'create'])->name('create');
        Route::post('/threads',                      [ForumController::class, 'store'])->name('store');
        Route::get('/threads/{thread}',              [ForumController::class, 'show'])->name('show');
        Route::patch('/replies/{reply}/solution',     [ForumController::class, 'markSolution'])->name('solution');
        Route::post('/threads/{thread}/reply',       [ForumController::class, 'reply'])->name('reply');
        Route::post('/threads/{thread}/typing',      [ForumController::class, 'typing'])->name('typing');
        Route::delete('/replies/{reply}',            [ForumController::class, 'destroyReply'])->name('reply.destroy');
        Route::delete('/threads/{thread}',           [ForumController::class, 'destroyThread'])->name('thread.destroy');
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

    // ── Quiz ──────────────────────────────────────────────────────────────────
    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/',                                        [TeacherQuizController::class, 'index'])->name('index');
        Route::post('/',                                       [TeacherQuizController::class, 'store'])->name('store');
        Route::get('/{quiz}/edit',                             [TeacherQuizController::class, 'edit'])->name('edit');
        Route::put('/{quiz}',                                  [TeacherQuizController::class, 'update'])->name('update');
        Route::delete('/{quiz}',                               [TeacherQuizController::class, 'destroy'])->name('destroy');
        Route::get('/stats/overview',                          [TeacherQuizStatsController::class, 'overview'])->name('stats.overview');
        Route::get('/{quiz}/stats',                            [TeacherQuizStatsController::class, 'show'])->name('stats');
        Route::post('/{quiz}/questions',                       [TeacherQuizController::class, 'addQuestion'])->name('questions.store');
        Route::put('/questions/{question}',                    [TeacherQuizController::class, 'updateQuestion'])->name('questions.update');
        Route::delete('/questions/{question}',                 [TeacherQuizController::class, 'destroyQuestion'])->name('questions.destroy');
        Route::post('/{quiz}/questions/reorder',               [TeacherQuizController::class, 'reorderQuestions'])->name('questions.reorder');
    });

    // ── Apprenants ────────────────────────────────────────────────────────────
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/',                                        [TeacherStudentController::class, 'index'])->name('index');
        Route::get('/export',                                  [TeacherStudentController::class, 'export'])->name('export');
        Route::get('/{enrollment}',                            TeacherStudentShowController::class)->name('show');
    });

    // ── Revenus ───────────────────────────────────────────────────────────────
    Route::prefix('revenues')->name('revenues.')->group(function () {
        Route::get('/',                                        [TeacherRevenueController::class, 'index'])->name('index');
        Route::get('/export',                                  [TeacherRevenueController::class, 'export'])->name('export');
    });

    // ── Statistiques ──────────────────────────────────────────────────────────
    Route::get('/statistics', [TeacherStatisticsController::class, 'index'])->name('statistics.index');

    // ── Forum Teacher (dans le groupe teacher) ────────────────────────────────
    // IMPORTANT : overview AVANT {course:slug} pour éviter le conflit de routing
    Route::get('/forum', [ForumController::class, 'overview'])->name('forum.overview');

    Route::prefix('forum/{course:slug}')->name('forum.')->group(function () {
        Route::get('/',                              [ForumController::class, 'index'])->name('index');
        Route::get('/create', [ForumController::class, 'create'])->name('create');
        Route::post('/threads', [ForumController::class, 'store'])->name('store');
        Route::get('/threads/{thread}',              [ForumController::class, 'show'])->name('show');
        Route::patch('/replies/{reply}/solution',     [ForumController::class, 'markSolution'])->name('solution');
        Route::post('/threads/{thread}/reply',       [ForumController::class, 'reply'])->name('reply');
        Route::post('/threads/{thread}/typing',      [ForumController::class, 'typing'])->name('typing');
        Route::patch('/threads/{thread}/pin',        [ForumController::class, 'pin'])->name('pin');
        Route::patch('/threads/{thread}/close',      [ForumController::class, 'close'])->name('close');
        Route::delete('/replies/{reply}',            [ForumController::class, 'destroyReply'])->name('reply.destroy');
        Route::delete('/threads/{thread}',           [ForumController::class, 'destroyThread'])->name('thread.destroy');
    });
});

// ══════════════════════════════════════════════════════════════════════════════
//  ESPACE ADMIN
// ══════════════════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
 
    // ── Dashboard & Analytiques ────────────────────────────────────────────
    Route::get('/dashboard',  [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/analytics',  [AnalyticsController::class, 'index'])->name('analytics');
 
    // ── Utilisateurs ──────────────────────────────────────────────────────
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/',              [UserController::class, 'index'])->name('index');
        Route::get('/create',        [UserController::class, 'create'])->name('create');
        Route::post('/',             [UserController::class, 'store'])->name('store');
        Route::get('/{user}',        [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit',   [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}',        [UserController::class, 'update'])->name('update');
        Route::patch('/{user}/toggle',[UserController::class, 'toggle'])->name('toggle');
        Route::delete('/{user}',     [UserController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-toggle',  [UserController::class, 'bulkToggle'])->name('bulk-toggle');
        Route::delete('/bulk-delete',[UserController::class, 'bulkDelete'])->name('bulk-delete');
    });
 
    // ── Cours ─────────────────────────────────────────────────────────────
   Route::prefix('courses')->name('courses.')->group(function () {
 
    Route::get('/',                          [CourseController::class, 'index'])->name('index');
    Route::get('/{course}',                  [CourseController::class, 'show'])->name('show');
    Route::get('/{course}/edit',             [CourseController::class, 'edit'])->name('edit');
    Route::put('/{course}',                  [CourseController::class, 'update'])->name('update');
    Route::delete('/{course}',               [CourseController::class, 'destroy'])->name('destroy');
 
    // Actions de modération
    Route::patch('/{course}/approve',        [CourseController::class, 'approve'])->name('approve');
    Route::patch('/{course}/reject',         [CourseController::class, 'reject'])->name('reject');
    Route::patch('/{course}/unpublish',      [CourseController::class, 'unpublish'])->name('unpublish');
});
    // // ── Quiz ──────────────────────────────────────────────────────────────
    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/',         [QuizController::class, 'index'])->name('index');
        Route::get('/{quiz}',   [QuizController::class, 'show'])->name('show');
        Route::delete('/{quiz}',[QuizController::class, 'destroy'])->name('destroy');
    });
    // // ── Forum ─────────────────────────────────────────────────────────────
   Route::get('/forum', [AdminForumController::class, 'overview'])->name('forum.overview');
 
Route::prefix('forum/{course:slug}')->name('forum.')->group(function () {
 
    Route::get('/',                              [AdminForumController::class, 'index'])->name('index');
    Route::get('/threads/{thread}',              [AdminForumController::class, 'show'])->name('show');
 
    // Modération
    Route::patch('/threads/{thread}/pin',        [AdminForumController::class, 'pin'])->name('pin');
    Route::patch('/threads/{thread}/close',      [AdminForumController::class, 'close'])->name('close');
    Route::delete('/threads/{thread}',           [AdminForumController::class, 'destroyThread'])->name('thread.destroy');
    Route::delete('/replies/{reply}',            [AdminForumController::class, 'destroyReply'])->name('reply.destroy');
});
    // // ── Certificats ───────────────────────────────────────────────────────
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/',              [AdminCertificateController::class, 'index'])->name('index');
        Route::get('/{certificate}', [AdminCertificateController::class, 'show'])->name('show');
        Route::delete('/{certificate}',[AdminCertificateController::class, 'destroy'])->name('destroy');
    });
    // // ── Finances ──────────────────────────────────────────────────────────
    // ── Revenus globaux ──────────────────────────────────────────────────────────
Route::get('/revenues', [AdminRevenueController::class, 'index'])->name('revenues.index');
 
// ── Paiements ────────────────────────────────────────────────────────────────
Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/',                              [AdminPaymentController::class, 'index'])->name('index');
    Route::patch('/{payment}/complete',          [AdminPaymentController::class, 'markCompleted'])->name('complete');
    Route::patch('/{payment}/refund',            [AdminPaymentController::class, 'refund'])->name('refund');
});
 
// ── Reversements formateurs ──────────────────────────────────────────────────
Route::prefix('payouts')->name('payouts.')->group(function () {
    Route::get('/',                              [AdminPayoutController::class, 'index'])->name('index');
    Route::get('/{teacher}',                     [AdminPayoutController::class, 'show'])->name('show');
    Route::patch('/{teacher}/mark-paid',         [AdminPayoutController::class, 'markPaid'])->name('markPaid');
});
  // ── Paramètres ───────────────────────────────────────────────────────────────
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/',                        [AdminSettingsController::class, 'index'])->name('index');
    Route::post('/platform',               [AdminSettingsController::class, 'updatePlatform'])->name('platform');
    Route::post('/mail',                   [AdminSettingsController::class, 'updateMail'])->name('mail');
    Route::post('/commission',             [AdminSettingsController::class, 'updateCommission'])->name('commission');
    Route::post('/social',                 [AdminSettingsController::class, 'updateSocial'])->name('social');
    Route::post('/maintenance',            [AdminSettingsController::class, 'toggleMaintenance'])->name('maintenance');
});

// ── Notifications ────────────────────────────────────────────────────────────
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/',                        [NotificationController::class, 'index'])->name('index');
    Route::post('/send',                   [NotificationController::class, 'send'])->name('send');
    Route::patch('/mark-all-read',         [NotificationController::class, 'markAllRead'])->name('markAllRead');
    Route::patch('/{notification}/read',   [NotificationController::class, 'markRead'])->name('markRead');
    Route::delete('/read',                 [NotificationController::class, 'destroyRead'])->name('destroyRead');
    Route::delete('/{notification}',       [NotificationController::class, 'destroy'])->name('destroy');
});

// ── Sécurité & Logs ──────────────────────────────────────────────────────────
Route::prefix('security')->name('security.')->group(function () {
    Route::get('/',                        [SecurityController::class, 'index'])->name('index');
    Route::patch('/users/{user}/unban',    [SecurityController::class, 'unban'])->name('unban');
    Route::delete('/logs/clear',           [SecurityController::class, 'clearOldLogs'])->name('logs.clear');
});

});
 

// ── Vérification publique de certificat (sans auth) ────────────────────────────
Route::get('/verify/{number}', [\App\Http\Controllers\CertificateVerifyController::class, 'show'])
     ->name('certificates.verify');

require __DIR__ . '/auth.php';