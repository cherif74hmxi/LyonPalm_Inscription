@extends('layouts.app')

@section('title', $adherent->nom_complet)

@section('content')
    <div class="space-y-4">
        <section class="card p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold">{{ $adherent->nom_complet }}</h1>
                    <p class="text-sm text-slate-500">Fiche adherent et historiques.</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('adherents.edit', $adherent) }}" class="btn-secondary">Modifier</a>

                    @if ($adherent->statut === 'actif')
                        <details class="relative">
                            <summary class="btn-primary cursor-pointer list-none">Archiver</summary>
                            <div class="absolute right-0 z-20 mt-2 w-80 max-w-[calc(100vw-2rem)] rounded-xl border border-slate-200 bg-white p-4 text-slate-900 shadow-2xl">
                                <h3 class="text-base font-bold">Confirmer archivage</h3>
                                <p class="mt-2 text-sm text-slate-600">Voulez-vous vraiment archiver cet adherent ? Aucune suppression definitive ne sera faite.</p>

                                <form method="POST" action="{{ route('adherents.destroy', $adherent) }}" class="mt-4 flex justify-end">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-primary">Confirmer</button>
                                </form>
                            </div>
                        </details>
                    @else
                        <form method="POST" action="{{ route('adherents.restore', $adherent) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-primary">Reactiver</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Email</p>
                    <p>{{ $adherent->email }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Telephone</p>
                    <p>{{ $adherent->mobile ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Date de naissance</p>
                    <p>{{ $adherent->date_naissance->format('d/m/Y') }} ({{ $adherent->age }} ans)</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Statut</p>
                    <span class="badge {{ $adherent->statut === 'actif' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($adherent->statut) }}</span>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Adresse</p>
                    <p>{{ $adherent->adresse }}, {{ $adherent->code_postal }} {{ $adherent->ville }}</p>
                </div>
            </div>

            @if ($adherent->representantLegal)
                <div class="mt-6 rounded-xl bg-slate-50 p-4">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-slate-500">Representant legal</h2>
                    <p class="font-medium">{{ $adherent->representantLegal->prenom }} {{ $adherent->representantLegal->nom }}</p>
                    <p class="text-sm text-slate-600">{{ $adherent->representantLegal->lien_parental ?: 'Lien non precise' }} - {{ $adherent->representantLegal->mobile ?: ($adherent->representantLegal->telephone ?: '-') }}</p>
                </div>
            @endif
        </section>

        <section class="card p-5">
            <h2 class="mb-3 text-lg font-semibold">Historique certificats</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="table-head">
                        <tr>
                            <th class="px-3 py-2 text-left">Emission</th>
                            <th class="px-3 py-2 text-left">Expiration</th>
                            <th class="px-3 py-2 text-left">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($adherent->certificatsMedicaux as $certificat)
                            <tr class="border-t border-slate-100">
                                <td class="px-3 py-2">{{ $certificat->date_emission->format('d/m/Y') }}</td>
                                <td class="px-3 py-2">{{ $certificat->date_expiration->format('d/m/Y') }}</td>
                                <td class="px-3 py-2">
                                    <span class="badge {{ $certificat->statut === 'valide' ? 'badge-success' : ($certificat->statut === 'expire_bientot' ? 'badge-warning' : 'badge-danger') }}">
                                        {{ str_replace('_', ' ', $certificat->statut) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="px-3 py-4 text-slate-500" colspan="3">Aucun certificat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card p-5">
            <h2 class="mb-3 text-lg font-semibold">Historique adhesions et paiements</h2>

            <div class="space-y-4">
                @forelse ($adherent->adhesions as $adhesion)
                    <article class="rounded-xl border border-slate-200 p-4">
                        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                            <p class="font-semibold">Saison {{ $adhesion->saison?->nom }} - {{ $adhesion->typeAdhesion?->nom }}</p>
                            <span class="badge {{ $adhesion->statut_paiement === 'a_jour' ? 'badge-success' : ($adhesion->statut_paiement === 'partiel' ? 'badge-warning' : 'badge-danger') }}">
                                {{ str_replace('_', ' ', $adhesion->statut_paiement) }}
                            </span>
                        </div>
                        <p class="text-sm text-slate-600">Total: {{ number_format((float) $adhesion->montant_total, 2, ',', ' ') }} EUR - Paye: {{ number_format((float) $adhesion->montant_paye, 2, ',', ' ') }} EUR - Solde: {{ number_format((float) $adhesion->solde, 2, ',', ' ') }} EUR</p>

                        @if ($adhesion->paiements->isNotEmpty())
                            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-slate-600">
                                @foreach ($adhesion->paiements as $paiement)
                                    <li>{{ $paiement->date_paiement->format('d/m/Y') }} - {{ number_format((float) $paiement->montant, 2, ',', ' ') }} EUR ({{ $paiement->mode }})</li>
                                @endforeach
                            </ul>
                        @endif
                    </article>
                @empty
                    <p class="text-sm text-slate-500">Aucune adhesion enregistree.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
