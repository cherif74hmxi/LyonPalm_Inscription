<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        $this->removeRedirectBody($response);
        $this->addSecurityHeaders($request, $response);
        $this->addPrivateCacheHeaders($request, $response);

        return $response;
    }

    private function removeRedirectBody(Response $response): void
    {
        if ($response->isRedirection()) {
            $response->setContent('');
        }
    }

    private function addSecurityHeaders(Request $request, Response $response): void
    {
        $headers = config('security.headers');

        if (data_get($headers, 'content_security_policy.enabled', true)) {
            $response->headers->set(
                'Content-Security-Policy',
                $this->buildContentSecurityPolicy(data_get($headers, 'content_security_policy.directives', [])),
            );
        }

        $this->setHeaderIfConfigured($response, 'X-Frame-Options', data_get($headers, 'x_frame_options'));
        $this->setHeaderIfConfigured($response, 'X-Content-Type-Options', data_get($headers, 'x_content_type_options'));
        $this->setHeaderIfConfigured($response, 'Referrer-Policy', data_get($headers, 'referrer_policy'));
        $this->setHeaderIfConfigured($response, 'Permissions-Policy', data_get($headers, 'permissions_policy'));
        $this->setHeaderIfConfigured($response, 'Cross-Origin-Embedder-Policy', data_get($headers, 'cross_origin_embedder_policy'));
        $this->setHeaderIfConfigured($response, 'Cross-Origin-Opener-Policy', data_get($headers, 'cross_origin_opener_policy'));
        $this->setHeaderIfConfigured($response, 'Cross-Origin-Resource-Policy', data_get($headers, 'cross_origin_resource_policy'));

        if ($request->isSecure() && data_get($headers, 'strict_transport_security.enabled', true)) {
            $response->headers->set('Strict-Transport-Security', $this->buildStrictTransportSecurityHeader());
        }
    }

    private function addPrivateCacheHeaders(Request $request, Response $response): void
    {
        if (! $request->isMethodCacheable() || $response->headers->hasCacheControlDirective('public')) {
            return;
        }

        $response->headers->set('Cache-Control', config('security.cache.private'));
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
    }

    private function setHeaderIfConfigured(Response $response, string $name, mixed $value): void
    {
        if (is_string($value) && $value !== '') {
            $response->headers->set($name, $value);
        }
    }

    private function buildContentSecurityPolicy(array $directives): string
    {
        return collect($directives)
            ->filter(fn (array|string|null $sources): bool => $sources !== null)
            ->map(function (array|string $sources, string $directive): string {
                $sources = array_values(array_filter((array) $sources));

                return trim($directive.' '.implode(' ', $sources));
            })
            ->implode('; ');
    }

    private function buildStrictTransportSecurityHeader(): string
    {
        $hsts = config('security.headers.strict_transport_security');
        $directives = ['max-age='.(int) $hsts['max_age']];

        if ($hsts['include_subdomains']) {
            $directives[] = 'includeSubDomains';
        }

        if ($hsts['preload']) {
            $directives[] = 'preload';
        }

        return implode('; ', $directives);
    }
}
