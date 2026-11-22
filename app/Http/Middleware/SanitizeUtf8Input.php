<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Replaces invalid UTF-8 byte sequences in request input so malformed submissions
 * are stored safely instead of causing database errors.
 */
class SanitizeUtf8Input
{
    public function handle(Request $request, Closure $next): Response
    {
        $clean = function ($value) use (&$clean) {
            if (is_array($value)) {
                return array_map($clean, $value);
            }

            return is_string($value) && ! mb_check_encoding($value, 'UTF-8')
                ? mb_scrub($value, 'UTF-8')
                : $value;
        };

        $request->merge($clean($request->except(array_keys($request->allFiles()))));

        return $next($request);
    }
}
