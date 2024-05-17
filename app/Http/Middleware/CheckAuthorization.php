<?php

namespace App\Http\Middleware;

use App\Models\UserAuthTokens;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthorization
{
    /**
     * Handle incoming authorization requests.
     *
     * Expected behaviours;
     * - Check user role
     * - Store user role for the processes
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

        $request['user'] = $user;

        return $next($request);
    }
}
