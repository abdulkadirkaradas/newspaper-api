<?php

namespace App\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateNotificationValidator
{
    public static function validate(Request $request)
    {
        $validations = [
            "type" => ['required', 'integer', 'min:1', 'max:3'],
            "title" => ['required', 'string', 'max:100'],
            "message" => ['required', 'string', 'max:1200'],
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