<?php

namespace Tests\Feature;

use App\Models\Adherent;
use App\Models\Adhesion;
use App\Models\CertificatMedical;
use App\Models\Saison;
use App\Models\TypeAdhesion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_access_choices(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Je suis adhérent');
        $response->assertSee('Je suis secrétaire / admin');
    }

    public function test_authenticated_user_can_see_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Tableau de bord');
    }

    public function test_user_can_create_adherent(): void
    {
        $user = User::factory()->create();

        $payload = [
            'nom' => 'Martin',
            'prenom' => 'Lea',
            'date_naissance' => '2000-05-14',
            'sexe' => 'F',
            'email' => 'lea.martin@example.test',
            'adresse' => '12 rue de Lyon',
            'code_postal' => '69007',
            'ville' => 'Lyon',
            'telephone' => '0472000000',
            'mobile' => '0612000000',
            'contact_urgence_nom' => 'Marc Martin',
            'contact_urgence_telephone' => '0611000000',
            'rgpd_accepte' => 1,
        ];

        $response = $this->actingAs($user)
            ->withSession(['_token' => 'test-token'])
            ->post(route('adherents.store'), $payload + ['_token' => 'test-token']);

        $response->assertRedirect();
        $this->assertDatabaseHas('adherents', [
            'email' => 'lea.martin@example.test',
            'statut' => 'actif',
        ]);
    }

    public function test_user_can_view_certificats_page(): void
    {
        $user = User::factory()->create();
        $adherent = Adherent::factory()->create();

        CertificatMedical::create([
            'adherent_id' => $adherent->id,
            'date_emission' => now()->subMonths(6),
            'date_expiration' => now()->addMonths(6),
            'fichier' => null,
            'restrictions' => null,
        ]);

        $response = $this->actingAs($user)->get(route('certificats.index'));

        $response->assertOk();
        $response->assertSee('Certificats médicaux');
    }

    public function test_user_can_add_paiement(): void
    {
        $user = User::factory()->create();
        $adherent = Adherent::factory()->create();

        $saison = Saison::create([
            'annee_debut' => 2025,
            'annee_fin' => 2026,
            'active' => true,
        ]);

        $type = TypeAdhesion::create([
            'nom' => 'Adulte',
            'saison_id' => $saison->id,
        ]);

        $adhesion = Adhesion::create([
            'adherent_id' => $adherent->id,
            'saison_id' => $saison->id,
            'type_adhesion_id' => $type->id,
            'montant_total' => 250,
            'montant_paye' => 0,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['_token' => 'test-token'])
            ->post(route('paiements.store', $adhesion), [
                'montant' => 100,
                'mode' => 'Cheque',
                'date_paiement' => now()->format('Y-m-d'),
                'remarques' => 'Test',
                '_token' => 'test-token',
            ]);

        $response->assertRedirect(route('adhesions.show', $adhesion));
        $this->assertDatabaseHas('paiements', [
            'adhesion_id' => $adhesion->id,
            'mode' => 'Cheque',
        ]);

        $this->assertDatabaseHas('adhesions', [
            'id' => $adhesion->id,
            'montant_paye' => 100,
        ]);
    }

    public function test_adherent_can_login_and_see_his_space(): void
    {
        $adherent = Adherent::factory()->create([
            'email' => 'adherent.login@example.test',
            'password' => 'AdherentLyon2026!',
            'statut' => 'actif',
        ]);

        $loginResponse = $this
            ->withSession(['_token' => 'test-token'])
            ->post(route('adherent.login.store'), [
                '_token' => 'test-token',
                'email' => 'adherent.login@example.test',
                'password' => 'AdherentLyon2026!',
            ]);

        $loginResponse->assertRedirect(route('adherent.dashboard'));
        $this->assertAuthenticatedAs($adherent, 'adherent');

        $dashboardResponse = $this->get(route('adherent.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Espace adhérent');
    }
}
