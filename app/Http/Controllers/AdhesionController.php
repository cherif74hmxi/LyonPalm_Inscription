<?php

namespace App\Http\Controllers;

use App\Exports\AdhesionsExport;
use App\Models\Adhesion;
use App\Models\Saison;
use App\Models\TypeAdhesion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdhesionController extends Controller
{
    public function index(Request $request)
    {
        $saisons = Saison::query()->orderByDesc('annee_debut')->get();
        $typesAdhesion = TypeAdhesion::query()->orderBy('nom')->get();
        $saisonActive = Saison::active()->first();

        $adhesions = $this->filteredQuery($request, $saisonActive?->id)
            ->paginate(20)
            ->withQueryString();

        return view('cotisations.index', [
            'adhesions' => $adhesions,
            'saisons' => $saisons,
            'typesAdhesion' => $typesAdhesion,
            'saisonActive' => $saisonActive,
        ]);
    }

    public function show(Adhesion $adhesion)
    {
        $adhesion->load([
            'adherent',
            'saison',
            'typeAdhesion',
            'paiements' => fn ($query) => $query->orderByDesc('date_paiement'),
        ]);

        return view('adhesions.show', compact('adhesion'));
    }

    public function export(Request $request)
    {
        $saisonActive = Saison::active()->first();
        $adhesions = $this->filteredQuery($request, $saisonActive?->id)->get();
        $filename = 'cotisations_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new AdhesionsExport($adhesions), $filename);
    }

    private function filteredQuery(Request $request, ?int $defaultSaisonId): Builder
    {
        $query = Adhesion::query()
            ->with([
                'adherent',
                'saison',
                'typeAdhesion',
                'paiements' => fn ($payments) => $payments->orderByDesc('date_paiement'),
            ]);

        $saisonId = $request->filled('saison_id')
            ? (int) $request->input('saison_id')
            : $defaultSaisonId;

        if ($saisonId) {
            $query->where('saison_id', $saisonId);
        }

        if ($request->filled('type_adhesion_id')) {
            $query->where('type_adhesion_id', (int) $request->input('type_adhesion_id'));
        }

        $statut = $request->string('statut')->toString();
        if ($statut === 'a_jour') {
            $query->whereRaw('montant_paye >= montant_total');
        } elseif ($statut === 'partiel') {
            $query->whereRaw('montant_paye > 0 AND montant_paye < montant_total');
        } elseif ($statut === 'impaye') {
            $query->whereRaw('montant_paye = 0');
        }

        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        if ($sort === 'nom') {
            $query->join('adherents', 'adherents.id', '=', 'adhesions.adherent_id')
                ->orderBy('adherents.nom', $direction)
                ->select('adhesions.*');
        } else {
            $query->orderByRaw('(montant_total - montant_paye) DESC');
        }

        return $query;
    }
}
