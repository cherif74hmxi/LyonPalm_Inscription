@extends('layouts.app')

@section('title', 'Nouvel adhérent')

@section('content')
    <section class="mb-4">
        <h1 class="text-2xl font-bold">Nouvel adhérent</h1>
        <p class="text-sm text-slate-500">Formulaire unique avec sections groupées.</p>
    </section>

    @include('adherents._form', [
        'action' => route('adherents.store'),
        'submitLabel' => 'Créer l\'adhérent',
    ])
@endsection
