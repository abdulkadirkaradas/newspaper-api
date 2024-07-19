<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommonFunctions {
    /**
     * Returns API responses
     *
     * @param int $status
     * @param string $error
     * @param string $message
     * @return array
     */
    public static function response(int $status, string $error, ?array $message = null) : array
    {
        return [
            'status' => $status,
            'error'  => $error,
            'message' => $message ?? '',
        ];
    }

    /**
     * Validates provided UUID's
     *
     * @param string $uuid
     * @return bool
     */
    public static function checkUUIDValid(string $uuid)
    {
        return Str::isUuid($uuid);
    }

    /**
     * Validates informations in the request body
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $validator
     * @return mixed
     */
    public static function validateRequest(Request $request, $validator)
    {
        $validated = $validator::validate($request);

        if (gettype($validated) === 'array' && isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        return $validated;
    }
}