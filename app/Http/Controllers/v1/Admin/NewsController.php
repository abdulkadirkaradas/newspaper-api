<?php

namespace App\Http\Controllers\v1\Admin;

use App\Models\News;
use Illuminate\Http\Request;
use App\Helpers\CommonFunctions;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    /**
     * Returns news by id|type[all, approved, unapproved]
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function news(Request $request): array
    {
        $user = $request->providedUser;
        $params = $request->only(['type']);

        if (count($params) === 0 && !$user) {
            return CommonFunctions::response(BAD_REQUEST, BAD_REQUEST_MSG);
        }

        $news = News::select(
            'id',
            'title',
            'content',
            'approved',
            'approved_by',
            'removed_by',
            'created_at'
        )
            ->with([
                'newsImages' => function ($query) {
                    $query->select('user_id', 'news_id', 'name', 'ext', 'fullpath', 'created_at');
                },
                'newsReactions' => function ($query) {
                    $query->select('user_id', 'news_id', 'reaction', 'type', 'created_at');
                }
            ]);

        if ($user) {
            $newsInfo = $news->where('user_id', $user->id);
        }

        if (isset($params['type']) && ($params['type'] === "all")) {
            $newsInfo = $news;
        }

        if (isset($params['type']) && ($params['type'] === "approved")) {
            $newsInfo = $news->where('approved', true);
        }

        if (isset($params['type']) && ($params['type'] === "unapproved")) {
            $newsInfo = $news->where('approved', false);
        }

        return [
            'news' => $newsInfo->get(),
        ];
    }

    /**
     * Approve news
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function approve(Request $request): array
    {
        $loggedUser = $request->user;
        $user = $request->providedUser;
        $news = $request->providedNews;

        $userNews = $user->news()->find($news->id);

        if ($userNews->approved === true) {
            return CommonFunctions::response(FAIL, "News has been already approved!");
        }

        if ($userNews) {
            $userNews->approved = true;
            $userNews->approved_by = $loggedUser->id;

            if ($userNews->save()) {
                return CommonFunctions::response(SUCCESS, "News succesfully approved!");
            }
        }

        return CommonFunctions::response(FAIL, "News could not be found!");
    }

    /**
     * Delete news
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function delete(Request $request): array
    {
        $loggedUser = $request->user;
        $user = $request->providedUser;
        $news = $request->providedNews;

        $userNews = $user->news()->find($news->id);

        if ($userNews->deleted_at !== null) {
            return CommonFunctions::response(FAIL, "News has been already deleted!");
        }

        if ($userNews) {
            $userNews->removed_by = $loggedUser->id;

            if ($userNews->save()) {
                $userNews->delete();

                return CommonFunctions::response(SUCCESS, "News successfully deleted!");
            }
        }

        return CommonFunctions::response(FAIL, "News could not be found!");
    }

    //TODO Add API function to allow admins or moderators to change the status of approved news
    //TODO Also this function, needs the refactoring 'news' table, because new columns must be added
    //TODO Columns are 'visibility' etc.
}
