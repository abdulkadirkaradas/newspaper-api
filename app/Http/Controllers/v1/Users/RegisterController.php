<?php

namespace App\Http\Controllers\v1\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserAuthTokens;
use App\Helpers\CommonFunctions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRoles as DefaultRoles;
use App\Validators\UserRegisterValidator;

class RegisterController extends Controller
{
    public function register(Request $request): array
    {
        $validated = CommonFunctions::validateRequest($request, UserRegisterValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        [$name, $lastname, $username, $email, $password] = array_values($validated);

        $user = User::create([
            'name' => $name,
            'lastname' => $lastname,
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => DefaultRoles::Writer->value,
        ]);

        if (!$user) {
            return CommonFunctions::response(INTERNAL_SERVER_ERROR, AN_ERROR_OCCURED);
        }

        $token = auth()->login($user);

        $userAuth = UserAuthTokens::create([
            'token' => $token,
            'user_id' => $user->id
        ]);

        if (!$userAuth) {
            return CommonFunctions::response(INTERNAL_SERVER_ERROR, AN_ERROR_OCCURED);
        }

        return [
            'status' => SUCCESS,
            'message' => 'User has been created successfully.',
            'authorisation' => [
                'token' => $token
            ]
        ];
    }
}
