<?php

namespace App\Http\Controllers\v1\Public;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * All those functions have been added for unauthenticated users
 */
class UserController extends Controller
{
    /**
     * Returns any user profile informations
     *
     * @var Request $request
     * @return array
     */
    public function user(Request $request): array
    {
        $user = $request->providedUser;

        $info = User::with([
            'news' => function ($query) {
                $query->select('user_id', 'title', 'created_at');
            },
            'reactions' => function ($query) {
                $query->select('user_id', 'reaction_type');
            },
            'badges' => function ($query) {
                $query->select('id', 'user_id', 'name', 'type')
                    ->with([
                        'badgeImages' => function ($query) {
                            $query->select('id', 'badge_id', 'fullpath');
                        }
                    ]);
            },
        ])->findOrFail($user->id);

        return [
            'name' => $user->name,
            'lastname' => $user->lastname,
            'username' => $user->username,
            'membership_date' => $user->created_at,
            'news' => $info->news,
            'reactions' => $info->reactions,
            'badges' => $info->badges,
        ];
    }
}