<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_sent_on_public_pages(): void
    {
        foreach (['/', '/login', '/espace-adherent/login'] as $path) {
            $response = $this->get($path);

            $response->assertOk();
            $this->assertSecurityHeaders($response);
            $response->assertHeaderContains('Cache-Control', 'no-store');
        }
    }

    public function test_hsts_is_sent_for_https_requests(): void
    {
        $response = $this
            ->withServerVariables(['HTTPS' => 'on', 'SERVER_PORT' => 443])
            ->get('/login');

        $response->assertOk();
        $response->assertHeader('Strict-Transport-Security', 'max-age=2592000');
        $this->assertStringNotContainsString('includeSubDomains', $response->headers->get('Strict-Transport-Security'));
    }

    public function test_session_cookie_is_secure_httponly_and_samesite_on_https(): void
    {
        config([
            'session.secure' => true,
            'session.http_only' => true,
            'session.same_site' => 'lax',
        ]);

        $response = $this
            ->withServerVariables(['HTTPS' => 'on', 'SERVER_PORT' => 443])
            ->get('/login');

        $sessionCookie = $response->getCookie(config('session.cookie'), false);

        $this->assertNotNull($sessionCookie);
        $this->assertTrue($sessionCookie->isHttpOnly());
        $this->assertTrue($sessionCookie->isSecure());
        $this->assertSame('lax', strtolower((string) $sessionCookie->getSameSite()));
        $this->assertNull($response->getCookie('XSRF-TOKEN', false));
    }

    public function test_private_pages_are_not_cacheable(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertHeaderContains('Cache-Control', 'no-store');
        $response->assertHeader('Pragma', 'no-cache');
        $response->assertHeader('Expires', '0');
    }

    public function test_public_metadata_endpoints_are_accessible_cacheable_and_without_session_cookie(): void
    {
        foreach (['/robots.txt', '/sitemap.xml'] as $path) {
            $response = $this->get($path);

            $response->assertOk();
            $this->assertSecurityHeaders($response);
            $response->assertHeaderContains('Cache-Control', 'public');
            $this->assertNull($response->getCookie(config('session.cookie'), false));
        }
    }

    public function test_public_assets_exist_and_do_not_contain_sensitive_values(): void
    {
        $manifestPath = public_path('build/manifest.json');

        $this->assertFileExists(public_path('logo.svg'));
        $this->assertFileExists($manifestPath);

        $manifest = json_decode(file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);

        foreach (['resources/css/app.css', 'resources/js/app.js'] as $entry) {
            $this->assertArrayHasKey($entry, $manifest);
            $assetPath = public_path('build/'.$manifest[$entry]['file']);
            $this->assertFileExists($assetPath);

            $contents = strtolower(file_get_contents($assetPath));
            $this->assertStringNotContainsString('app_key', $contents);
            $this->assertStringNotContainsString('db_password', $contents);
            $this->assertStringNotContainsString('password=', $contents);
        }
    }

    private function assertSecurityHeaders($response): void
    {
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
        $response->assertHeader('Cross-Origin-Resource-Policy', 'same-origin');
        $response->assertHeaderContains('Permissions-Policy', 'camera=()');
        $response->assertHeaderContains('Content-Security-Policy', "default-src 'self'");
        $response->assertHeaderContains('Content-Security-Policy', "frame-ancestors 'none'");
        $response->assertHeaderContains('Content-Security-Policy', "script-src 'self'");
        $this->assertStringNotContainsString("'unsafe-inline'", $response->headers->get('Content-Security-Policy'));
    }
}
