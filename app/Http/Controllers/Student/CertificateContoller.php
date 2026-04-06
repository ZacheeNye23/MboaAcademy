<?php
namespace App\Http\Controllers\Student;
 
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
 
class CertificateController extends Controller
{
    /** Mes certificats */
    public function index(): View
    {
        $certificates = Certificate::with('course')
            ->where('user_id', Auth::id())
            ->latest('issued_at')
            ->get();
 
        return view('student.certificates.index', compact('certificates'));
    }
 
    /** Télécharger un certificat */
    public function download(Certificate $certificate)
    {
        abort_unless($certificate->user_id === Auth::id(), 403);
 
        if ($certificate->file_path && \Storage::exists($certificate->file_path)) {
            return \Storage::download($certificate->file_path, "Certificat-{$certificate->certificate_number}.pdf");
        }
 
        return back()->with('error', 'Certificat non disponible pour le moment.');
    }
}