<?php

namespace App\Http\Controllers\v1\Users;

use App\Enums\UserRoles;
use App\Helpers\CommonFunctions;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    /**
     * Returns logged user profile informations
     *
     * @var Request $request
     * @return array
     */
    public function profile(Request $request): array
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
                $query->select('id', 'user_id', 'name', 'type')
                    ->with([
                        'badgeImages' => function ($query) {
                            $query->select('id', 'badge_id', 'fullpath');
                        }
                    ]);
            }
        ])->findOrFail($user->id);

        $userInfo->role = UserRoles::getRole($user->role_id);

        return [
            'name' => $user->name,
            'lastname' => $user->lastname,
            'username' => $user->username,
            'membershipDate' => $user->created_at,
            'news' => $userInfo->news,
            'notifications' => $userInfo->notifications,
            'warnings' => $userInfo->warnings,
            'reactions' => $userInfo->reactions,
            'badges' => $userInfo->badges,
            'role' => $userInfo->role
        ];
    }

    /**
     * Returns logged user notifications | all, read, unread | time-range (optional)
     *
     * @var Request $request
     * @return array
     */
    public function notifications(Request $request): array
    {
        $params = $request->only(['type', 'from', 'to', 'userId']);

        if (!isset($params['type'])) {
            return CommonFunctions::response(BAD_REQUEST, BAD_REQUEST_MSG);
        }

        $query = User::select('id', 'name', 'lastname', 'username')
            ->with([
                'notifications' => function ($query) use ($params) {
                    $query->select('user_id', 'type', 'title', 'message', 'created_at');

                    if (isset($params['from'])) {
                        $query->where('created_at', '>=', date($params['from']));
                    }
                    if (isset($params['to'])) {
                        $query->where('created_at', '<=', date($params['to']));
                    }

                    if ($params['type'] !== 'all') {
                        $query->where('is_read', $params['type'] === 'read');
                    }
                }
            ]);

        if (isset($params['userId'])) {
            $notifications = $query->find($params['userId']);
        } else {
            $notifications = $query->get();
        }

        return [
            'notifications' => $notifications ?? []
        ];
    }

    /**
     * Returns logged user warnings
     *
     * @var Request $request
     * @return array
     */
    public function warnings(Request $request): array
    {
        $user = $request->user;

        $warnings = User::select('id', 'name', 'lastname', 'username')
            ->with([
                'warnings' => function ($query) {
                    $query->select('user_id', 'message', 'reason', 'warning_level')
                        ->orderBy('warning_level', 'asc');
                }
            ])->findOrFail($user->id);

        return [
            'warnings' => $warnings
        ];
    }
}