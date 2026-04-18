<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AdhesionsExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(private readonly Collection $adhesions)
    {
    }

    public function collection(): Collection
    {
        return $this->adhesions->map(function ($adhesion) {
            $dernierPaiement = $adhesion->paiements->first();

            return [
                'adherent' => $adhesion->adherent?->nom_complet ?? '-',
                'saison' => $adhesion->saison?->nom ?? '-',
                'type' => $adhesion->typeAdhesion?->nom ?? '-',
                'montant_total' => number_format((float) $adhesion->montant_total, 2, '.', ''),
                'montant_paye' => number_format((float) $adhesion->montant_paye, 2, '.', ''),
                'solde' => number_format((float) $adhesion->solde, 2, '.', ''),
                'statut' => $adhesion->statut_paiement,
                'dernier_paiement' => $dernierPaiement?->date_paiement?->format('d/m/Y') ?? '-',
                'mode' => $dernierPaiement?->mode ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Adherent',
            'Saison',
            'Type adhesion',
            'Montant total',
            'Montant paye',
            'Solde',
            'Statut',
            'Dernier paiement',
            'Mode',
        ];
    }
}
