<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdherentRequest;
use App\Http\Requests\UpdateAdherentRequest;
use App\Models\Adherent;
use App\Models\RepresentantLegal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdherentController extends Controller
{
    public function index()
    {
        $query = Adherent::query()
            ->with(['representantLegal', 'dernierCertificat'])
            ->orderBy('nom');

        $statut = request('statut');
        if (in_array($statut, ['actif', 'archive'], true)) {
            $query->where('statut', $statut);
        }

        $age = request('age');
        if ($age === 'mineur') {
            $query->mineurs();
        } elseif ($age === 'majeur') {
            $query->majeurs();
        }

        $certificat = request('certificat');
        if ($certificat === 'valide') {
            $query->whereHas('certificatsMedicaux', function (Builder $builder) {
                $builder->whereDate('date_expiration', '>', now()->addDays(30)->toDateString());
            });
        } elseif ($certificat === 'expire_bientot') {
            $query->whereHas('certificatsMedicaux', function (Builder $builder) {
                $builder->whereBetween('date_expiration', [
                    now()->toDateString(),
                    now()->addDays(30)->toDateString(),
                ]);
            });
        } elseif ($certificat === 'expire') {
            $query->whereHas('certificatsMedicaux');
            $query->whereDoesntHave('certificatsMedicaux', function (Builder $builder) {
                $builder->whereDate('date_expiration', '>=', now()->toDateString());
            });
        }

        $query->recherche(request('search'));

        $sort = request('sort', 'nom');
        $direction = request('direction', 'asc') === 'desc' ? 'desc' : 'asc';
        $allowedSorts = ['nom', 'prenom', 'date_naissance', 'email', 'statut', 'created_at'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'nom';
        }

        $adherents = $query
            ->orderBy($sort, $direction)
            ->paginate(20)
            ->withQueryString();

        return view('adherents.index', compact('adherents'));
    }

    public function create()
    {
        return view('adherents.create');
    }

    public function store(StoreAdherentRequest $request)
    {
        $validated = $request->validated();
        $adherentData = $this->extractAdherentData($validated);
        $representantData = $this->extractRepresentantData($validated);
        $adherent = null;

        $adherentData['rgpd_accepte'] = true;
        $adherentData['rgpd_accepte_le'] = now();
        $adherentData['rgpd_ip'] = $request->ip();

        DB::transaction(function () use (&$adherent, $adherentData, $representantData, $request): void {
            $adherent = Adherent::create($adherentData);

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('photos', 'public');
                $adherent->update(['photo' => $path]);
            }

            $this->syncRepresentantLegal($adherent, $representantData);
        });

        return redirect()
            ->route('adherents.show', $adherent)
            ->with('success', 'Adherent cree avec succes.');
    }

    public function show(Adherent $adherent)
    {
        $adherent->load([
            'representantLegal',
            'certificatsMedicaux' => fn ($query) => $query->orderByDesc('date_emission'),
            'adhesions' => fn ($query) => $query
                ->with([
                    'saison',
                    'typeAdhesion',
                    'paiements' => fn ($payments) => $payments->orderByDesc('date_paiement'),
                ])
                ->orderByDesc('saison_id'),
        ]);

        return view('adherents.show', compact('adherent'));
    }

    public function edit(Adherent $adherent)
    {
        $adherent->load('representantLegal');

        return view('adherents.edit', compact('adherent'));
    }

    public function update(UpdateAdherentRequest $request, Adherent $adherent)
    {
        $validated = $request->validated();
        $adherentData = $this->extractAdherentData($validated);
        $representantData = $this->extractRepresentantData($validated);

        DB::transaction(function () use ($request, $adherent, $adherentData, $representantData): void {
            if ($request->boolean('rgpd_accepte') && ! $adherent->rgpd_accepte) {
                $adherentData['rgpd_accepte'] = true;
                $adherentData['rgpd_accepte_le'] = now();
                $adherentData['rgpd_ip'] = $request->ip();
            }

            $adherent->update($adherentData);

            if ($request->hasFile('photo')) {
                if ($adherent->photo) {
                    Storage::disk('public')->delete($adherent->photo);
                }

                $path = $request->file('photo')->store('photos', 'public');
                $adherent->update(['photo' => $path]);
            }

            $this->syncRepresentantLegal($adherent, $representantData);
        });

        return redirect()
            ->route('adherents.show', $adherent)
            ->with('success', 'Adherent modifie avec succes.');
    }

    public function destroy(Adherent $adherent)
    {
        $adherent->update([
            'statut' => 'archive',
            'archive_le' => now(),
        ]);

        return redirect()
            ->route('adherents.index')
            ->with('success', 'Adherent archive.');
    }

    public function restore(Adherent $adherent)
    {
        $adherent->update([
            'statut' => 'actif',
            'archive_le' => null,
        ]);

        return redirect()
            ->route('adherents.index')
            ->with('success', 'Adherent reactive.');
    }

    private function extractAdherentData(array $validated): array
    {
        return collect($validated)->except([
            'representant_nom',
            'representant_prenom',
            'representant_telephone',
            'representant_mobile',
            'representant_email',
            'representant_lien_parental',
            'photo',
            'rgpd_accepte',
        ])->toArray();
    }

    private function extractRepresentantData(array $validated): array
    {
        return [
            'nom' => $validated['representant_nom'] ?? null,
            'prenom' => $validated['representant_prenom'] ?? null,
            'telephone' => $validated['representant_telephone'] ?? null,
            'mobile' => $validated['representant_mobile'] ?? null,
            'email' => $validated['representant_email'] ?? null,
            'lien_parental' => $validated['representant_lien_parental'] ?? null,
        ];
    }

    private function syncRepresentantLegal(Adherent $adherent, array $representantData): void
    {
        if (! $adherent->estMineur()) {
            if ($adherent->representantLegal) {
                $representant = $adherent->representantLegal;
                $adherent->update(['representant_legal_id' => null]);
                $representant->delete();
            }
            return;
        }

        $cleaned = collect($representantData)
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->toArray();

        if (! $cleaned['nom']) {
            $cleaned['nom'] = 'A completer';
        }
        if (! $cleaned['prenom']) {
            $cleaned['prenom'] = 'A completer';
        }

        if ($adherent->representantLegal) {
            $adherent->representantLegal()->update($cleaned);
            return;
        }

        $representant = RepresentantLegal::create($cleaned);
        $adherent->update(['representant_legal_id' => $representant->id]);
    }
}
