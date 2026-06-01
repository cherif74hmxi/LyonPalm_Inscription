@extends('layouts.app')

@section('title', 'Changer mot de passe')

@section('content')
    <section class="card max-w-xl p-6">
        <h1 class="mb-5 text-xl font-bold">Changer le mot de passe</h1>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf

            <div>
                <label class="label" for="current_password">Mot de passe actuel</label>
                <input id="current_password" type="password" name="current_password" required class="input" />
            </div>

            <div>
                <label class="label" for="password">Nouveau mot de passe</label>
                <input id="password" type="password" name="password" required class="input" />
                <p class="mt-1 text-xs text-slate-500">Minimum 12 caractères avec majuscule, minuscule, chiffre et symbole.</p>
            </div>

            <div>
                <label class="label" for="password_confirmation">Confirmation</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required class="input" />
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-primary">Mettre à jour</button>
            </div>
        </form>
    </section>
@endsection
