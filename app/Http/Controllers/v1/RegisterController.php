<?php

namespace App\Http\Controllers\v1;

use App\Helpers\UserRoles;
use App\Validators\UserRegisterValidator;
use App\Http\Controllers\Controller;
use App\Models\UserAuthTokens;
use Illuminate\Http\Request;
use App\Models\Users;
use Carbon\Carbon;
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

        $user = new Users();
        $user->name = $name;
        $user->lastname = $lastname;
        $user->username = $username;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->role_id = $role_id;
        $userSave = $user->save();

        if (!$userSave) {
            $this->errorMessage(FAIL, AN_ERROR_OCCURED);
        }

        $userAuth = new UserAuthTokens();
        $userAuth->token = $userAuth->generateTokenString();
        $userAuth->user_id = $user->id;
        $authSave = $userAuth->save();

        if (!$authSave) {
            $this->errorMessage(FAIL, AN_ERROR_OCCURED);
        }

        return [
            'status' => SUCCESS,
            'message' => 'User has been created successfully.'
        ];
    }

    private function errorMessage($status, $message) : array
    {
        return [
            'status' => $status,
            'message' => $message
        ];
    }
}
