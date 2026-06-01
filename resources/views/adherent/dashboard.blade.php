@extends('layouts.adherent')

@section('title', 'Mon espace')

@php
    $statutCertificat = $certificat?->statut ?? 'absent';
    $badgeCertificat = match ($statutCertificat) {
        'valide' => 'badge-success',
        'expire_bientot' => 'badge-warning',
        'expire' => 'badge-danger',
        default => 'badge-muted',
    };
    $statutCertificatLabel = match ($statutCertificat) {
        'valide' => 'Valide',
        'expire_bientot' => 'Expire bientôt',
        'expire' => 'Expiré',
        'absent' => 'Absent',
        default => ucfirst(str_replace('_', ' ', $statutCertificat)),
    };
@endphp

@section('content')
    <section class="mb-6 card p-5">
        <h1 class="text-2xl font-bold">Bienvenue {{ $adherent->nom_complet }}</h1>
        <p class="mt-1 text-sm text-slate-500">
            Retrouvez ici votre situation d'inscription, votre certificat médical et vos paiements.
        </p>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <article class="card p-5">
            <p class="text-sm text-slate-500">Statut du certificat</p>
            <div class="mt-2 flex items-center gap-2">
                <span class="badge {{ $badgeCertificat }}">
                    {{ $statutCertificatLabel }}
                </span>
            </div>
            @if ($certificat)
                <p class="mt-3 text-sm text-slate-600">Expiration : {{ $certificat->date_expiration->format('d/m/Y') }}</p>
                <p class="text-sm text-slate-600">Jours restants : {{ $certificat->jours_restants }}</p>
            @else
                <p class="mt-3 text-sm text-slate-600">Aucun certificat enregistré.</p>
            @endif
        </article>

        <article class="card p-5">
            <p class="text-sm text-slate-500">Cotisation saison active</p>
            @if ($adhesionActive)
                <p class="mt-2 text-xl font-bold text-slate-900">{{ $adhesionActive->saison->nom }}</p>
                <p class="mt-2 text-sm text-slate-600">Type : {{ $adhesionActive->typeAdhesion->nom }}</p>
                <p class="text-sm text-slate-600">Total : {{ number_format((float) $adhesionActive->montant_total, 2, ',', ' ') }} EUR</p>
                <p class="text-sm text-slate-600">Payé : {{ number_format((float) $adhesionActive->montant_paye, 2, ',', ' ') }} EUR</p>
                <p class="text-sm font-semibold text-slate-800">Solde : {{ number_format((float) $adhesionActive->solde, 2, ',', ' ') }} EUR</p>
            @else
                <p class="mt-2 text-sm text-slate-600">Aucune adhésion active pour le moment.</p>
            @endif
        </article>

        <article class="card p-5">
            <p class="text-sm text-slate-500">Mes informations</p>
            <p class="mt-2 text-sm text-slate-700">{{ $adherent->email }}</p>
            <p class="text-sm text-slate-700">{{ $adherent->telephone ?: $adherent->mobile ?: 'Téléphone non renseigné' }}</p>
            <p class="text-sm text-slate-700">{{ $adherent->adresse }}, {{ $adherent->code_postal }} {{ $adherent->ville }}</p>
        </article>
    </section>

    <section class="mt-6 grid gap-4 lg:grid-cols-2">
        <article class="card overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-lg font-bold text-slate-900">Derniers paiements</h2>
            </div>
            <div class="p-5">
                @if ($adhesionActive && $adhesionActive->paiements->isNotEmpty())
                    <ul class="space-y-2 text-sm text-slate-700">
                        @foreach ($adhesionActive->paiements->sortByDesc('date_paiement') as $paiement)
                            <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                                <span>{{ $paiement->date_paiement->format('d/m/Y') }} - {{ $paiement->mode }}</span>
                                <span class="font-semibold">{{ number_format((float) $paiement->montant, 2, ',', ' ') }} EUR</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-600">Aucun paiement enregistré sur la saison active.</p>
                @endif
            </div>
        </article>

        <article class="card overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-lg font-bold text-slate-900">Mes adhésions récentes</h2>
            </div>
            <div class="p-5">
                @if ($adhesionsRecentes->isNotEmpty())
                    <ul class="space-y-2 text-sm text-slate-700">
                        @foreach ($adhesionsRecentes as $adhesion)
                            <li class="rounded-lg bg-slate-50 px-3 py-2">
                                <p class="font-semibold">{{ $adhesion->saison->nom }}</p>
                                <p>Type : {{ $adhesion->typeAdhesion->nom }}</p>
                                <p>Solde : {{ number_format((float) $adhesion->solde, 2, ',', ' ') }} EUR</p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-600">Aucune adhésion dans l'historique.</p>
                @endif
            </div>
        </article>
    </section>
@endsection
