@extends('layouts.app')

@section('title', 'Ajouter un paiement')

@section('content')
    <section class="mb-4">
        <h1 class="text-2xl font-bold">Ajouter un paiement</h1>
        <p class="text-sm text-slate-500">{{ $adhesion->adherent?->nom_complet }} - {{ $adhesion->saison?->nom }} - {{ $adhesion->typeAdhesion?->nom }}</p>
    </section>

    <section class="card mb-4 p-5">
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total</p>
                <p>{{ number_format((float) $adhesion->montant_total, 2, ',', ' ') }} EUR</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Payé</p>
                <p>{{ number_format((float) $adhesion->montant_paye, 2, ',', ' ') }} EUR</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Solde restant</p>
                <p class="font-semibold">{{ number_format((float) $adhesion->solde, 2, ',', ' ') }} EUR</p>
            </div>
        </div>
    </section>

    <section class="card max-w-2xl p-5">
        <form method="POST" action="{{ route('paiements.store', $adhesion) }}" class="space-y-4">
            @csrf

            <div>
                <label class="label" for="montant">Montant</label>
                <input id="montant" type="number" step="0.01" min="0.01" max="{{ number_format((float) $adhesion->solde, 2, '.', '') }}" name="montant" value="{{ old('montant') }}" class="input" required />
            </div>

            <div>
                <label class="label" for="mode">Mode</label>
                <select id="mode" name="mode" class="input" required>
                    <option value="">Choisir un mode</option>
                    @foreach ($modes as $mode)
                        <option value="{{ $mode }}" @selected(old('mode') === $mode)>{{ $mode }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label" for="date_paiement">Date de paiement</label>
                <input id="date_paiement" type="date" name="date_paiement" value="{{ old('date_paiement', now()->format('Y-m-d')) }}" class="input" required />
            </div>

            <div>
                <label class="label" for="remarques">Remarques</label>
                <textarea id="remarques" name="remarques" class="input" rows="3">{{ old('remarques') }}</textarea>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary">Enregistrer le paiement</button>
                <a href="{{ route('adhesions.show', $adhesion) }}" class="btn-secondary">Annuler</a>
            </div>
        </form>
    </section>
@endsection
