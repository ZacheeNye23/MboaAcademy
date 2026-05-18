<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CertificateController extends Controller
{
    // ── Liste des certificats ────────────────────────────────────────────────
    public function index(): View
    {
        $user = Auth::user();

        // Certificats obtenus
        $certificates = Certificate::with(['course.teacher'])
            ->where('user_id', $user->id)
            ->latest('issued_at')
            ->get();

        // Cours en cours (sans certificat encore)
        $certifiedCourseIds = $certificates->pluck('course_id');
        $inProgressEnrollments = Enrollment::with(['course.teacher', 'course.lessons'])
            ->where('user_id', $user->id)
            ->whereNull('completed_at')
            ->whereNotIn('course_id', $certifiedCourseIds)
            ->orderByDesc('progress_percent')
            ->get();

        return view('student.certificates.index', compact(
            'certificates', 'inProgressEnrollments'
        ));
    }

    // ── Voir un certificat ───────────────────────────────────────────────────
    public function show(Certificate $certificate): View
    {
        abort_unless($certificate->user_id === Auth::id(), 403);
        $certificate->load(['user', 'course.teacher']);

        return view('student.certificates.show', compact('certificate'));
    }

    // ── Télécharger le PDF ───────────────────────────────────────────────────
    public function download(Certificate $certificate)
    {
        abort_unless($certificate->user_id === Auth::id(), 403);
        $certificate->load(['user', 'course.teacher']);

        if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
    $fullPath = storage_path('app/public/' . $certificate->file_path);

    return response()->download(
        $fullPath,
        "Certificat-{$certificate->certificate_number}.pdf",
        ['Content-Type' => 'application/pdf']
    );
}

        return redirect()->route('student.certificates.show', $certificate)
            ->with('info', 'Utilisez la fonction d\'impression de votre navigateur pour sauvegarder en PDF.');
    }

    // ── Génération automatique d'un certificat ───────────────────────────────
    // Appelé automatiquement quand un cours est complété à 100%
    public static function generateFor(int $userId, int $courseId): Certificate
    { 
        return Certificate::firstOrCreate(
            ['user_id' => $userId, 'course_id' => $courseId],
            [
                'certificate_number' => Certificate::generateNumber(),
                'issued_at'          => now(),
            ]
        );
    }
}