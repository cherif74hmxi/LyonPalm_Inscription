@extends('layouts.app')

@section('title', 'Cotisations')

@section('content')
    <section class="mb-4 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Cotisations</h1>
            <p class="text-sm text-slate-500">Suivi des adhésions et paiements par saison.</p>
        </div>

        <a href="{{ route('adhesions.export', request()->query()) }}" class="btn-primary">Exporter Excel</a>
    </section>

    <section class="card mb-4 p-4">
        <form method="GET" class="grid gap-3 md:grid-cols-5">
            <select name="saison_id" class="input">
                <option value="">Saison active (défaut)</option>
                @foreach ($saisons as $saison)
                    <option value="{{ $saison->id }}" @selected((string) request('saison_id', $saisonActive?->id) === (string) $saison->id)>
                        {{ $saison->nom }}{{ $saisonActive && $saison->id === $saisonActive->id ? ' (active)' : '' }}
                    </option>
                @endforeach
            </select>

            <select name="statut" class="input">
                <option value="">Tous statuts</option>
                <option value="a_jour" @selected(request('statut') === 'a_jour')>À jour</option>
                <option value="partiel" @selected(request('statut') === 'partiel')>Partiel</option>
                <option value="impaye" @selected(request('statut') === 'impaye')>Impayé</option>
            </select>

            <select name="type_adhesion_id" class="input">
                <option value="">Tous types</option>
                @foreach ($typesAdhesion as $type)
                    <option value="{{ $type->id }}" @selected((string) request('type_adhesion_id') === (string) $type->id)>{{ $type->nom }}</option>
                @endforeach
            </select>

            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="btn-primary">Filtrer</button>
                <a href="{{ route('adhesions.index') }}" class="btn-secondary">Réinitialiser</a>
            </div>
        </form>
    </section>

    <section class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="px-4 py-3 text-left">Adhérent</th>
                        <th class="px-4 py-3 text-left">Saison</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Total</th>
                        <th class="px-4 py-3 text-left">Payé</th>
                        <th class="px-4 py-3 text-left">Solde</th>
                        <th class="px-4 py-3 text-left">Dernier paiement</th>
                        <th class="px-4 py-3 text-left">Mode</th>
                        <th class="px-4 py-3 text-left">Statut</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($adhesions as $adhesion)
                        @php
                            $dernierPaiement = $adhesion->paiements->first();
                            $statutPaiementLabel = match ($adhesion->statut_paiement) {
                                'a_jour' => 'À jour',
                                'partiel' => 'Partiel',
                                'impaye' => 'Impayé',
                                default => ucfirst(str_replace('_', ' ', $adhesion->statut_paiement)),
                            };
                        @endphp
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3 font-medium">{{ $adhesion->adherent?->nom_complet ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $adhesion->saison?->nom ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $adhesion->typeAdhesion?->nom ?? '-' }}</td>
                            <td class="px-4 py-3">{{ number_format((float) $adhesion->montant_total, 2, ',', ' ') }} EUR</td>
                            <td class="px-4 py-3">{{ number_format((float) $adhesion->montant_paye, 2, ',', ' ') }} EUR</td>
                            <td class="px-4 py-3">{{ number_format((float) $adhesion->solde, 2, ',', ' ') }} EUR</td>
                            <td class="px-4 py-3">{{ $dernierPaiement?->date_paiement?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $dernierPaiement?->mode ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $adhesion->statut_paiement === 'a_jour' ? 'badge-success' : ($adhesion->statut_paiement === 'partiel' ? 'badge-warning' : 'badge-danger') }}">
                                    {{ $statutPaiementLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('adhesions.show', $adhesion) }}" class="btn-secondary">Voir</a>
                                    <a href="{{ route('paiements.create', $adhesion) }}" class="btn-primary">+ Paiement</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-slate-500">Aucune cotisation trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-4 py-3">
            {{ $adhesions->links() }}
        </div>
    </section>
@endsection
