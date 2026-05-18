<?php
namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\View\View;

class CertificateVerifyController extends Controller
{
    /**
     * Vérification publique d'un certificat (accessible sans connexion)
     * URL : /verify/{number}
     */
    public function show(string $number): View
    {
        $certificate = Certificate::with(['user', 'course.teacher'])
            ->where('certificate_number', strtoupper($number))
            ->first();

        return view('certificates.verify', compact('certificate'));
    }
}