<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\UserAuthTokens;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class LoginMiddleware
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
            return response()->json($this->errorMessage(UNAUTHORIZED, UNAUTHORIZED_ACCESS));
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
