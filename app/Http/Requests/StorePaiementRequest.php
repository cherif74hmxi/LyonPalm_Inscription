<?php

namespace App\Http\Requests;

use App\Models\Paiement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $adhesion = $this->route('adhesion');
        $solde = $adhesion ? (float) $adhesion->solde : 0.0;

        return [
            'montant' => [
                'required',
                'numeric',
                'min:0.01',
                function (string $attribute, mixed $value, \Closure $fail) use ($solde) {
                    if ((float) $value > $solde) {
                        $fail('Le montant ne peut pas depasser le solde restant.');
                    }
                },
            ],
            'mode' => ['required', Rule::in(Paiement::MODES)],
            'date_paiement' => ['required', 'date', 'before_or_equal:today'],
            'remarques' => ['nullable', 'string', 'max:500'],
        ];
    }
}
