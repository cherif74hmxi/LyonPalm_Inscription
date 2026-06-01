<?php

namespace Tests\Unit;

use App\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use ReflectionProperty;
use Tests\TestCase;

class ValidateCsrfTokenMiddlewareTest extends TestCase
{
    public function test_csrf_middleware_keeps_validation_but_does_not_emit_xsrf_cookie(): void
    {
        $middleware = app(ValidateCsrfToken::class);
        $property = new ReflectionProperty(PreventRequestForgery::class, 'addHttpCookie');
        $property->setAccessible(true);

        $this->assertInstanceOf(PreventRequestForgery::class, $middleware);
        $this->assertFalse($property->getValue($middleware));
    }
}
