<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'matran/*/export-download-full',
        'matran/*/export-download-simple',
        'trich-xuat-de-thi/*/download-full',
        'trich-xuat-de-thi/*/download-simple'
    ];
} 