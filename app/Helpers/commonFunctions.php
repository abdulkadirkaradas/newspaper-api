<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommonFunctions {
    /**
     * Returns API responses
     *
     * @param int $status
     * @param mixed $message
     * @param string $error
     * @return array
     */
    public static function response(int $status, mixed $message = null)
    {
        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
        ], $status);
    }

    /**
     * Validates provided UUID's
     *
     * @param string $uuid
     * @return bool
     */
    public static function validateUUID(string|array $uuid)
    {
        if (is_array($uuid)) {
            $result = array_map([Str::class, 'isUuid'], $uuid);
            return !in_array(false, $result, true);
        }

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

    public static function getEnumValues(string $enumClass): array
    {
        $valuesAndNames = [];
        foreach ($enumClass::cases() as $case) {
            $valuesAndNames[$case->value] = $case->name;
        }
        return $valuesAndNames;
    }
}