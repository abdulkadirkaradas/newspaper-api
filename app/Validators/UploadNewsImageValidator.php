<?php

namespace App\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadNewsImageValidator
{
    public static function validate(Request $request)
    {
        $validations = [
            "image" => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],
            "name" => ['required', 'string', 'max:25', 'regex:/^[a-zA-Z0-9_\-]+$/'],
            "ext" => ['required', 'string', 'in:jpeg,jpg,png']
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