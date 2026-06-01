<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Espace adhérent') - Lyon Palme</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <header class="bg-lyon-gradient text-white shadow-sm">
        <div class="mx-auto flex w-full max-w-5xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logo.svg') }}" alt="Logo Lyon Palme" class="h-10 w-10 rounded-lg bg-white/20 p-1" />
                <div>
                    <p class="text-lg font-bold leading-tight">Espace adhérent</p>
                    <p class="text-xs text-white/80">Suivi de mon dossier Lyon Palme</p>
                </div>
            </div>

            <form method="POST" action="{{ route('adherent.logout') }}">
                @csrf
                <button type="submit" class="rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-100">
                    Déconnexion
                </button>
            </form>
        </div>
    </header>

    <main class="mx-auto w-full max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
        @include('partials.alerts')
        @yield('content')
    </main>
</body>
</html>
