<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserRegisterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $username = $request->bodyContent['username'];
        $email = $request->bodyContent['email'];

        if (!isset($username) && !isset($email)) {
            return response()
                ->json($this->errorMessage(BAD_REQUEST, "Fields should be filled!"));
        }

        if ($this->checkValueExists('email', $email) || $this->checkValueExists('username', $username)) {
            return response()
                ->json($this->errorMessage(BAD_REQUEST, "This email|username has already been obtained!"));
        }

        return $next($request);
    }

    private function checkValueExists(string $key, string $value)
    {
        return User::where($key, $value)->exists();
    }

    private function errorMessage(int $status, string $message): array
    {
        return [
            "status" => $status,
            "message" => $message
        ];
    }
}
