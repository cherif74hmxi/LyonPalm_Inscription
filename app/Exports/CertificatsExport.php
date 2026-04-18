<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CertificatsExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(private readonly Collection $certificats)
    {
    }

    public function collection(): Collection
    {
        return $this->certificats->map(function ($certificat) {
            return [
                'adherent' => $certificat->adherent?->nom_complet ?? '-',
                'date_emission' => optional($certificat->date_emission)->format('d/m/Y'),
                'date_expiration' => optional($certificat->date_expiration)->format('d/m/Y'),
                'jours_restants' => $certificat->jours_restants,
                'statut' => $certificat->statut,
                'questionnaire_sante_requis' => $certificat->questionnaire_sante_requis ? 'Oui' : 'Non',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Adherent',
            'Date emission',
            'Date expiration',
            'Jours restants',
            'Statut',
            'Questionnaire sante requis',
        ];
    }
}
