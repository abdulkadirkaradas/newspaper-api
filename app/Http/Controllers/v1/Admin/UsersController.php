<?php

namespace App\Http\Controllers\v1\Admin;

use App\Models\User;
use App\Models\Warning;
use App\Enums\UserRoles;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\CommonFunctions;
use App\Http\Controllers\Controller;
use App\Validators\BlockUserValidator;
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
    public function index(Request $request): array
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
    public function updateRole(User $user, Request $request): array
    {
        $request->validate([
            'role_id' => 'required|integer|in:1,2,3'
        ]);

        $roleId = $request->input('role_id');

        $user->role_id = $roleId;

        if ($user->save()) {
            return CommonFunctions::response(SUCCESS, "User role has been changed successfully!");
        } else {
            return CommonFunctions::response(FAIL, "User role could not be changed!");
        }
    }

    /**
     * Block or unblock an user by id no
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function block(User $user, Request $request)
    {
        $validated = CommonFunctions::validateRequest($request, BlockUserValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $block = (bool) $validated['block'];

        if ($block && $user->blocked) {
            return CommonFunctions::response(BAD_REQUEST, "User already blocked!");
        }

        if (!$block && !$user->blocked) {
            return CommonFunctions::response(BAD_REQUEST, "User is not blocked!");
        }

        $user->blocked = $block;
        $message = $block ? "User has been blocked!" : "User has been unblocked!";

        return $user->save()
            ? CommonFunctions::response(SUCCESS, $message)
            : CommonFunctions::response(FAIL, "Failed to update block status.");
    }

    /**
     * Returns logged user notifications | all, read, unread | time-range (optional)
     *
     * @var Request $request
     * @return array
     */
    public function getUserNotifications(Request $request)
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

                    if ($params['type'] !== 'read' || 'unread') {
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
     * Create notification for user by id
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function createNotification(User $user, Request $request)
    {
        $validated = CommonFunctions::validateRequest($request, CreateNotificationValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $notification = new Notification();
        $notification->type = $validated['type'];
        $notification->title = $validated['title'];
        $notification->message = $validated['message'];

        if ($user->notifications()->save($notification)) {
            return CommonFunctions::response(SUCCESS, [
                "notificationId" => $notification->id,
                'meesage' => NOTIFICATION_CREATED
            ]);
        }

        return CommonFunctions::response(BAD_REQUEST, NOTIFICATION_CREATION_FAILED);
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
            return CommonFunctions::response(SUCCESS, [
                "warningId" => $warning->id,
                'message' => WARNING_CREATED
            ]);
        } else {
            return CommonFunctions::response(FAIL, WARNING_CREATION_FAILED);
        }
    }
}