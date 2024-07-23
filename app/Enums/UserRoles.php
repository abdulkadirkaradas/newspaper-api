<?php

namespace App\Enums;

enum UserRoles: int {
    case Admin = 1;
    case Moderator = 2;
    case Writer = 3;

    public static function getRole(int|string $role): ?string
    {
        if (is_int($role)) {
            return match ($role) {
                self::Admin->value => self::Admin->name,
                self::Moderator->value => self::Moderator->name,
                self::Writer->value => self::Writer->name,
                default => null,
            };
        } elseif (is_string($role)) {
            return match ($role) {
                self::Admin->name => self::Admin->value,
                self::Moderator->name => self::Moderator->value,
                self::Writer->name => self::Writer->value,
                default => null,
            };
        }

        return null;
    }
}
