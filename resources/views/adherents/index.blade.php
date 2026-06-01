@extends('layouts.app')

@section('title', 'Adhérents')

@section('content')
    <section class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Adhérents</h1>
            <p class="text-sm text-slate-500">Recherche, filtres, tri et archivage.</p>
        </div>
        <a href="{{ route('adherents.create') }}" class="btn-primary">+ Nouvel adhérent</a>
    </section>

    <section class="card mb-4 p-4">
        <form method="GET" class="grid gap-3 md:grid-cols-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, prénom ou email" class="input md:col-span-2" />

            <select name="statut" class="input">
                <option value="">Tous statuts</option>
                <option value="actif" @selected(request('statut') === 'actif')>Actifs</option>
                <option value="archive" @selected(request('statut') === 'archive')>Archivés</option>
            </select>

            <select name="age" class="input">
                <option value="">Mineur/Majeur</option>
                <option value="mineur" @selected(request('age') === 'mineur')>Mineurs</option>
                <option value="majeur" @selected(request('age') === 'majeur')>Majeurs</option>
            </select>

            <select name="certificat" class="input">
                <option value="">Certificat: tous</option>
                <option value="valide" @selected(request('certificat') === 'valide')>Valide</option>
                <option value="expire_bientot" @selected(request('certificat') === 'expire_bientot')>Expire bientôt</option>
                <option value="expire" @selected(request('certificat') === 'expire')>Expiré</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary w-full">Filtrer</button>
                <a href="{{ route('adherents.index') }}" class="btn-secondary">Réinitialiser</a>
            </div>

            <input type="hidden" name="sort" value="{{ request('sort', 'nom') }}" />
            <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}" />
        </form>
    </section>

    <section class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="px-4 py-3 text-left">Nom</th>
                        <th class="px-4 py-3 text-left">Âge</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Statut</th>
                        <th class="px-4 py-3 text-left">Certificat</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($adherents as $adherent)
                        @php $certificat = $adherent->dernierCertificat; @endphp
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3 font-semibold text-slate-700">{{ $adherent->nom_complet }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $adherent->age }} ans</td>
                            <td class="px-4 py-3 text-slate-600">{{ $adherent->email }}</td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $adherent->statut === 'actif' ? 'badge-success' : 'badge-muted' }}">
                                    {{ $adherent->statut === 'actif' ? 'Actif' : 'Archivé' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if (! $certificat)
                                    <span class="badge badge-muted">Aucun</span>
                                @elseif ($certificat->statut === 'valide')
                                    <span class="badge badge-success">Valide</span>
                                @elseif ($certificat->statut === 'expire_bientot')
                                    <span class="badge badge-warning">Expire bientôt</span>
                                @else
                                    <span class="badge badge-danger">Expiré</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('adherents.show', $adherent) }}" class="btn-secondary">Voir</a>
                                    <a href="{{ route('adherents.edit', $adherent) }}" class="btn-secondary">Modifier</a>

                                    @if ($adherent->statut === 'archive')
                                        <form method="POST" action="{{ route('adherents.restore', $adherent) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-primary">Réactiver</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Aucun adhérent trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-4 py-3">
            {{ $adherents->links() }}
        </div>
    </section>
@endsection
