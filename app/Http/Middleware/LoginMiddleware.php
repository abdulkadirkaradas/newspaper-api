<?php

namespace App\Http\Middleware;

use App\Models\Users;
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
        if ($request->bearerToken() &&
            (str_contains($request->route()->getActionName(), 'refreshAuthToken') || str_contains($request->route()->getActionName(), 'logout'))) {
            return $next($request);
        }

        $email = $request->input('email');
        $password = $request->input('password');

        $user = Users::where([
            ['email', $email]
        ])->first();

        if (!isset($user) || !Hash::check($password, $user->password)) {
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
