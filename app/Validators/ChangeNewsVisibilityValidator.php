<?php

namespace App\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChangeNewsVisibilityValidator
{
    public static function validate(Request $request)
    {
        $validations = [
            "userId" => ['required', 'uuid'],
            "type" => ['required', 'string'],
            "visibility" => ['required', 'boolean'],
            "warning" => ['required', 'array'],
            "warning.message" => ['required', 'string'],
            "warning.reason" => ['required', 'string'],
            "warning.warningLevel" => ['required', 'integer', 'min:1', 'max:5'],
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
