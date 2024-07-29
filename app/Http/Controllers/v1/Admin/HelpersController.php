<?php

namespace App\Http\Controllers\v1\Admin;

use App\Enums\UserRoles;
use App\Enums\WarningLevels;
use App\Helpers\CommonFunctions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HelpersController extends Controller
{
    /**
     * Return default user roles
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function user_roles(Request $request): array
    {
        return CommonFunctions::getEnumValues(UserRoles::class);
    }

    /**
     * Return default warning_levels
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function warning_levels(Request $request): array
    {
        return CommonFunctions::getEnumValues(WarningLevels::class);
    }
}
