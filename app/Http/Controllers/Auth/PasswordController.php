<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(ChangePasswordRequest $request)
    {
        $request->user()->update([
            'password' => Hash::make($request->string('password')->toString()),
            'password_changed_at' => now(),
            'password_expires_at' => now()->addDays(90),
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Mot de passe mis a jour avec succes.');
    }
}
