<?php

namespace App\Http\Controllers\v1\Admin;

use App\Helpers\CommonFunctions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Announcements;
use App\Validators\CreateAnnouncementsValidator;

class AnnouncementsController extends Controller
{
    /**
     * Create an announcement
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function create(Request $request): array
    {
        $user = $request->user;

        $validated = CommonFunctions::validateRequest($request, CreateAnnouncementsValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $title = $validated['title'];
        $content = $validated['content'];
        $priority = $validated['priority'];

        $announce = new Announcements();
        $announce->title = $title;
        $announce->content = $content;
        $announce->priority = $priority;
        $announce->user_id = $user->id;

        if ($announce->save()) {
            return CommonFunctions::response(SUCCESS, [
                "announceId" => $announce->id,
                "message" => "Announcements has been created successfully!"
            ]);
        }

        return CommonFunctions::response(FAIL, "Announcements could not be created!");
    }
}
