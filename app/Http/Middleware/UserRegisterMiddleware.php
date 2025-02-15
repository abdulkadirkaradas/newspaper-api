<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\CommonFunctions;
use Symfony\Component\HttpFoundation\Response;

class UserRegisterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $username = $request->bodyContent['username'] ?? null;
        $email = $request->bodyContent['email'] ?? null;
        $password = $request->bodyContent['password'] ?? null;

        if (!isset($username) || !isset($email) || !isset($password)) {
            return CommonFunctions::response(BAD_REQUEST, "The mandatory fields should be filled!");
        }

        if ($this->checkValueExists('email', $email) || $this->checkValueExists('username', $username)) {
            return CommonFunctions::response(CONFLICT, "This email|username has already been obtained!");
        }

        return $next($request);
    }

    private function checkValueExists(string $key, string $value)
    {
        return User::where($key, $value)->exists();
    }
}
