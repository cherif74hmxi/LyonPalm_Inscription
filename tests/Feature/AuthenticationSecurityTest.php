<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthenticationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_forms_include_csrf_tokens(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('name="_token"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false);

        $this->get('/espace-adherent/login')
            ->assertOk()
            ->assertSee('name="_token"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false);
    }

    public function test_invalid_internal_login_redirect_does_not_expose_sensitive_content(): void
    {
        $response = $this->from('/login')->post('/login', [
            '_token' => 'test-token',
            'email' => 'zaproxy@example.com',
            'password' => 'SecretPassword123!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email' => 'Identifiants invalides.']);
        $response->assertSessionHasInput('email', 'zaproxy@example.com');
        $response->assertSessionMissingInput(['password', '_token']);
        $this->assertSame('', $response->getContent());
        $this->assertStringNotContainsString('SecretPassword123!', $response->headers->get('Location'));
        $this->assertStringNotContainsString('zaproxy@example.com', $response->headers->get('Location'));
    }

    public function test_invalid_adherent_login_redirect_does_not_expose_sensitive_content(): void
    {
        $response = $this->from('/espace-adherent/login')->post(route('adherent.login.store'), [
            '_token' => 'test-token',
            'email' => 'zaproxy@example.com',
            'password' => 'SecretPassword123!',
        ]);

        $response->assertRedirect('/espace-adherent/login');
        $response->assertSessionHasErrors(['email' => 'Identifiants invalides.']);
        $response->assertSessionHasInput('email', 'zaproxy@example.com');
        $response->assertSessionMissingInput(['password', '_token']);
        $this->assertSame('', $response->getContent());
        $this->assertStringNotContainsString('SecretPassword123!', $response->headers->get('Location'));
        $this->assertStringNotContainsString('zaproxy@example.com', $response->headers->get('Location'));
    }

    public function test_login_validation_rejects_invalid_payloads(): void
    {
        $this->from('/login')->post('/login', [
            '_token' => 'test-token',
            'email' => 'not-an-email',
            'password' => '',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_rate_limiting_is_applied(): void
    {
        config([
            'security.login.max_attempts' => 2,
            'security.login.decay_seconds' => 60,
        ]);

        $email = 'limited-user@example.test';
        RateLimiter::clear(strtolower($email).'|203.0.113.51');

        for ($attempt = 0; $attempt < 2; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.51'])
                ->from('/login')
                ->post('/login', [
                    '_token' => 'test-token',
                    'email' => $email,
                    'password' => 'wrong-password',
                ])
                ->assertSessionHasErrors(['email' => 'Identifiants invalides.']);
        }

        $response = $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.51'])
            ->from('/login')
            ->post('/login', [
                '_token' => 'test-token',
                'email' => $email,
                'password' => 'wrong-password',
            ]);

        $response->assertSessionHasErrors(['email' => 'Trop de tentatives de connexion. Reessayez dans 60 secondes.']);
    }

    public function test_private_routes_redirect_guests_to_the_expected_login_page(): void
    {
        $this->get(route('dashboard'))->assertRedirect('/login');
        $this->get(route('adherent.dashboard'))->assertRedirect('/espace-adherent/login');
    }
}
