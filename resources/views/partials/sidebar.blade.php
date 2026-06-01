<aside class="w-full rounded-xl bg-white p-4 shadow-sm lg:w-64 lg:shrink-0">
    <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Navigation</p>

    <nav class="space-y-2">
        <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50' }}">Tableau de bord</a>
        <a href="{{ route('adherents.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('adherents.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50' }}">Adhérents</a>
        <a href="{{ route('certificats.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('certificats.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50' }}">Certificats médicaux</a>
        <a href="{{ route('adhesions.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('adhesions.*') || request()->routeIs('paiements.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50' }}">Cotisations</a>
        <a href="{{ route('password.show') }}" class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('password.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50' }}">Changer le mot de passe</a>
    </nav>
</aside>
