<?php

namespace Database\Seeders;

use App\Models\Adherent;
use App\Models\Adhesion;
use App\Models\CertificatMedical;
use App\Models\Paiement;
use App\Models\RepresentantLegal;
use App\Models\Saison;
use App\Models\TypeAdhesion;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'secretaire@lyonpalme.com'],
            [
                'name' => 'Secretaire Club',
                'password' => Hash::make('SecretLyon2026!'),
                'role' => 'secretaire',
                'password_changed_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@lyonpalme.com'],
            [
                'name' => 'Admin Club',
                'password' => Hash::make('AdminLyon2026!'),
                'role' => 'admin',
                'password_changed_at' => now(),
            ]
        );

        $saisonAncienne = Saison::updateOrCreate(
            ['annee_debut' => 2024, 'annee_fin' => 2025],
            ['active' => false]
        );

        $saisonActive = Saison::updateOrCreate(
            ['annee_debut' => 2025, 'annee_fin' => 2026],
            ['active' => true]
        );

        Saison::where('id', '!=', $saisonActive->id)->update(['active' => false]);

        $types = [
            'Adulte' => 250,
            'Junior' => 180,
            'Etudiant' => 200,
            'Enfant' => 150,
        ];

        foreach ([$saisonAncienne, $saisonActive] as $saison) {
            foreach ($types as $nom => $prix) {
                TypeAdhesion::updateOrCreate(
                    ['saison_id' => $saison->id, 'nom' => $nom],
                    ['nom' => $nom]
                );
            }
        }

        $faker = FakerFactory::create('fr_FR');

        for ($i = 1; $i <= 50; $i++) {
            $isMinor = $i <= 10;
            $isArchived = $i > 40;

            $dateNaissance = $isMinor
                ? now()->subYears($faker->numberBetween(8, 17))->subDays($faker->numberBetween(0, 360))
                : now()->subYears($faker->numberBetween(18, 65))->subDays($faker->numberBetween(0, 360));

            $adherent = Adherent::updateOrCreate(
                ['email' => sprintf('adherent%02d@lyonpalme.test', $i)],
                [
                    'nom' => $faker->lastName(),
                    'prenom' => $faker->firstName(),
                    'date_naissance' => $dateNaissance,
                    'sexe' => $faker->randomElement(['M', 'F', 'Autre']),
                    'password' => 'AdherentLyon2026!',
                    'adresse' => $faker->streetAddress(),
                    'code_postal' => $faker->numerify('#####'),
                    'ville' => $faker->city(),
                    'telephone' => $faker->phoneNumber(),
                    'mobile' => $faker->phoneNumber(),
                    'contact_urgence_nom' => $faker->name(),
                    'contact_urgence_telephone' => $faker->phoneNumber(),
                    'statut' => $isArchived ? 'archive' : 'actif',
                    'rgpd_accepte' => true,
                    'rgpd_accepte_le' => now()->subDays($faker->numberBetween(20, 700)),
                    'rgpd_ip' => $faker->ipv4(),
                    'archive_le' => $isArchived ? now()->subDays($faker->numberBetween(1, 180)) : null,
                ]
            );

            if ($isMinor) {
                $representant = RepresentantLegal::updateOrCreate(
                    ['email' => sprintf('representant%02d@lyonpalme.test', $i)],
                    [
                        'nom' => $faker->lastName(),
                        'prenom' => $faker->firstName(),
                        'telephone' => $faker->phoneNumber(),
                        'mobile' => $faker->phoneNumber(),
                        'lien_parental' => $faker->randomElement(['Pere', 'Mere', 'Tuteur']),
                    ]
                );

                if ($adherent->representant_legal_id !== $representant->id) {
                    $adherent->update(['representant_legal_id' => $representant->id]);
                }
            } elseif ($adherent->representant_legal_id) {
                $adherent->update(['representant_legal_id' => null]);
            }

            $bucket = $i % 3;
            if ($bucket === 0) {
                $expiration = now()->subDays($faker->numberBetween(1, 90));
            } elseif ($bucket === 1) {
                $expiration = now()->addDays($faker->numberBetween(1, 30));
            } else {
                $expiration = now()->addDays($faker->numberBetween(31, 365));
            }

            CertificatMedical::updateOrCreate(
                ['adherent_id' => $adherent->id],
                [
                    'date_emission' => $expiration->copy()->subYear()->subDays($faker->numberBetween(0, 30)),
                    'date_expiration' => $expiration,
                    'fichier' => null,
                    'restrictions' => null,
                ]
            );

            if ($isArchived) {
                continue;
            }

            $typeNom = $isMinor
                ? $faker->randomElement(['Enfant', 'Junior'])
                : $faker->randomElement(['Adulte', 'Etudiant']);
            $type = TypeAdhesion::where('saison_id', $saisonActive->id)->where('nom', $typeNom)->firstOrFail();
            $montantTotal = $types[$typeNom];

            $paymentPattern = $i % 3;
            if ($paymentPattern === 0) {
                $montantPaye = 0;
            } elseif ($paymentPattern === 1) {
                $montantPaye = round($montantTotal * 0.5, 2);
            } else {
                $montantPaye = (float) $montantTotal;
            }

            $adhesion = Adhesion::updateOrCreate(
                ['adherent_id' => $adherent->id, 'saison_id' => $saisonActive->id],
                [
                    'type_adhesion_id' => $type->id,
                    'montant_total' => $montantTotal,
                    'montant_paye' => $montantPaye,
                ]
            );

            $adhesion->paiements()->delete();

            if ($montantPaye <= 0) {
                continue;
            }

            if ($montantPaye >= $montantTotal) {
                Paiement::create([
                    'adhesion_id' => $adhesion->id,
                    'montant' => $montantTotal,
                    'mode' => $faker->randomElement(Paiement::MODES),
                    'date_paiement' => now()->subDays($faker->numberBetween(1, 90)),
                ]);
                continue;
            }

            $first = round($montantPaye / 2, 2);
            $second = round($montantPaye - $first, 2);

            Paiement::create([
                'adhesion_id' => $adhesion->id,
                'montant' => $first,
                'mode' => $faker->randomElement(Paiement::MODES),
                'date_paiement' => now()->subDays($faker->numberBetween(20, 90)),
            ]);

            Paiement::create([
                'adhesion_id' => $adhesion->id,
                'montant' => $second,
                'mode' => $faker->randomElement(Paiement::MODES),
                'date_paiement' => now()->subDays($faker->numberBetween(1, 19)),
            ]);
        }
    }
}
