<?php

namespace App\Http\Controllers\v1\Admin;

use App\Models\User;
use App\Models\Warning;
use App\Enums\UserRoles;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\CommonFunctions;
use App\Http\Controllers\Controller;
use App\Validators\CreateWarningValidator;
use App\Validators\CreateNotificationValidator;

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
     * Change user role by id
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function change_user_role(Request $request): array
    {
        $params = $request->only(['id', 'role_id']);

        $user = User::find($params['id']);
        $user->role_id = $params['role_id'];

        if ($user->save()) {
            return CommonFunctions::response(SUCCESS, "User role has been changed successfully!");
        } else {
            return CommonFunctions::response(FAIL, "User role could not be changed!");
        }
    }

    /**
     * Block user by id no
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function block_user(Request $request): array
    {
        $user = $request->providedUser;

        if ($user->blocked === true) {
            return CommonFunctions::response(BAD_REQUEST, "User already blocked!");
        }

        $user->blocked = true;

        return $user->save()
            ? CommonFunctions::response(SUCCESS, "User has been blocked!")
            : CommonFunctions::response(FAIL, "Failed to block user.");
    }

    /**
     * Returns logged user notifications | all, read, unread | time-range (optional)
     *
     * @var Request $request
     * @return array
     */
    public function get_user_notifications(Request $request): array
    {
        $user = $request->providedUser;
        $params = $request->only(['type', 'from', 'to']);

        if (count($params) === 0 || !isset($params['type'])) {
            return CommonFunctions::response(BAD_REQUEST, BAD_REQUEST_MSG);
        }

        $info = User::with([
            'notifications' => function ($query) use ($params) {
                $query->select('user_id', 'type', 'title', 'message', 'created_at');

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
        ])->find($user->id);

        return [
            'notifications' => $info->notifications
        ];
    }

    /**
     * Create notification for user by id
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function create_notification(Request $request): array
    {
        $user = $request->providedUser;

        $validated = CommonFunctions::validateRequest($request, CreateNotificationValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $notification = new Notification();
        $notification->type = $validated['type'];
        $notification->title = $validated['title'];
        $notification->message = $validated['message'];

        if ($user->notifications()->save($notification)) {
            return CommonFunctions::response(SUCCESS, NOTIFICATION_CREATED, [
                "notificationId" => $notification->id
            ]);
        } else {
            return CommonFunctions::response(SUCCESS, NOTIFICATION_CREATION_FAILED);
        }
    }

    /**
     * Returns logged user warnings
     *
     * @var Request $request
     * @return array
     */
    public function get_user_warnings(Request $request): array
    {
        $user = $request->providedUser;

        $warnings = User::select('id', 'name', 'lastname', 'username')
            ->with([
                'warnings' => function ($query) {
                    $query->select('user_id', 'message', 'reason', 'warning_level')
                        ->orderBy('warning_level', 'asc');
                }
            ])->find($user->id);

        return [
            'warnings' => $warnings
        ];
    }

    /**
     * Create warning for user by id
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function create_warning(Request $request): array
    {
        $user = $request->providedUser;

        $validated = CommonFunctions::validateRequest($request, CreateWarningValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $warning = new Warning();
        $warning->message = $validated['message'];
        $warning->reason = $validated['reason'];
        $warning->warning_level = $validated['warning_level'];

        if ($user->notifications()->save($warning)) {
            return CommonFunctions::response(SUCCESS, WARNING_CREATED, [
                "warningId" => $warning->id
            ]);
        } else {
            return CommonFunctions::response(SUCCESS, WARNING_CREATION_FAILED);
        }
    }
}