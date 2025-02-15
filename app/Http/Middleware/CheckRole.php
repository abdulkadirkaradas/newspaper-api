<?php

namespace App\Http\Middleware;

use App\Enums\UserRoles;
use Closure;
use Illuminate\Http\Request;
use App\Helpers\CommonFunctions;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Check user role
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user;
        $userRole = UserRoles::getRole($user->role_id);

        if (!$user || !in_array($userRole, $roles)) {
            return CommonFunctions::response(FORBIDDEN, INVALID_ROLE);
        }

        return $next($request);
    }
}
