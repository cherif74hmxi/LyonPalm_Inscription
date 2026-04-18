@extends('layouts.app')

@section('title', 'Nouvel adherent')

@section('content')
    <section class="mb-4">
        <h1 class="text-2xl font-bold">Nouvel adherent</h1>
        <p class="text-sm text-slate-500">Formulaire unique avec sections groupees.</p>
    </section>

    @include('adherents._form', [
        'action' => route('adherents.store'),
        'submitLabel' => 'Creer adherent',
    ])
@endsection
