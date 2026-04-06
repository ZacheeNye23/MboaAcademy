<?php
namespace App\Http\Controllers\Student;
 
use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\UserBadge;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
 
class BadgeController extends Controller
{
    /** Tous les badges + ceux obtenus par l'étudiant */
    public function index(): View
    {
        $allBadges    = Badge::all();
        $earnedBadges = UserBadge::with('badge')
            ->where('user_id', Auth::id())
            ->latest('earned_at')
            ->get()
            ->keyBy('badge_id');
 
        return view('student.badges.index', compact('allBadges', 'earnedBadges'));
    }
}
 