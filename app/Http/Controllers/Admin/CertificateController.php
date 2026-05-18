<?php
// app/Http/Controllers/Admin/CertificateController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    // ── Liste des certificats ───────────────────────────────────────────────
    public function index(Request $request): View
    {
        $query = Certificate::with(['user', 'course.teacher'])
            ->latest('issued_at');

        // Filtres
        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('certificate_number', 'like', '%'.$request->search.'%')
                  ->orWhereHas('user', fn($u) =>
                      $u->where('first_name', 'like', '%'.$request->search.'%')
                        ->orWhere('last_name',  'like', '%'.$request->search.'%')
                        ->orWhere('email',      'like', '%'.$request->search.'%')
                  )
                  ->orWhereHas('course', fn($c) =>
                      $c->where('title', 'like', '%'.$request->search.'%')
                  )
            );
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('period')) {
            $query->where('issued_at', '>=', now()->subDays((int) $request->period));
        }

        $certificates = $query->paginate(20)->appends(request()->query());

        // ── KPI Stats ────────────────────────────────────────────────────────
        $stats = [
            'total'          => Certificate::count(),
            'this_month'     => Certificate::whereMonth('issued_at', now()->month)
                                           ->whereYear('issued_at', now()->year)
                                           ->count(),
            'unique_courses' => Certificate::distinct('course_id')->count('course_id'),
            'unique_users'   => Certificate::distinct('user_id')->count('user_id'),
        ];

        // ── Top cours par nombre de certificats ──────────────────────────────
        $topCourses = Course::withCount('certificates as certs_count')
            ->having('certs_count', '>', 0)
            ->orderByDesc('certs_count')
            ->take(6)
            ->get();

        // ── Émissions par mois (année en cours) ──────────────────────────────
        $monthlyData = Certificate::whereYear('issued_at', now()->year)
            ->selectRaw('MONTH(issued_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthlyStats = [];
        foreach (range(1, 12) as $m) {
            $monthlyStats[$m] = $monthlyData[$m]->total ?? 0;
        }

        // ── Liste cours pour le filtre ────────────────────────────────────────
        $coursesList = Course::published()->orderBy('title')->get();

        return view('admin.certificates.index', compact(
            'certificates', 'stats', 'topCourses', 'monthlyStats', 'coursesList'
        ));
    }

    // ── Détail d'un certificat ──────────────────────────────────────────────
    public function show(Certificate $certificate): View
    {
        $certificate->load([
            'user.certificates',
            'course.teacher',
            'course.certificates',
        ]);

        // Enrichir avec le count enrollments du cours
        $certificate->course->loadCount('enrollments');

        return view('admin.certificates.show', compact('certificate'));
    }

    // ── Révoquer (supprimer) un certificat ─────────────────────────────────
    public function destroy(Certificate $certificate): RedirectResponse
    {
        $number = $certificate->certificate_number;
        $user   = $certificate->user->full_name;

        // Supprimer le fichier PDF s'il existe
        if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
            Storage::disk('public')->delete($certificate->file_path);
        }

        $certificate->delete();

        return redirect()->route('admin.certificates.index')
            ->with('success', "Certificat {$number} de {$user} révoqué avec succès.");
    }
}