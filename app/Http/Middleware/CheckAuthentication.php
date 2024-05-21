<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserAuthTokens;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthentication
{
    /**
     * Handle incoming authentication requests and check if the request auth tokens exist or have expired
     *
     * Expected behaviors;
     * - Check if token exists and not expired
     * - Check if auth token exists
     * - Check if auth token expired
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // This condition will be changed in the future
        // This condition currently passes '/register' route directly without checking any token existence
        // This condition passes requests if the incoming requests are for '/login' route
        if (!$request->bearerToken()
            && (str_contains($request->route()->getActionName(), 'register') || str_contains($request->route()->getActionName(), 'login'))) {
            return $next($request);
        }

        // Refuse request if it's not have 'Authorization' header
        if (!$request->bearerToken()) {
            return response()->json($this->errorMessage(UNAUTHORIZED, UNAUTHORIZED_ACCESS));
        }

        $authToken = $request->bearerToken();

        $auth = UserAuthTokens::where('token', $authToken)->first();
        if (!$auth) {
            return response()->json($this->errorMessage(UNAUTHORIZED, UNAUTHORIZED_ACCESS));
        }

        $user = Users::find($auth->user_id);

        $expireDateTS = strtotime($auth->expire_date);
        $currentTS = time();

        if (($expireDateTS < $currentTS) && !str_contains($request->route()->getActionName(), 'refreshAuthToken')) {
            return response()->json($this->errorMessage(UNAUTHORIZED, SESSION_EXPIRED));
        }

        $request['user'] = $user;

        return $next($request);
    }

    private function errorMessage(int $status, string $message): array
    {
        return [
            "status" => $status,
            "message" => $message
        ];
    }
}
