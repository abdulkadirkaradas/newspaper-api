<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\CommonFunctions;
use Symfony\Component\HttpFoundation\Response;

class ValidateUUID
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->input('id');

        if (!CommonFunctions::validateUUID($id)) {
            return response()
                ->json(CommonFunctions::response(BAD_REQUEST, INVALID_ID_NO));
        }

        return $next($request);
    }
}