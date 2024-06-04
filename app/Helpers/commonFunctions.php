<?php

namespace App\Helpers;

class CommonFunctions {
    public static function response(int $status, string $message) : array
    {
        return [
            'status' => $status,
            'message' => $message
        ];
    }
}