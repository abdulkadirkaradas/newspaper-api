<?php

namespace App\Http\Middleware;

use App\Helpers\UserRoles;
use App\Models\UserAuthTokens;
use App\Models\Users;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthorization
{
    /**
     * Handle incoming authorization requests.
     *
     * Expected behaviours;
     * - check if token exists and not expired
     * - Check user role
     * - Store user role for the processes
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // This condition will be removed in the future, now just added for the test purposes
        if (str_contains($request->url(), '/register')) {
            return $next($request);
        }

        if (!$request->header('auth-token')) {
            return response()->json($this->errorMessage(UNAUTHORIZED, UNAUTHORIZED_ACCESS));
        }

        $authToken = $request->header('auth-token');

        $auth = UserAuthTokens::where('token', $authToken)->first();
        $user = Users::find($auth->user_id);

        $userRoles = [
            UserRoles::$admin,
            UserRoles::$mod,
            UserRoles::$writer
        ];

        if (!in_array($user->role_id, $userRoles)) {
            return response()->json($this->errorMessage(UNAUTHORIZED, UNAUTHORIZED_ACCESS));
        }

        $expireDateTS = strtotime($auth->expire_date);
        $currentTS = time();

        if ($expireDateTS < $currentTS) {
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
