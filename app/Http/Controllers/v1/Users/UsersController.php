<?php

namespace App\Http\Controllers\v1\Users;

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
            'role' => $userInfo->roles
        ];
    }

    /**
     * Returns any user profile informations
     *
     * @var Request $request
     * @return array
     */
    public function user(Request $request): array
    {
        $user = $request->user;

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

    /**
     * Returns logged user notifications | all, read, unread | time-range (optional)
     *
     * @var Request $request
     * @return array
     */
    public function notifications(Request $request): array
    {
        $user = $request->user;
        $params = $request->only(['type', 'from', 'to']);

        if (count($params) === 0 || !isset($params['type'])) {
            return CommonFunctions::response(BAD_REQUEST, BAD_REQUEST_MSG);
        }

        $info = User::with([
            'notifications' => function ($query) use ($params) {
                $query->select('user_id', 'type', 'message', 'created_at');

                // Check if 'from' and 'to' parameters exists
                // and make query by the creation time
                if (isset($params['from']) || isset($params['to'])) {
                    $query->whereBetween('created_at', [$params['from'], $params['to']]);
                }

                // Check if 'type' parameter is not equal 'all' value
                // and make query by the 'is_read' column
                if ($params['type'] !== 'all') {
                    $query->where('is_read', $params['type'] === 'read');
                }
            }
        ])->findOrFail($user->id);

        return [
            'notifications' => $info->notifications
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

    /**
     * Returns all news reactions of logged user
     *
     * @var Request $request
     * @return array
     */
    public function reactions(Request $request): array
    {
        $user = $request->user;

        $reactions = User::select('id', 'name', 'lastname', 'username')
            ->with([
                'news' => function ($query) {
                    $query->select('id', 'user_id', 'title', 'created_at')
                        ->with([
                            'newsReactions' => function ($query) {
                                $query->select('user_id', 'news_id', 'reaction', 'type', 'created_at');
                            }
                        ]);
                }
            ])->findOrFail($user->id);

        return [
            "reactions" => $reactions
        ];
    }
}