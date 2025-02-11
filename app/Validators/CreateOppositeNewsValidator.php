<?php

namespace App\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateOppositeNewsValidator
{
    public static function validate(Request $request)
    {
        $validations = [
            "sourceUserId" => ['required', 'uuid'],
            "categoryId" => ['required', 'uuid'],
            "title" => ['required', 'string', 'max:100'],
            "content" => ['required', 'string', 'max:4000'],
            "priority" => ['nullable', 'integer', 'max:2'],
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