<?php

namespace App\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserRegisterValidator
{
    public static function validate(Request $request)
    {
        $validations = [
            "name" => ['required', 'string', 'max:255'],
            "lastname" => ['required', 'string', 'max:255'],
            "username" => ['required', 'string', 'max:25'],
            "email" => ['required', 'string', 'email', 'max:255'],
            // "password" => ['required', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()->uncompromised(2)]
            "password" => ['required', Password::min(8)->letters()->numbers()->mixedCase()],
        ];

        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            return [
                'status' => BAD_REQUEST,
                'message' => "The request could not be understood or was missing required parameters.",
            ];
        }

        return $validator->validated();
    }
}
