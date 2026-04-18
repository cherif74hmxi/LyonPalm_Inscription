<?php

namespace App\Http\Controllers;

use App\Exports\CertificatsExport;
use App\Models\CertificatMedical;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CertificatMedicalController extends Controller
{
    public function index(Request $request)
    {
        $certificats = $this->filteredQuery($request)
            ->paginate(20)
            ->withQueryString();

        return view('certificats.index', compact('certificats'));
    }

    public function download(CertificatMedical $certificat)
    {
        if (! $certificat->fichier) {
            return back()->withErrors(['fichier' => 'Aucun fichier associe a ce certificat.']);
        }

        if (! Storage::disk('public')->exists($certificat->fichier)) {
            return back()->withErrors(['fichier' => 'Le fichier du certificat est introuvable.']);
        }

        $filename = 'certificat-'.$certificat->adherent_id.'-'.$certificat->date_expiration->format('Ymd').'.pdf';

        return Storage::disk('public')->download($certificat->fichier, $filename);
    }

    public function export(Request $request)
    {
        $certificats = $this->filteredQuery($request)->get();
        $filename = 'certificats_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new CertificatsExport($certificats), $filename);
    }

    private function filteredQuery(Request $request): Builder
    {
        $query = CertificatMedical::query()
            ->with('adherent')
            ->orderBy('date_expiration');

        $statut = $request->string('statut')->toString();
        if ($statut === 'valide') {
            $query->valides();
        } elseif ($statut === 'expire_bientot') {
            $query->expireBientot();
        } elseif ($statut === 'expire') {
            $query->expires();
        }

        return $query;
    }
}
