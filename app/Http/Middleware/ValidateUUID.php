<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\CommonFunctions;
use Symfony\Component\HttpFoundation\Response;

class ValidateUUID
{
    /**
     * This middleware mainly validates UUIDs from incoming requests
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Take specified parameters
        $params = $request->only(['userId', 'newsId', 'categoryId']);

        if (count($params) === 0) {
            return $next($request);
        }

        // Validate UUID parameters
        if (!CommonFunctions::validateUUID($params)) {
            return CommonFunctions::response(BAD_REQUEST, INVALID_ID_NO);
        }

        return $next($request);
    }
}