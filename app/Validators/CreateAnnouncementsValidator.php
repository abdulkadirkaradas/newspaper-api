<?php

namespace App\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateAnnouncementsValidator
{
    public static function validate(Request $request)
    {
        $validations = [
            "title" => ['required', 'string', 'max:100'],
            "content" => ['required', 'string', 'max:5000'],
            "priority" => ['nullable', 'integer', 'between:1,5'],
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