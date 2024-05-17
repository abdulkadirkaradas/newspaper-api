<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserAuthTokens;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthentication
{
    /**
     * Handle incoming authentication requests and check if the request auth tokens exist or have expired
     *
     * Expected behaviors;
     * - Check if user exists in the database
     * - Check if auth token exists
     * - Check if auth token expired
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->header('auth-token')) {
            return response()->json([
                'status' => UNAUTHORIZED,
                'message' => UNAUTHORIZED_ACCESS
            ]);
        }

        $authToken = $request->header('auth-token');

        $user = UserAuthTokens::where('token', $authToken)->first();

        if (!$user) {
            return response()->json([
                'status' => UNAUTHORIZED,
                'message' => UNAUTHORIZED_ACCESS
            ]);
        }

        $expiredDate = strtotime($user->expired_date);
        $currentTS = strtotime(time());

        if ($expiredDate > $currentTS) {
            return response()->json([
                'status' => UNAUTHORIZED,
                'message' => SESSION_EXPIRED
            ]);
        }

        $request['user'] = $user;

        return $next($request);
    }
}
