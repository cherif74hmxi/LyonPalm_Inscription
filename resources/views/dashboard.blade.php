@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold">Tableau de bord</h1>
        <p class="text-sm text-slate-500">Vue rapide de la saison {{ $saisonActive?->nom ?? 'non définie' }}.</p>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="card p-5">
            <p class="text-sm text-slate-500">Adhérents actifs</p>
            <p class="kpi-value mt-2">{{ $adherentsActifs }}</p>
        </article>

        <article class="card p-5">
            <p class="text-sm text-slate-500">Certificats expirant bientôt</p>
            <p class="kpi-value mt-2">{{ $certificatsExpireBientot }}</p>
        </article>

        <article class="card p-5">
            <p class="text-sm text-slate-500">Certificats expirés</p>
            <p class="kpi-value mt-2">{{ $certificatsExpires }}</p>
        </article>

        <article class="card p-5">
            <p class="text-sm text-slate-500">Cotisations impayées</p>
            <p class="kpi-value mt-2">{{ $cotisationsImpayees }}</p>
        </article>
    </section>
@endsection
