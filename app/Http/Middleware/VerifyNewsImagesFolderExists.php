<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class VerifyNewsImagesFolderExists
{
    /**
     * Check 'images' folder exists in the public folder and if not exists make directory
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $imageFolderPath = public_path('images');

        if (!File::exists($imageFolderPath)) {
            File::makeDirectory($imageFolderPath, 0755, true);
        }

        return $next($request);
    }
}
