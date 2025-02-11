<?php

namespace App\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlockUserValidator
{
    public static function validate(Request $request)
    {
        $validations = [
            "block" => ['required', 'boolean'],
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