<?php

namespace App\Http\Controllers\v1\Users;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BadgesController extends Controller
{
    public function badges(Request $request)
    {
        $user = $request->user;

        $userBadges = $user->badges()->get();

        return [
            'badgeCount' => $userBadges->count(),
            'userBadges' => $userBadges,
        ];
    }
}
