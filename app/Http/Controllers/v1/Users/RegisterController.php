<?php

namespace App\Http\Controllers\v1\Users;

use App\Enums\UserRoles;
use App\Validators\UserRegisterValidator;
use App\Http\Controllers\Controller;
use App\Models\UserAuthTokens;
use App\Models\UserRoles as UserRolesModel;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request): array
    {
        $validated = UserRegisterValidator::validate($request);

        if (gettype($validated) === 'array' && isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $validated['role_id'] = UserRoles::Writer->value;

        [$name, $lastname, $username, $email, $password, $role_id] = array_values($validated);

        $user = User::create([
            'name' => $name,
            'lastname' => $lastname,
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        if (!$user) {
            return $this->errorMessage(FAIL, AN_ERROR_OCCURED);
        }

        $role = UserRolesModel::create([
            'user_id' => $user->id,
            'role_id' => $role_id,
        ]);

        if (!$role) {
            return $this->errorMessage(FAIL, AN_ERROR_OCCURED);
        }

        $token = Auth::guard('api')->login($user);

        $userAuth = UserAuthTokens::create([
            'token' => $token,
            'user_id' => $user->id
        ]);

        if (!$userAuth) {
            return $this->errorMessage(FAIL, AN_ERROR_OCCURED);
        }

        return [
            'status' => SUCCESS,
            'message' => 'User has been created successfully.',
            'authorisation' => [
                'token' => $token
            ]
        ];
    }

    private function errorMessage(int $status, string $message) : array
    {
        return [
            'status' => $status,
            'message' => $message
        ];
    }
}