<?php

namespace App\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChangePostVisiblityValidator
{
    public static function validate(Request $request)
    {
        $validations = [
            "message" => ['required', 'string', 'max:200'],
            "reason" => ['required', 'string', 'max:20'],
            "warning_level" => ['required', 'integer', 'between:1,5'],
        ];

        $params = $request->input('warning');

        if (is_null($params) || empty($params)) {
            return [
                'status' => BAD_REQUEST,
                'message' => VALIDATOR_FAILED,
            ];
        }

        $validator = Validator::make($params, $validations);

        if ($validator->fails()) {
            return [
                'status' => BAD_REQUEST,
                'message' => VALIDATOR_FAILED,
            ];
        }

        return $validator->validated();
    }
}