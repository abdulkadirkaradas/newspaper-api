<?php

namespace App\Policies;

use App\Enums\UserRoles as DefaultRoles;
use App\Models\User;
use App\Models\UserMessages;
use App\Models\UserNotifications;
use App\Models\UserReactions;
use App\Models\UserWarnings;

class UsersPolicy
{
    public function before(User $user) {
        if ($user->isAdministrator()) {
            return true;
        }
    }

    public function notifications(User $user, UserNotifications $notifs)
    {
        return ($user->id === $notifs->user_id && $user->roles->roles_id === DefaultRoles::Writer->value)
            || $user->roles->roles_id && DefaultRoles::Moderator->value;
    }

    public function warnings(User $user, UserWarnings $warnings)
    {
        return ($user->id === $warnings->user_id && $user->roles->roles_id === DefaultRoles::Writer->value)
            || $user->roles->roles_id && DefaultRoles::Moderator->value;
    }

    public function reactions(User $user, UserReactions $reactions)
    {
        return ($user->id === $reactions->user_id && $user->roles->roles_id === DefaultRoles::Writer->value)
            || $user->roles->roles_id && DefaultRoles::Moderator->value;
    }

    public function messsages(User $user, UserMessages $messages)
    {
        return ($user->id === $messages->user_id && $user->roles->roles_id === DefaultRoles::Writer->value)
            || $user->roles->roles_id && DefaultRoles::Moderator->value;
    }
}
