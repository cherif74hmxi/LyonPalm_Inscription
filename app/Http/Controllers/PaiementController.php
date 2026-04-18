<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaiementRequest;
use App\Models\Adhesion;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;

class PaiementController extends Controller
{
    public function create(Adhesion $adhesion)
    {
        $adhesion->load([
            'adherent',
            'saison',
            'typeAdhesion',
            'paiements' => fn ($query) => $query->orderByDesc('date_paiement'),
        ]);

        return view('paiements.create', [
            'adhesion' => $adhesion,
            'modes' => Paiement::MODES,
        ]);
    }

    public function store(StorePaiementRequest $request, Adhesion $adhesion)
    {
        $validated = $request->validated();
        $montant = min((float) $validated['montant'], (float) $adhesion->solde);

        if ($montant <= 0) {
            return back()->withErrors(['montant' => 'Aucun solde restant pour cette adhesion.']);
        }

        DB::transaction(function () use ($adhesion, $validated, $montant): void {
            Paiement::create([
                'adhesion_id' => $adhesion->id,
                'montant' => $montant,
                'mode' => $validated['mode'],
                'date_paiement' => $validated['date_paiement'],
                'remarques' => $validated['remarques'] ?? null,
            ]);

            $adhesion->update([
                'montant_paye' => (float) $adhesion->paiements()->sum('montant'),
            ]);
        });

        return redirect()
            ->route('adhesions.show', $adhesion)
            ->with('success', 'Paiement enregistre avec succes.');
    }
}
