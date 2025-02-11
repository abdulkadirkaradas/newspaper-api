<?php

namespace App\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateNewsCategoryValidator
{
    public static function validate(Request $request)
    {
        $validations = [
            "name" => ['required', 'string', 'max:50'],
            "description" => ['required', 'string', 'max:500'],
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