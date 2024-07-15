<?php

namespace App\Helpers;

use Illuminate\Http\Request;

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
        $uuidRegex = '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/';

        // Compare taken id parameter with the uuid regex string
        if (!preg_match($uuidRegex, $uuid)) {
            // Return response message if it not match
            return false;
        }

        return true;
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