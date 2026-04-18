@extends('layouts.app')

@section('title', 'Certificats medicaux')

@section('content')
    <section class="mb-4 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Certificats medicaux</h1>
            <p class="text-sm text-slate-500">Tri par expiration croissante, filtres et export.</p>
        </div>

        <a href="{{ route('certificats.export', request()->query()) }}" class="btn-primary">Exporter Excel</a>
    </section>

    <section class="card mb-4 p-4">
        <form method="GET" class="grid gap-3 md:grid-cols-4">
            <select name="statut" class="input">
                <option value="">Tous statuts</option>
                <option value="valide" @selected(request('statut') === 'valide')>Valides</option>
                <option value="expire_bientot" @selected(request('statut') === 'expire_bientot')>Expire bientot</option>
                <option value="expire" @selected(request('statut') === 'expire')>Expires</option>
            </select>

            <div class="md:col-span-3 flex gap-2">
                <button type="submit" class="btn-primary">Filtrer</button>
                <a href="{{ route('certificats.index') }}" class="btn-secondary">Reset</a>
            </div>
        </form>
    </section>

    <section class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="px-4 py-3 text-left">Adherent</th>
                        <th class="px-4 py-3 text-left">Emission</th>
                        <th class="px-4 py-3 text-left">Expiration</th>
                        <th class="px-4 py-3 text-left">Jours restants</th>
                        <th class="px-4 py-3 text-left">Statut</th>
                        <th class="px-4 py-3 text-left">Questionnaire sante</th>
                        <th class="px-4 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($certificats as $certificat)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3 font-medium">{{ $certificat->adherent?->nom_complet ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $certificat->date_emission->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $certificat->date_expiration->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $certificat->jours_restants }}</td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $certificat->statut === 'valide' ? 'badge-success' : ($certificat->statut === 'expire_bientot' ? 'badge-warning' : 'badge-danger') }}">
                                    {{ str_replace('_', ' ', $certificat->statut) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $certificat->questionnaire_sante_requis ? 'Oui' : 'Non' }}</td>
                            <td class="px-4 py-3">
                                @if ($certificat->fichier)
                                    <a href="{{ route('certificats.download', $certificat) }}" class="btn-secondary">Telecharger</a>
                                @else
                                    <span class="text-xs text-slate-400">Aucun fichier</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">Aucun certificat trouve.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-4 py-3">
            {{ $certificats->links() }}
        </div>
    </section>
@endsection
