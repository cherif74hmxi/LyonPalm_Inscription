@extends('layouts.auth')

@section('title', 'Connexion')
@section('panel_title', 'Espace secrétaire')
@section('panel_description', "Connexion sécurisée à l'application de gestion.")

@section('content')
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label class="label" for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="input" placeholder="secretaire@lyonpalme.com" />
        </div>

        <div>
            <label class="label" for="password">Mot de passe</label>
            <input id="password" type="password" name="password" required class="input" placeholder="Votre mot de passe" />
        </div>

        <div class="flex items-center justify-between gap-2 text-sm">
            <label class="flex items-center gap-2 text-slate-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300" {{ old('remember') ? 'checked' : '' }} />
                Se souvenir de moi
            </label>
            <span class="text-slate-400">Lyon Palme 2026</span>
        </div>

        <button type="submit" class="btn-primary w-full py-3">Se connecter</button>

        <p class="text-center text-sm text-slate-500">
            Vous êtes adhérent ?
            <a href="{{ route('adherent.login') }}" class="font-semibold text-slate-700 hover:text-slate-900">Accéder à mon espace</a>
        </p>
    </form>
@endsection
