<?php

namespace App\Http\Controllers;

use App\Models\Adherent;
use App\Models\Adhesion;
use App\Models\CertificatMedical;
use App\Models\Saison;

class DashboardController extends Controller
{
    public function index()
    {
        $adherentsActifs = Adherent::actifs()->count();
        $certificatsExpireBientot = CertificatMedical::expireBientot()->count();
        $certificatsExpires = CertificatMedical::expires()->count();

        $saisonActive = Saison::active()->first();
        $cotisationsImpayees = 0;

        if ($saisonActive) {
            $cotisationsImpayees = Adhesion::where('saison_id', $saisonActive->id)
                ->whereRaw('montant_paye < montant_total')
                ->count();
        }

        return view('dashboard', [
            'adherentsActifs' => $adherentsActifs,
            'certificatsExpireBientot' => $certificatsExpireBientot,
            'certificatsExpires' => $certificatsExpires,
            'cotisationsImpayees' => $cotisationsImpayees,
            'saisonActive' => $saisonActive,
        ]);
    }
}
