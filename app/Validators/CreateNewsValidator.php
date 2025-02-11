<?php

namespace App\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateNewsValidator
{
    public static function validate(Request $request)
    {
        $validations = [
            "title" => ['required', 'string', 'max:100'],
            "content" => ['required', 'string', 'max:4000'],
            "priority" => ['nullable', 'integer', 'max:2'],
            "categoryId" => ['required', 'uuid'],
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