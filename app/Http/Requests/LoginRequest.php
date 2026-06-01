<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ThrottlesLoginAttempts;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    use ThrottlesLoginAttempts;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'L email est obligatoire.',
            'email.email' => 'Le format de l email est invalide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ];
    }
}
