<?php

namespace App\Http\Middleware;

use App\Helpers\CommonFunctions;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleAdminMiddleware
{
    /**
     * Check if user have 'Adminisitrator' role.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user;

        if (!$user->isAdministrator()) {
            return response()->json(CommonFunctions::response(UNAUTHORIZED, UNAUTHORIZED_ACCESS));
        }

        return $next($request);
    }
}
