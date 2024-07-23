<?php

namespace App\Http\Middleware;

use App\Helpers\CommonFunctions;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyImageUploadHeader
{
    /**
     * Verify a incoming request has 'ContentType: multipart/form-data' header
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!str_contains($request->header('Content-Type'), 'multipart/form-data')) {
            return response()->json(CommonFunctions::response(BAD_REQUEST, INCORRECT_IMAGE_UPLOAD_CONTENT_TYPE));
        }

        return $next($request);
    }
}
