@extends('layouts.app')

@section('title', 'Detail adhesion')

@section('content')
    <section class="mb-4 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Detail adhesion</h1>
            <p class="text-sm text-slate-500">{{ $adhesion->adherent?->nom_complet }} - Saison {{ $adhesion->saison?->nom }}</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('paiements.create', $adhesion) }}" class="btn-primary">Ajouter paiement</a>
            <a href="{{ route('adhesions.index') }}" class="btn-secondary">Retour</a>
        </div>
    </section>

    <section class="card mb-4 p-5">
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Type adhesion</p>
                <p>{{ $adhesion->typeAdhesion?->nom }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Montant total</p>
                <p>{{ number_format((float) $adhesion->montant_total, 2, ',', ' ') }} EUR</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Montant paye</p>
                <p>{{ number_format((float) $adhesion->montant_paye, 2, ',', ' ') }} EUR</p>
            </div>
        </div>

        <div class="mt-4">
            <span class="badge {{ $adhesion->statut_paiement === 'a_jour' ? 'badge-success' : ($adhesion->statut_paiement === 'partiel' ? 'badge-warning' : 'badge-danger') }}">
                {{ str_replace('_', ' ', $adhesion->statut_paiement) }}
            </span>
        </div>
    </section>

    <section class="card p-5">
        <h2 class="mb-3 text-lg font-semibold">Historique paiements</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="px-3 py-2 text-left">Date</th>
                        <th class="px-3 py-2 text-left">Montant</th>
                        <th class="px-3 py-2 text-left">Mode</th>
                        <th class="px-3 py-2 text-left">Remarques</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($adhesion->paiements as $paiement)
                        <tr class="border-t border-slate-100">
                            <td class="px-3 py-2">{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                            <td class="px-3 py-2">{{ number_format((float) $paiement->montant, 2, ',', ' ') }} EUR</td>
                            <td class="px-3 py-2">{{ $paiement->mode }}</td>
                            <td class="px-3 py-2">{{ $paiement->remarques ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-6 text-center text-slate-500">Aucun paiement enregistre.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
