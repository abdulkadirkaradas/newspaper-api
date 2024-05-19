<?php

namespace App\Http\Middleware;

use App\Models\Users;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $username = $request->input('username');
        $email = $request->input('email');

        if ($this->checkValueExists('email', $email)) {
            return response()
                ->json($this->errorMessage(BAD_REQUEST, 'This email has already been obtained!'));
        }

        if ($this->checkValueExists('username', $username)) {
            return response()
                ->json($this->errorMessage(BAD_REQUEST, 'This username has already been obtained!'));
        }

        return $next($request);
    }

    private function checkValueExists(string $key, string $value)
    {
        return Users::where($key, $value)->exists();
    }

    private function errorMessage(int $status, string $message): array
    {
        return [
            "status" => $status,
            "message" => $message
        ];
    }
}
