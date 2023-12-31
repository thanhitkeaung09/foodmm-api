<?php

declare(strict_types=1);

use Illuminate\Support\Str;

/**
 * Helper Functions
 */

if (!function_exists('generateAppId')) {
    function generateAppId(): string
    {
        return Str::uuid()->toString();
    }
}

if (!function_exists('generateAppSecrete')) {
    function generateAppSecrete(): string
    {
        return (string) (Str::uuid() . Str::uuid());
    }
}
