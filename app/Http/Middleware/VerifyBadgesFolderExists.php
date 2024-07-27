<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class VerifyBadgesFolderExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $imageFolderPath = public_path('badges');

        if (!File::exists($imageFolderPath)) {
            File::makeDirectory($imageFolderPath, 0755, true);
        }

        return $next($request);
    }
}
