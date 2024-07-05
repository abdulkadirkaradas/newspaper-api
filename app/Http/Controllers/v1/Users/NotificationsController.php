<?php

namespace App\Http\Controllers\v1\Users;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\CommonFunctions;
use App\Http\Controllers\Controller;

class NotificationsController extends Controller
{
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
}
