<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdherentLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdherentLoginController extends Controller
{
    public function show()
    {
        if (Auth::guard('adherent')->check()) {
            return redirect()->route('adherent.dashboard');
        }

        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }

        return view('adherent.login');
    }

    public function store(AdherentLoginRequest $request)
    {
        $request->ensureIsNotRateLimited();

        $credentials = $request->safe()->only(['email', 'password']);
        $credentials['statut'] = 'actif';

        if (! Auth::guard('adherent')->attempt($credentials, $request->boolean('remember'))) {
            $request->hitRateLimiter();

            return back()
                ->withErrors(['email' => 'Identifiants invalides.'])
                ->onlyInput('email');
        }

        $request->clearRateLimiter();
        $request->session()->regenerate();

        return redirect()->route('adherent.dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::guard('adherent')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('adherent.login')->with('success', 'Vous etes deconnecte de votre espace adherent.');
    }
}
