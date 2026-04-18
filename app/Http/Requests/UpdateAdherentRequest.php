<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdherentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $adherentId = $this->route('adherent')?->id;
        $isMinor = $this->isMinor();

        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'date_naissance' => ['required', 'date', 'before:today'],
            'sexe' => ['nullable', Rule::in(['M', 'F', 'Autre'])],
            'email' => ['required', 'email', 'max:255', Rule::unique('adherents', 'email')->ignore($adherentId)],
            'adresse' => ['required', 'string', 'max:255'],
            'code_postal' => ['required', 'regex:/^[0-9]{5}$/'],
            'ville' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'contact_urgence_nom' => ['required', 'string', 'max:255'],
            'contact_urgence_telephone' => ['required', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'rgpd_accepte' => ['nullable', 'boolean'],
            'representant_nom' => [Rule::requiredIf($isMinor), 'nullable', 'string', 'max:255'],
            'representant_prenom' => [Rule::requiredIf($isMinor), 'nullable', 'string', 'max:255'],
            'representant_telephone' => ['nullable', 'string', 'max:20'],
            'representant_mobile' => ['nullable', 'string', 'max:20'],
            'representant_email' => ['nullable', 'email', 'max:255'],
            'representant_lien_parental' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'code_postal.regex' => 'Le code postal doit contenir 5 chiffres.',
            'representant_nom.required' => 'Le nom du representant legal est obligatoire pour un mineur.',
            'representant_prenom.required' => 'Le prenom du representant legal est obligatoire pour un mineur.',
        ];
    }

    private function isMinor(): bool
    {
        if (! $this->filled('date_naissance')) {
            return false;
        }

        try {
            return Carbon::parse($this->input('date_naissance'))->diffInYears(now()) < 18;
        } catch (\Throwable) {
            return false;
        }
    }
}
