<?php

namespace App\Http\Controllers\v1;

use App\Validators\UserRegisterValidator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users;

class RegisterController extends Controller
{
    public function register(Request $request) {
        $validated = UserRegisterValidator::validate($request);

        if (gettype($validated) === 'array' && isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        // $validated['role_id'] = ROLE_WRITER;

        [$name, $lastname, $username, $email, $password] = array_values($validated);

        $user = new Users();
        $user->name = $name;
        $user->lastname = $lastname;
        $user->username = $username;
        $user->email = $email;
        $user->password = $password;
        $save = $user->save();

        if ($save) {
            return [
                'status' => SUCCESS,
                'message' => 'User has been created successfully.'
            ];
        } else {
            return [
                'status' => FAIL,
                'message' => AN_ERROR_OCCURED
            ];
        }
    }
}
