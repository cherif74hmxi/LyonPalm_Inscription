<?php

namespace Database\Factories;

use App\Models\CertificatMedical;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CertificatMedical>
 */
class CertificatMedicalFactory extends Factory
{
    protected $model = CertificatMedical::class;

    public function definition(): array
    {
        $bucket = fake()->randomElement(['valide', 'expire_bientot', 'expire']);

        if ($bucket === 'valide') {
            $expiration = now()->addDays(fake()->numberBetween(31, 365));
        } elseif ($bucket === 'expire_bientot') {
            $expiration = now()->addDays(fake()->numberBetween(1, 30));
        } else {
            $expiration = now()->subDays(fake()->numberBetween(1, 120));
        }

        return [
            'date_emission' => $expiration->copy()->subYear()->subDays(fake()->numberBetween(0, 40)),
            'date_expiration' => $expiration,
            'fichier' => null,
            'restrictions' => null,
        ];
    }
}
