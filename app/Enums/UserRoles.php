<?php

namespace App\Enums;

enum UserRoles: int {
    case Admin = 1;
    case Moderator = 2;
    case Writer = 3;

    public static function getRole(int $role): ?string
    {
        return match ($role) {
            self::Admin->value => self::Admin->name,
            self::Moderator->value => self::Moderator->name,
            self::Writer->value => self::Writer->name,
            default => null,
        };
    }
}
