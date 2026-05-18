@extends('admin.layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('page-subtitle', $stats['unread'].' non lue(s) · '.$stats['total'].' au total')

@section('topbar-actions')
<form method="POST" action="{{ route('admin.notifications.markAllRead') }}">
    @csrf @method('PATCH')
    <button type="submit"
            class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors"
            style="background:rgba(37,194,110,0.1);color:#25c26e">
        ✅ Tout marquer lu
    </button>
</form>
@endsection

@push('styles')
<style>
    .notif-row {
        display:flex; align-items:flex-start; gap:14px;
        padding:16px 24px; border-bottom:1px solid rgba(255,255,255,0.04);
        transition:background .2s;
    }
    .notif-row:hover { background:rgba(255,255,255,0.02); }
    .notif-row:last-child { border-bottom:none; }
    .notif-row.unread { border-left:3px solid #e8b84b; background:rgba(232,184,75,0.02); }
    .type-dot { width:9px;height:9px;border-radius:50%;flex-shrink:0;margin-top:5px; }
    .filter-btn { padding:6px 14px;border-radius:100px;font-size:.75rem;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap;border:none;font-family:'Outfit',sans-serif; }
    .filter-btn.on  { background:#e8b84b;color:#0a1a0f; }
    .filter-btn.off { background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.45);border:1px solid rgba(255,255,255,0.08); }
    .filter-btn.off:hover { border-color:rgba(232,184,75,0.3);color:#e8b84b; }
    .template-card { background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.06);border-radius:14px;padding:14px 16px;cursor:pointer;transition:all .2s; }
    .template-card:hover { border-color:rgba(232,184,75,0.25);background:rgba(232,184,75,0.04); }
    .form-label { display:block;font-size:.7rem;font-weight:700;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:.07rem;margin-bottom:7px; }
    .form-input { width:100%;background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.08);border-radius:12px;padding:10px 15px;font-family:'Outfit',sans-serif;font-size:.875rem;color:#fff;outline:none;transition:all .2s; }
    .form-input:focus { border-color:rgba(232,184,75,0.4);background:rgba(255,255,255,0.06); }
    .form-input option { background:#0d1f10; }
    textarea.form-input { min-height:90px;resize:vertical; }
    .btn-send { display:inline-flex;align-items:center;gap:8px;padding:10px 22px;border-radius:12px;font-family:'Outfit',sans-serif;font-size:.875rem;font-weight:700;color:#0a1a0f;background:linear-gradient(135deg,#e8b84b,#f0d070);border:none;cursor:pointer;transition:all .2s; }
    .btn-send:hover { transform:translateY(-1px);box-shadow:0 5px 16px rgba(232,184,75,0.3); }
    [x-cloak]{display:none!important}
</style>
@endpush

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-7">
    @foreach([
        ['📬','Total',    $stats['total'],  '#e8b84b'],
        ['🔴','Non lues', $stats['unread'], '#f87171'],
        ['✅','Lues',     $stats['read'],   '#25c26e'],
    ] as [$icon,$label,$val,$color])
    <div class="glass p-5 anim d{{ $loop->iteration }}">
        <div class="text-2xl mb-2">{{ $icon }}</div>
        <div class="text-3xl font-bold" style="font-family:'Playfair Display',serif;color:{{ $color }}">{{ number_format($val) }}</div>
        <div class="text-xs mt-1" style="color:rgba(255,255,255,0.35)">{{ $label }}</div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── LISTE NOTIFICATIONS ────────────────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Filtres --}}
        <div class="glass p-4 anim d2">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('admin.notifications.index') }}"
                       class="filter-btn {{ !request('status') ? 'on' : 'off' }}">🗂 Toutes</a>
                    <a href="{{ route('admin.notifications.index', ['status'=>'unread']) }}"
                       class="filter-btn {{ request('status')==='unread' ? 'on' : 'off' }}">🔴 Non lues</a>
                    <a href="{{ route('admin.notifications.index', ['status'=>'read']) }}"
                       class="filter-btn {{ request('status')==='read' ? 'on' : 'off' }}">✅ Lues</a>
                </div>
                @if($stats['read'] > 0)
                <form method="POST" action="{{ route('admin.notifications.destroyRead') }}">
                    @csrf @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Supprimer toutes les notifications lues ?')"
                            class="filter-btn off" style="color:rgba(239,68,68,0.7);border-color:rgba(239,68,68,0.15)">
                        🗑 Vider les lues
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Liste --}}
        <div class="glass overflow-hidden anim d3">
            @forelse($notifications as $notif)
            @php
                $data    = $notif->data;
                $type    = $data['type'] ?? 'info';
                $dotColor = match($type) { 'success'=>'#25c26e','warning'=>'#e8b84b',default=>'#60a5fa' };
                $isUnread = is_null($notif->read_at);
            @endphp
            <div class="notif-row {{ $isUnread ? 'unread' : '' }}">
                <div class="type-dot" style="background:{{ $dotColor }}"></div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-sm font-semibold text-white">{{ $data['title'] ?? 'Notification' }}</span>
                        @if($isUnread)
                        <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:#e8b84b"></span>
                        @endif
                    </div>
                    <p class="text-xs leading-relaxed mb-2" style="color:rgba(255,255,255,0.45)">
                        {{ Str::limit($data['message'] ?? '', 120) }}
                    </p>
                    <div class="flex items-center gap-3 text-[10px]" style="color:rgba(255,255,255,0.25)">
                        <span>{{ $notif->created_at->diffForHumans() }}</span>
                        @if($notif->notifiable)
                        <span>· {{ $notif->notifiable->full_name }}</span>
                        @endif
                        @if(isset($data['target']))
                        <span>· Envoyé à : {{ match($data['target'] ?? 'all') { 'students'=>'Apprenants','teachers'=>'Formateurs',default=>'Tous' } }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    @if($isUnread)
                    <form method="POST" action="{{ route('admin.notifications.markRead', $notif) }}">
                        @csrf @method('PATCH')
                        <button type="submit" title="Marquer comme lue"
                                class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-colors"
                                style="background:rgba(37,194,110,0.1);color:#25c26e">✓</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('admin.notifications.destroy', $notif) }}"
                          onsubmit="return confirm('Supprimer cette notification ?')">
                        @csrf @method('DELETE')
                        <button type="submit" title="Supprimer"
                                class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-colors"
                                style="background:rgba(239,68,68,0.08);color:rgba(239,68,68,0.6)">🗑</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="text-5xl mb-4">🔔</div>
                <h3 class="font-bold text-white mb-2" style="font-family:'Playfair Display',serif">Aucune notification</h3>
                <p class="text-sm" style="color:rgba(255,255,255,0.3)">Envoyez votre première notification ci-contre.</p>
            </div>
            @endforelse
        </div>

        <div>{{ $notifications->withQueryString()->links() }}</div>
    </div>

    {{-- ── SIDEBAR : ENVOYER + TEMPLATES ─────────────────────────────────── --}}
    <div class="space-y-5 anim d3">

        {{-- Formulaire envoi --}}
        <div class="glass overflow-hidden"
             x-data="{ title: '', message: '', target: 'all', type: 'info' }">
            <div class="px-5 py-4 border-b border-white/5">
                <h3 class="font-bold text-white text-sm" style="font-family:'Playfair Display',serif">
                    📢 Envoyer une notification
                </h3>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('admin.notifications.send') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label">Destinataires</label>
                        <select name="target" x-model="target" class="form-input">
                            <option value="all">👥 Tous les utilisateurs</option>
                            <option value="students">🎓 Apprenants uniquement</option>
                            <option value="teachers">📖 Formateurs uniquement</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Type</label>
                        <select name="type" x-model="type" class="form-input">
                            <option value="info">ℹ️ Info</option>
                            <option value="success">✅ Succès</option>
                            <option value="warning">⚠️ Avertissement</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Titre *</label>
                        <input type="text" name="title" x-model="title"
                               class="form-input @error('title') border-red-500/50 @enderror"
                               placeholder="Ex: Mise à jour importante" required>
                        @error('title')<p class="text-xs mt-1 text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-5">
                        <label class="form-label">Message *</label>
                        <textarea name="message" x-model="message"
                                  class="form-input @error('message') border-red-500/50 @enderror"
                                  placeholder="Votre message..." required></textarea>
                        @error('message')<p class="text-xs mt-1 text-red-400">{{ $message }}</p>@enderror
                    </div>

                    {{-- Aperçu --}}
                    <div x-show="title || message"
                         class="mb-4 p-3 rounded-xl"
                         :style="type==='success' ? 'background:rgba(37,194,110,0.07);border:1px solid rgba(37,194,110,0.2)'
                               : type==='warning' ? 'background:rgba(232,184,75,0.07);border:1px solid rgba(232,184,75,0.2)'
                               : 'background:rgba(96,165,250,0.07);border:1px solid rgba(96,165,250,0.2)'">
                        <div class="text-xs font-bold mb-1" style="color:rgba(255,255,255,0.7)" x-text="title"></div>
                        <div class="text-xs" style="color:rgba(255,255,255,0.45)" x-text="message"></div>
                    </div>

                    <button type="submit" class="btn-send w-full justify-center">
                        📢 Envoyer
                    </button>
                </form>
            </div>
        </div>

        {{-- Templates --}}
        <div class="glass overflow-hidden">
            <div class="px-5 py-4 border-b border-white/5">
                <h3 class="font-bold text-white text-sm" style="font-family:'Playfair Display',serif">
                    📋 Templates rapides
                </h3>
            </div>
            <div class="p-4 space-y-2"
                 x-data="templates()">
                @foreach($templates as $tpl)
                <div class="template-card"
                     @click="apply({{ json_encode($tpl) }})">
                    <div class="text-xs font-semibold text-white mb-1">{{ $tpl['label'] }}</div>
                    <div class="text-[11px]" style="color:rgba(255,255,255,0.35)">
                        {{ Str::limit($tpl['message'], 70) }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function templates() {
    return {
        apply(tpl) {
            // Trouver les champs du formulaire et les remplir
            const form = document.querySelector('form[action="{{ route('admin.notifications.send') }}"]');
            if (!form) return;
            form.querySelector('[name="title"]').value   = tpl.title;
            form.querySelector('[name="message"]').value = tpl.message;
            form.querySelector('[name="type"]').value    = tpl.type;
            form.querySelector('[name="target"]').value  = tpl.target;
            // Sync Alpine si possible
            form.querySelectorAll('input,select,textarea').forEach(el => el.dispatchEvent(new Event('input')));
        }
    }
}
</script>
@endpush