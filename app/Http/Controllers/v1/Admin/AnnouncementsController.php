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
     * Return announcements | priority, from, to
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function announcements(Request $request): array
    {
        $params = $request->only(['priority', 'from', 'to']);

        $announces = Announcements::query();

        if (!empty($params['priority'])) {
            $announces->where('priority', $params['priority']);
        }

        if (!empty($params['from']) || !empty($params['to'])) {
            $from = $params['from'] ?? null;
            $to = $params['to'] ?? null;

            if ($from && $to) {
                $announces->whereBetween('created_at', [$from, $to]);
            } elseif ($from) {
                $announces->where('created_at', '>=', $from);
            } elseif ($to) {
                $announces->where('created_at', '<=', $to);
            }
        }

        return [
            "announcements" => $announces->get()
        ];
    }

    /**
     * Return latest announcement
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function latest_announcement(Request $request): array
    {
        $latest = Announcements::latest()->first();

        return [
            "announcement" => $latest
        ];
    }

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
