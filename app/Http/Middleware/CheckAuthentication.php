<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserAuthTokens;
use App\Models\User;
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
        // Refuse request if its not have bearer token
        if (!$request->bearerToken()) {
            return response()->json($this->errorMessage(UNAUTHORIZED, UNAUTHORIZED_ACCESS));
        }

        $authToken = $request->bearerToken();

        // Check if the user deactived or session expired
        $auth = UserAuthTokens::whereNull('deleted_at')->where('expired', '!=', true)->where('token', $authToken)->first();
        if (!$auth) {
            return response()->json($this->errorMessage(UNAUTHORIZED, UNAUTHORIZED_ACCESS));
        } else if ($auth->expired === true) {
            return response()->json($this->errorMessage(UNAUTHORIZED, SESSION_EXPIRED));
        }

        $user = User::find($auth->user_id);

        // Check if the user blocked
        if ($user->blocked === true) {
            return response()->json($this->errorMessage(FORBIDDEN, "User '{$user->username}' has been blocked!"));
        }

        $expireDateTS = strtotime($auth->expire_date);
        $currentTS = time();

        // Check if the user session expired
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
