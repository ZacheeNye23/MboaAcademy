<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index', [
            'user' => Auth::user()
        ]);
    }

    // ── Notifications / préférences ──
    public function update(Request $request)
{
    $user = Auth::user();

    $user->update([
        // notifications
        'notify_forum'      => $request->has('notify_forum'),
        'notify_mentions'   => $request->has('notify_mentions'),
        'notify_courses'    => $request->has('notify_courses'),
        'notify_badges'     => $request->has('notify_badges'),

        // UX
        'dark_mode'         => $request->has('dark_mode'),
        'reduce_animations' => $request->has('reduce_animations'),

        // comportement
        'auto_resume_course'=> $request->has('auto_resume_course'),
        'hide_completed'    => $request->has('hide_completed'),
    ]);

    return back()->with('success', 'Paramètres mis à jour ⚙️');
}

   
}