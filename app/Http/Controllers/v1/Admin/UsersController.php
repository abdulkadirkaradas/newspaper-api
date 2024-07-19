<?php

namespace App\Http\Controllers\v1\Admin;

use App\Models\User;
use App\Enums\UserRoles;
use Illuminate\Http\Request;
use App\Helpers\CommonFunctions;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    /**
     * Return users
     * - id
     * - type; all, blocked
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function user(Request $request): array
    {
        $params = $request->only(['id', 'type']);

        $userQuery = User::select(
            'id',
            'name',
            'lastname',
            'username',
            'email',
            'blocked',
            'role_id',
            'created_at'
        )
            ->with([
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
            ]);

        if (!isset($params['type'])) {
            $user = $userQuery->find($params['id']);

            if ($user) {
                $user->role = UserRoles::getRole($user->role_id);
            }

            return [
                'user' => $user
            ];
        }

        if (isset($params['type']) && $params['type'] === 'blocked') {
            $userQuery->where('blocked', 'true');
        }

        $user = $userQuery->get();

        return [
            'user' => $user
        ];
    }

    /**
     * Block user by id no
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function block_user(Request $request)
    {
        $id = $request->input('id');

        if (!CommonFunctions::checkUUIDValid($id)) {
            return CommonFunctions::response(BAD_REQUEST, INVALID_ID_NO);
        }

        $user = User::find($id);

        if (!$user) {
            return CommonFunctions::response(BAD_REQUEST, USER_NOT_FOUND);
        }

        if ($user->blocked === true) {
            return CommonFunctions::response(BAD_REQUEST, "User already blocked!");
        }

        $user->blocked = true;

        return $user->save()
            ? CommonFunctions::response(SUCCESS, "User has been blocked!")
            : CommonFunctions::response(FAIL, "Failed to block user.");
    }
}