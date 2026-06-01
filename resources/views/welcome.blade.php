<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Lyon Palme</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-shell flex items-center justify-center py-8">
    <main class="w-full max-w-5xl px-4">
        <section class="card overflow-hidden">
            <div class="bg-lyon-gradient px-6 py-6 text-white sm:px-8">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.svg') }}" alt="Logo Lyon Palme" class="h-8 w-auto" />
                    <div>
                        <h1 class="text-2xl font-black sm:text-3xl">Lyon Palme</h1>
                        <p class="text-sm text-white/90">Gestion des inscriptions et des adhésions</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 p-6 sm:grid-cols-2 sm:gap-6 sm:p-8">
                <a href="{{ route('adherent.login') }}" class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Choix 1</p>
                    <h2 class="mt-2 text-xl font-bold text-slate-900">Je suis adhérent</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        Consulter mon dossier, mon certificat médical et l'état de ma cotisation.
                    </p>
                    <p class="mt-4 font-semibold text-slate-800 group-hover:text-slate-900">Accéder à l'espace adhérent</p>
                </a>

                <a href="{{ route('login') }}" class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Choix 2</p>
                    <h2 class="mt-2 text-xl font-bold text-slate-900">Je suis secrétaire / admin</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        Gérer les adhérents, certificats, cotisations et paiements du club.
                    </p>
                    <p class="mt-4 font-semibold text-slate-800 group-hover:text-slate-900">Accéder à l'espace interne</p>
                </a>
            </div>
        </section>
    </main>
</body>
</html>
