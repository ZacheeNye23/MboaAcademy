<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
 
class SecurityController extends Controller
{
    // ── Vue principale sécurité & logs ───────────────────────────────────────
    public function index(Request $request): View
    {
        // ── Journaux de connexion ────────────────────────────────────────────
        $loginQuery = DB::table('login_attempts')
            ->join('users', 'login_attempts.user_id', '=', 'users.id', 'left')
            ->select(
                'login_attempts.*',
                'users.first_name',
                'users.last_name',
                'users.email as user_email',
                'users.role'
            )
            ->orderByDesc('login_attempts.created_at');
 
        if ($request->filled('login_search')) {
            $loginQuery->where(fn($q) =>
                $q->where('login_attempts.ip_address', 'like', '%'.$request->login_search.'%')
                  ->orWhere('users.email', 'like', '%'.$request->login_search.'%')
            );
        }
        if ($request->filled('login_status')) {
            $loginQuery->where('login_attempts.status', $request->login_status);
        }
 
        $loginAttempts = $loginQuery->paginate(15, ['*'], 'login_page')->appends(request()->query());
 
        // ── Actions admin ────────────────────────────────────────────────────
        $adminLogQuery = AdminLog::with('admin')
            ->orderByDesc('created_at');
 
        if ($request->filled('log_search')) {
            $adminLogQuery->where(fn($q) =>
                $q->where('action', 'like', '%'.$request->log_search.'%')
                  ->orWhere('description', 'like', '%'.$request->log_search.'%')
            );
        }
        if ($request->filled('log_action')) {
            $adminLogQuery->where('action', $request->log_action);
        }
 
        $adminLogs = $adminLogQuery->paginate(15, ['*'], 'log_page')->withQueryString();
 
        // ── Utilisateurs bannis ──────────────────────────────────────────────
        $bannedQuery = User::where('is_banned', true)->withCount('enrollments');
        if ($request->filled('banned_search')) {
            $bannedQuery->where(fn($q) =>
                $q->where('first_name', 'like', '%'.$request->banned_search.'%')
                  ->orWhere('last_name',  'like', '%'.$request->banned_search.'%')
                  ->orWhere('email',      'like', '%'.$request->banned_search.'%')
            );
        }
        $bannedUsers = $bannedQuery->latest()->paginate(10, ['*'], 'banned_page')->withQueryString();
 
        // ── Stats sécurité ───────────────────────────────────────────────────
        $stats = [
            'total_logins'     => DB::table('login_attempts')->count(),
            'failed_logins'    => DB::table('login_attempts')->where('status', 'failed')->count(),
            'logins_today'     => DB::table('login_attempts')->whereDate('created_at', today())->count(),
            'banned_users'     => User::where('is_banned', true)->count(),
            'admin_actions'    => AdminLog::count(),
            'admin_today'      => AdminLog::whereDate('created_at', today())->count(),
            'suspicious_ips'   => DB::table('login_attempts')
                ->where('status', 'failed')
                ->where('created_at', '>=', now()->subHours(24))
                ->select('ip_address', DB::raw('COUNT(*) as attempts'))
                ->groupBy('ip_address')
                ->having('attempts', '>=', 5)
                ->count(),
        ];
 
        // Types d'actions admin pour le filtre
        $actionTypes = AdminLog::select('action')->distinct()->pluck('action');
 
        return view('admin.system.security', compact(
            'loginAttempts', 'adminLogs', 'bannedUsers',
            'stats', 'actionTypes'
        ));
    }
 
    // ── Débannir un utilisateur ──────────────────────────────────────────────
    public function unban(User $user): RedirectResponse
    {
        $user->update(['is_banned' => false]);
 
        // Logger l'action
        AdminLog::create([
            'admin_id'    => auth()->id(),
            'action'      => 'unban_user',
            'description' => 'Utilisateur débanni : '.$user->full_name.' ('.$user->email.')',
            'ip_address'  => request()->ip(),
        ]);
 
        return back()->with('success', '✅ Utilisateur '.$user->full_name.' réactivé.');
    }
 
    // ── Vider les anciens logs ───────────────────────────────────────────────
    public function clearOldLogs(Request $request): RedirectResponse
    {
        $days = $request->input('days', 30);
 
        DB::table('login_attempts')
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
 
        AdminLog::where('created_at', '<', now()->subDays($days))->delete();
 
        return back()->with('success', "🗑 Logs de plus de {$days} jours supprimés.");
    }
}
 