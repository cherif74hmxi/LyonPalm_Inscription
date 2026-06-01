<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

class ValidateCsrfToken extends PreventRequestForgery
{
    protected $addHttpCookie = false;
}
