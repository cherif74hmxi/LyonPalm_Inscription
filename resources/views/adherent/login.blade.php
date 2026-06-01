@extends('layouts.auth')

@section('title', 'Connexion adhérent')
@section('panel_title', 'Espace adhérent')
@section('panel_description', "Connexion à votre suivi personnel d'inscription.")

@section('content')
    <form method="POST" action="{{ route('adherent.login.store') }}" class="space-y-5">
        @csrf

        <div>
            <label class="label" for="email">Email adhérent</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="input" placeholder="adherent01@lyonpalme.test" />
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
            <span class="text-slate-400">Accès adhérent</span>
        </div>

        <button type="submit" class="btn-primary w-full py-3">Se connecter à mon espace</button>

        <p class="text-center text-sm text-slate-500">
            Vous êtes secrétaire/admin ?
            <a href="{{ route('login') }}" class="font-semibold text-slate-700 hover:text-slate-900">Connexion interne</a>
        </p>
    </form>
@endsection
