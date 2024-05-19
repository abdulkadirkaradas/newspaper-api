<?php

namespace App\Http\Controllers\v1;

use App\Helpers\UserRoles;
use App\Validators\UserRegisterValidator;
use App\Http\Controllers\Controller;
use App\Models\UserAuthTokens;
use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request): array
    {
        $validated = UserRegisterValidator::validate($request);

        if (gettype($validated) === 'array' && isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $validated['role_id'] = UserRoles::$writer;

        [$name, $lastname, $username, $email, $password, $role_id] = array_values($validated);

        if ($this->checkValueExists('email', $email)) {
            return $this->errorMessage(BAD_REQUEST, 'This email has already been obtained!');
        }

        if ($this->checkValueExists('username', $username)) {
            return $this->errorMessage(BAD_REQUEST, 'This username has already been obtained!');
        }

        $user = new Users();
        $user->name = $name;
        $user->lastname = $lastname;
        $user->username = $username;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->role_id = $role_id;
        $userSave = $user->save();

        if (!$userSave) {
            return $this->errorMessage(FAIL, AN_ERROR_OCCURED);
        }

        $userAuth = new UserAuthTokens();
        $userAuth->token = $userAuth->generateTokenString();
        $userAuth->user_id = $user->id;
        $authSave = $userAuth->save();

        if (!$authSave) {
            return $this->errorMessage(FAIL, AN_ERROR_OCCURED);
        }

        return [
            'status' => SUCCESS,
            'message' => 'User has been created successfully.'
        ];
    }

    private function errorMessage(int $status, string $message) : array
    {
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    private function checkValueExists(string $key, string $value)
    {
        return Users::where($key, $value)->exists();
    }
}
