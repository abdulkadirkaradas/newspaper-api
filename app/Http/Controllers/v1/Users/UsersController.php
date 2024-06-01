<?php

namespace App\Http\Controllers\v1\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Returns logged user profile informations
     */
    public function profile(Request $request)
    {
        $user = $request->user;

        $userInfo = User::with([
            'news' => function ($query) {
                $query->select('user_id', 'title', 'created_at');
            },
            'notifications' => function ($query) {
                $query->select('user_id', 'type', 'message', 'created_at');
            },
            'warnings' => function ($query) {
                $query->select('user_id', 'message', 'warning_level', 'created_at');
            },
            'reactions' => function ($query) {
                $query->select('user_id', 'reaction_type');
            },
            'badges' => function ($query) {
                $query->select('user_id', 'name', 'type');
            },
            //TODO add user_id column to the badge_images table
            'badges.badgeImages' => function ($query) {
                $query->select('badge_id', 'fullpath');
            },
            'roles' => function ($query) {
                $query->select('name');
            }
        ])->findOrFail($user->id);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'lastname' => $user->lastname,
            'username' => $user->username,
            'membership_date' => $user->created_at,
            'news' => $userInfo->news,
            'notifications' => $userInfo->notifications,
            'warnings' => $userInfo->warnings,
            'reactions' => $userInfo->reactions,
            'badges' => $userInfo->badges,
            'role' => $userInfo->role
        ];
    }
}
