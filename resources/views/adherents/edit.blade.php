@extends('layouts.app')

@section('title', 'Modifier un adhérent')

@section('content')
    <section class="mb-4">
        <h1 class="text-2xl font-bold">Modifier {{ $adherent->nom_complet }}</h1>
        <p class="text-sm text-slate-500">Mise à jour des informations de l'adhérent.</p>
    </section>

    @include('adherents._form', [
        'action' => route('adherents.update', $adherent),
        'submitLabel' => 'Enregistrer les modifications',
        'adherent' => $adherent,
    ])
@endsection
