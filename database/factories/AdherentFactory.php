<?php

namespace Database\Factories;

use App\Models\Adherent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Adherent>
 */
class AdherentFactory extends Factory
{
    protected $model = Adherent::class;

    public function definition(): array
    {
        $isMinor = fake()->boolean(20);
        $birthDate = $isMinor
            ? fake()->dateTimeBetween('-17 years', '-8 years')
            : fake()->dateTimeBetween('-65 years', '-18 years');

        return [
            'nom' => fake('fr_FR')->lastName(),
            'prenom' => fake('fr_FR')->firstName(),
            'date_naissance' => $birthDate,
            'sexe' => fake()->randomElement(['M', 'F', 'Autre']),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'AdherentLyon2026!',
            'adresse' => fake('fr_FR')->streetAddress(),
            'code_postal' => fake('fr_FR')->numerify('#####'),
            'ville' => fake('fr_FR')->city(),
            'telephone' => fake('fr_FR')->phoneNumber(),
            'mobile' => fake('fr_FR')->phoneNumber(),
            'contact_urgence_nom' => fake('fr_FR')->name(),
            'contact_urgence_telephone' => fake('fr_FR')->phoneNumber(),
            'photo' => null,
            'statut' => 'actif',
            'rgpd_accepte' => true,
            'rgpd_accepte_le' => now()->subDays(fake()->numberBetween(10, 600)),
            'rgpd_ip' => fake()->ipv4(),
            'archive_le' => null,
            'representant_legal_id' => null,
        ];
    }

    public function archive(): static
    {
        return $this->state(fn () => [
            'statut' => 'archive',
            'archive_le' => now()->subDays(fake()->numberBetween(1, 300)),
        ]);
    }
}
