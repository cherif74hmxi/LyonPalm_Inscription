<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'string',
                'min:12',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[^a-zA-Z0-9]/',
                'confirmed',
                'different:current_password',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.min' => 'Le nouveau mot de passe doit contenir au moins 12 caracteres.',
            'password.regex' => 'Le mot de passe doit contenir majuscule, minuscule, chiffre et symbole.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.different' => 'Le nouveau mot de passe doit etre different de l ancien.',
        ];
    }
}
