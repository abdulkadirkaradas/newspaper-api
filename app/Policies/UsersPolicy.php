<?php

namespace App\Policies;

use App\Enums\UserRoles as DefaultRoles;
use App\Models\User;
use App\Models\Notification;
use App\Models\Reaction;
use App\Models\Warning;

class UsersPolicy
{
    public function before(User $user) {
        if ($user->isAdministrator()) {
            return true;
        }
    }

    public function notifications(User $user, Notification $notifs)
    {
        return ($user->id === $notifs->user_id && $user->roles->role_id === DefaultRoles::Writer->value)
            || $user->roles->role_id && DefaultRoles::Moderator->value;
    }

    public function warnings(User $user, Warning $warnings)
    {
        return ($user->id === $warnings->user_id && $user->roles->role_id === DefaultRoles::Writer->value)
            || $user->roles->role_id && DefaultRoles::Moderator->value;
    }

    public function reactions(User $user, Reaction $reactions)
    {
        return ($user->id === $reactions->user_id && $user->roles->role_id === DefaultRoles::Writer->value)
            || $user->roles->role_id && DefaultRoles::Moderator->value;
    }
}
