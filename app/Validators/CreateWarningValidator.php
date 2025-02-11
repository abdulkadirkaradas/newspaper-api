<?php

namespace App\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateWarningValidator
{
    public static function validate(Request $request)
    {
        $validations = [
            "message" => ['required', 'string', 'max:255'],
            "reason" => ['required', 'string', 'max:25'],
            "warning_level" => ['required', 'integer', 'min:1', 'max:5'],
        ];

        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            return [
                'status' => BAD_REQUEST,
                'message' => VALIDATOR_FAILED,
            ];
        }

        return $validator->validated();
    }
}