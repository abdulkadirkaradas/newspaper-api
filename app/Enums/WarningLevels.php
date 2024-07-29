<?php

namespace App\Enums;

enum WarningLevels: int {
    case Low = 1;
    case Medium = 2;
    case High = 3;
    case Critical = 4;
    case Block = 5;

    public static function getWarningLevel(int|string $level): ?string
    {
        return match ($level) {
            self::Low->value => self::Low->name,
            self::Medium->value => self::Medium->name,
            self::High->value => self::High->name,
            self::Critical->value => self::Critical->name,
            self::Block->value => self::Block->name,
            default => null,
        };
    }
}
