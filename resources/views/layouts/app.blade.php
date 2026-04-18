<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Lyon Palme') - Gestion des adherents</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    @include('partials.navbar')

    <div class="mx-auto flex w-full max-w-7xl flex-col gap-4 px-4 py-6 sm:px-6 lg:flex-row lg:gap-6 lg:px-8">
        @include('partials.sidebar')

        <main class="min-w-0 flex-1">
            @include('partials.alerts')
            @yield('content')
        </main>
    </div>

    <footer class="mx-auto mt-6 w-full max-w-7xl px-4 pb-8 text-xs text-slate-500 sm:px-6 lg:px-8">
        Lyon Palme - Application de gestion interne.
    </footer>
</body>
</html>
