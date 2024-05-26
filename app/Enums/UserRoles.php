<?php

namespace App\Enums;

enum UserRoles: int {
    case Admin = 1;
    case Moderator = 2;
    case Writer = 3;
}
