<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserAuthTokens;
use App\Helpers\CommonFunctions;
use Symfony\Component\HttpFoundation\Response;

class UserLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (str_contains($request->route()->getActionName(), 'refreshAuthToken') || str_contains($request->route()->getActionName(), 'logout')) {
            return $next($request);
        }

        $token = $request->bearerToken();

        $auth = UserAuthTokens::whereNull('deleted_at')->where('expired', '!=', true)->where('token', $token)->first();
        $user = User::find($auth->user_id);

        if (!$auth || !isset($user)) {
            return response()->json(CommonFunctions::response(UNAUTHORIZED, UNAUTHORIZED_ACCESS));
        }

        $request['user'] = $user;

        return $next($request);
    }
}
