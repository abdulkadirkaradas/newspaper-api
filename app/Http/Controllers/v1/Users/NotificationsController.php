<?php

namespace App\Http\Controllers\v1\Users;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationsController extends Controller
{
    /**
     * Returns logged user notifications
     *
     * @var Request $request
     * @return array
     */
    public function notifications(Request $request): array
    {
        $user = $request->user;

        $info = User::with([
            'notifications' => function ($query) {
                $query->select('user_id', 'type', 'message', 'created_at')->where('is_read', false);
            }
        ])->findOrFail($user->id);

        return [
            'notifications' => $info->notifications
        ];
    }
}
