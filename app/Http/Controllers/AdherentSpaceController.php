<?php

namespace App\Http\Controllers;

use App\Models\Adhesion;
use Illuminate\Support\Facades\Auth;

class AdherentSpaceController extends Controller
{
    public function dashboard()
    {
        $adherent = Auth::guard('adherent')->user();

        $certificat = $adherent->certificatsMedicaux()
            ->orderByDesc('date_expiration')
            ->first();

        $adhesionActive = Adhesion::with(['saison', 'typeAdhesion', 'paiements'])
            ->where('adherent_id', $adherent->id)
            ->whereHas('saison', fn ($query) => $query->where('active', true))
            ->first();

        $adhesionsRecentes = Adhesion::with(['saison', 'typeAdhesion'])
            ->where('adherent_id', $adherent->id)
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        return view('adherent.dashboard', [
            'adherent' => $adherent,
            'certificat' => $certificat,
            'adhesionActive' => $adhesionActive,
            'adhesionsRecentes' => $adhesionsRecentes,
        ]);
    }
}
