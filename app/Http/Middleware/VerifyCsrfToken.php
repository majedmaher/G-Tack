<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Session;

class VerifyCsrfToken extends Middleware
{

    protected $except = [
        'api/*', // Skip CSRF token verification for all API routes
    ];
}
