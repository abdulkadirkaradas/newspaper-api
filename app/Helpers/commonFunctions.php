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
    public static function validateUUID(string $uuid)
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

    /**
     * Check if logged user role is Administrator
     *
     * @param \App\Models\User $user
     * @return
     */
    public static function isAdmin(User $user)
    {
        return $user->isAdministrator();
    }

    /**
     * Check if logged user role is Moderator
     *
     * @param \App\Models\User $user
     * @return
     */
    public static function isModerator(User $user)
    {
        return $user->isModerator();
    }

    /**
     * Check if logged user role is Writer
     *
     * @param \App\Models\User $user
     * @return
     */
    public static function isWriter(User $user)
    {
        return $user->isWriter();
    }
}