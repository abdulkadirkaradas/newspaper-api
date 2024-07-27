<?php

namespace App\Http\Controllers\v1\Admin;

use App\Helpers\CommonFunctions;
use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\BadgeImage;
use App\Validators\CreateBadgeValidator;
use App\Validators\UploadBadgeImageValidator;
use Illuminate\Http\Request;

class BadgesController extends Controller
{
    /**
     * Create badges
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function create(Request $request): array
    {
        $validated = CommonFunctions::validateRequest($request, CreateBadgeValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $badge = new Badge();
        $badge->name = $validated['name'];
        $badge->type = $validated['type'];
        $badge->description = $validated['description'];

        if ($badge->save()) {
            return CommonFunctions::response(SUCCESS, BADGE_CREATED, [
                "badgeId" => $badge->id
            ]);
        }

        return CommonFunctions::response(FAIL, BADGE_CREATION_FAILED);
    }
}
