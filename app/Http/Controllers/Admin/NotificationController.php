<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminBroadcastNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    // ── Liste des notifications ──────────────────────────────────────────────
    public function index(Request $request): View
    {
        $query = DatabaseNotification::with('notifiable')
            ->orderByDesc('created_at');

        // Filtre
        if ($request->filled('status')) {
            $request->status === 'read'
                ? $query->whereNotNull('read_at')
                : $query->whereNull('read_at');
        }

        if ($request->filled('search')) {
            $query->where('data->message', 'like', '%'.$request->search.'%');
        }

        $notifications = $query->paginate(20)->withQueryString();

        $stats = [
            'total'  => DatabaseNotification::count(),
            'unread' => DatabaseNotification::whereNull('read_at')->count(),
            'read'   => DatabaseNotification::whereNotNull('read_at')->count(),
        ];

        // Templates disponibles
        $templates = $this->getTemplates();

        return view('admin.system.notifications', compact('notifications', 'stats', 'templates'));
    }

    // ── Envoyer une notification broadcast ──────────────────────────────────
    public function send(Request $request): RedirectResponse
    {
        $request->validate([
            'title'   => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:500'],
            'target'  => ['required', 'in:all,students,teachers'],
            'type'    => ['required', 'in:info,success,warning'],
        ], [
            'title.required'   => 'Le titre est obligatoire.',
            'message.required' => 'Le message est obligatoire.',
        ]);

        $users = match($request->target) {
            'students' => User::where('role', 'student')->get(),
            'teachers' => User::where('role', 'teacher')->get(),
            default    => User::all(),
        };

        Notification::send($users, new AdminBroadcastNotification(
            title:   $request->title,
            message: $request->message,
            type:    $request->type,
        ));

        $count = $users->count();
        return back()->with('success', "✅ Notification envoyée à {$count} utilisateur(s).");
    }

    // ── Marquer tout comme lu ────────────────────────────────────────────────
    public function markAllRead(): RedirectResponse
    {
        DatabaseNotification::whereNull('read_at')->update(['read_at' => now()]);
        return back()->with('success', '✅ Toutes les notifications marquées comme lues.');
    }

    // ── Marquer une notification comme lue ──────────────────────────────────
    public function markRead(DatabaseNotification $notification): RedirectResponse
    {
        $notification->markAsRead();
        return back()->with('success', '✅ Notification marquée comme lue.');
    }

    // ── Supprimer une notification ───────────────────────────────────────────
    public function destroy(DatabaseNotification $notification): RedirectResponse
    {
        $notification->delete();
        return back()->with('success', '🗑 Notification supprimée.');
    }

    // ── Supprimer toutes les notifications lues ──────────────────────────────
    public function destroyRead(): RedirectResponse
    {
        DatabaseNotification::whereNotNull('read_at')->delete();
        return back()->with('success', '🗑 Notifications lues supprimées.');
    }

    // ── Templates prédéfinis ─────────────────────────────────────────────────
    private function getTemplates(): array
    {
        return [
            [
                'label'   => '🎉 Bienvenue',
                'title'   => 'Bienvenue sur MboaAcademy !',
                'message' => 'Découvrez tous nos cours disponibles et commencez votre apprentissage dès aujourd\'hui.',
                'type'    => 'success',
                'target'  => 'all',
            ],
            [
                'label'   => '🛠 Maintenance',
                'title'   => 'Maintenance planifiée',
                'message' => 'Le site sera en maintenance ce soir de 22h à 23h. Merci de votre compréhension.',
                'type'    => 'warning',
                'target'  => 'all',
            ],
            [
                'label'   => '📚 Nouveau cours',
                'title'   => 'Un nouveau cours est disponible !',
                'message' => 'Un nouveau cours vient d\'être publié sur la plateforme. Connectez-vous pour le découvrir.',
                'type'    => 'info',
                'target'  => 'students',
            ],
            [
                'label'   => '💰 Paiement',
                'title'   => 'Virement formateurs effectué',
                'message' => 'Vos revenus du mois ont été virés. Consultez votre espace revenus pour les détails.',
                'type'    => 'success',
                'target'  => 'teachers',
            ],
        ];
    }
}