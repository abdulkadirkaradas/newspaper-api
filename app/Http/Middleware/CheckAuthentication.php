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
        if (str_contains($request->route()->getActionName(), 'register')) {
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

        // This condition passes requests if the incoming requests are for login
        if (!$request->header('auth-token') && str_contains($request->route()->getActionName(), 'login')) {
            $email = $request->input('email');
            $password = $request->input('password');

            $user = Users::where([
                ['email', $email],
                ['password', Hash::make($password)]
            ])->first();

            if ($user) {
                $request['user'] = $user;

                return $next($request);
            }

            return response()->json($this->errorMessage(UNAUTHORIZED, UNAUTHORIZED_ACCESS));
        }

        $authToken = $request->header('auth-token');

        $auth = UserAuthTokens::where('token', $authToken)->first();
        $user = Users::find($auth->user_id);

        $expireDateTS = strtotime($auth->expire_date);
        $currentTS = time();

        if ($expireDateTS < $currentTS) {
            $auth->expire_date = now()->addDays(15);
            $auth->save();
            // return response()->json($this->errorMessage(UNAUTHORIZED, SESSION_EXPIRED));
        }

        $request['user'] = $user;

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
