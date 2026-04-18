@extends('layouts.app')

@section('title', 'Modifier adherent')

@section('content')
    <section class="mb-4">
        <h1 class="text-2xl font-bold">Modifier {{ $adherent->nom_complet }}</h1>
        <p class="text-sm text-slate-500">Mise a jour des informations adherent.</p>
    </section>

    @include('adherents._form', [
        'action' => route('adherents.update', $adherent),
        'submitLabel' => 'Enregistrer modifications',
        'adherent' => $adherent,
    ])
@endsection
