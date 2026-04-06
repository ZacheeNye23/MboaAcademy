{{-- resources/views/components/footer.blade.php --}}
<footer class="bg-[#050e07] border-t border-green-bright/10 pt-12 pb-6 px-6 lg:px-10">
    <div class="max-w-7xl mx-auto">

        {{-- Footer top --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-10">

            {{-- Brand --}}
            <div>
                <a href="{{ route('welcome') }}" class="font-playfair text-2xl font-black text-white block mb-3">
                    Mboa<span class="text-gold">Academy</span>
                </a>
                <p class="text-sm text-white/35 leading-relaxed max-w-xs">
                    La plateforme e-learning made in Africa. Apprends, grandis et rayonne avec les meilleures formations.
                </p>
            </div>

            {{-- Plateforme --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">Plateforme</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-white/50 hover:text-green-bright transition-colors">Catalogue</a></li>
                    <li><a href="#" class="text-sm text-white/50 hover:text-green-bright transition-colors">Formateurs</a></li>
                    <li><a href="#" class="text-sm text-white/50 hover:text-green-bright transition-colors">Certifications</a></li>
                    <li><a href="#" class="text-sm text-white/50 hover:text-green-bright transition-colors">Forum</a></li>
                </ul>
            </div>

            {{-- Entreprise --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">Entreprise</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-white/50 hover:text-green-bright transition-colors">À propos</a></li>
                    <li><a href="#" class="text-sm text-white/50 hover:text-green-bright transition-colors">Blog</a></li>
                    <li><a href="#" class="text-sm text-white/50 hover:text-green-bright transition-colors">Carrières</a></li>
                    <li><a href="#" class="text-sm text-white/50 hover:text-green-bright transition-colors">Contact</a></li>
                </ul>
            </div>

            {{-- Légal --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">Légal</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-white/50 hover:text-green-bright transition-colors">CGU</a></li>
                    <li><a href="#" class="text-sm text-white/50 hover:text-green-bright transition-colors">Confidentialité</a></li>
                    <li><a href="#" class="text-sm text-white/50 hover:text-green-bright transition-colors">Cookies</a></li>
                </ul>
            </div>
        </div>

        {{-- Footer bottom --}}
        <div class="border-t border-white/5 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-xs text-white/25">© {{ date('Y') }} MboaAcademy. Tous droits réservés. 🌍 Made in Africa</p>
            <div class="flex gap-2">
                @foreach(['𝕏', 'in', 'f', '▶'] as $icon)
                <a href="#" class="w-8 h-8 rounded-lg border border-white/10 flex items-center justify-center text-xs text-white/40 hover:border-green-bright hover:text-green-bright transition-all duration-200">
                    {{ $icon }}
                </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>