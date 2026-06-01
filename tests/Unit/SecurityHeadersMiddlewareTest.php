<?php

namespace Tests\Unit;

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SecurityHeadersMiddlewareTest extends TestCase
{
    public function test_middleware_adds_configured_headers(): void
    {
        $middleware = app(SecurityHeaders::class);
        $request = Request::create('https://localhost/login', 'GET');

        $response = $middleware->handle($request, fn () => new Response('ok', 200, [
            'Cache-Control' => 'no-cache, private',
        ]));

        $this->assertSame('DENY', $response->headers->get('X-Frame-Options'));
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('same-origin', $response->headers->get('Cross-Origin-Opener-Policy'));
        $this->assertSame('same-origin', $response->headers->get('Cross-Origin-Resource-Policy'));
        $this->assertSame('require-corp', $response->headers->get('Cross-Origin-Embedder-Policy'));
        $this->assertSame('max-age=2592000', $response->headers->get('Strict-Transport-Security'));
        $this->assertStringContainsString("frame-ancestors 'none'", $response->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_middleware_removes_redirect_response_bodies(): void
    {
        $middleware = app(SecurityHeaders::class);
        $request = Request::create('https://localhost/login', 'POST');

        $response = $middleware->handle($request, fn () => new RedirectResponse('/login'));

        $this->assertSame('', $response->getContent());
        $this->assertTrue($response->isRedirection());
        $this->assertSame('/login', $response->headers->get('Location'));
    }
}
