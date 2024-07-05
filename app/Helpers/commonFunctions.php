<?php

namespace App\Helpers;

class CommonFunctions {
    public static function response(int $status, string $error, string $message = null) : array
    {
        return [
            'status' => $status,
            'error'  => $error,
            'message' => $message ?? '',
        ];
    }

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
}