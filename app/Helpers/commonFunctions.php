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
}