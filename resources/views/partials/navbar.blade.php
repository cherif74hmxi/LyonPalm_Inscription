<nav class="bg-lyon-gradient text-white shadow-sm">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <img src="{{ asset('logo.svg') }}" alt="Logo Lyon Palme" class="h-10 w-10 rounded-lg bg-white/20 p-1" />
            <div>
                <p class="text-lg font-bold leading-tight">Lyon Palme</p>
                <p class="text-xs text-white/80">Gestion des inscriptions</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('password.show') }}" class="hidden rounded-lg bg-white/15 px-3 py-2 text-sm font-medium hover:bg-white/25 sm:inline-flex">
                Mon profil
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-100">
                    Déconnexion
                </button>
            </form>
        </div>
    </div>
</nav>
