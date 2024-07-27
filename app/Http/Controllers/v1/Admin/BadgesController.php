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

    /**
     * Upload badge image
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function upload_image(Request $request): array
    {
        $user = $request->user;
        $badge = $request->badge;

        $validated = CommonFunctions::validateRequest($request, UploadBadgeImageValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        // Remove all characters before index 18 and merge IDs
        $compoundKey = substr($user->id, 19) . '-' . substr($badge->id, 19);

        $fullpath = time() . '_' . $compoundKey . '_' . $validated['name'] . '.' . $request['ext'];
        $request->image->move(public_path('badges'), $fullpath);

        $newsImage = new BadgeImage();
        $newsImage->name = $validated['name'];
        $newsImage->ext = $validated['ext'];
        $newsImage->fullpath = $fullpath;

        if ($badge->badgeImages()->save($newsImage)) {
            return CommonFunctions::response(SUCCESS, BADGE_IMAGE_CREATED);
        } else {
            return CommonFunctions::response(FAIL, BADGE_IMAGE_CREATION_FAILED);
        }
    }
}
