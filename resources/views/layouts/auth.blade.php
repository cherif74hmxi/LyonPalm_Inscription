<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Connexion') - Lyon Palme</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-shell flex items-center justify-center py-6">
    <div class="auth-grid">
        <aside class="auth-brand">
            <div class="auth-brand-content">
                <div class="auth-logo-wrap">
                    <img src="{{ asset('logo.svg') }}" alt="Logo Lyon Palme" />
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-100/90">Monopalme Lyon</p>
                        <p class="text-sm text-white/85">Saint-Fons - Vénissieux</p>
                    </div>
                </div>

                <h1 class="auth-title">Lyon Palme</h1>
                <p class="auth-tagline">
                    Bienvenue sur l'espace interne du club.
                    Une interface simple pour gérer les inscriptions, certificats médicaux et cotisations.
                </p>

                <div class="auth-badges">
                    <span class="auth-pill">FFESSM</span>
                    <span class="auth-pill">Monopalme</span>
                    <span class="auth-pill">Club Lyonnais</span>
                </div>

                <p class="auth-quote">
                    Toute l'actu du club en direct sur les réseaux sociaux et dans les bassins.
                </p>
            </div>
        </aside>

        <section class="auth-panel">
                <div class="mb-6 flex items-center gap-3">
                    <img src="{{ asset('logo.svg') }}" alt="Logo Lyon Palme" class="h-10 w-10 rounded-xl border border-slate-200 bg-white p-1" />
                    <div>
                        <h2>@yield('panel_title', 'Espace secrétaire')</h2>
                        <p>@yield('panel_description', "Connexion sécurisée à l'application de gestion.")</p>
                    </div>
                </div>

            @include('partials.alerts')

            @yield('content')
        </section>
    </div>
</body>
</html>
