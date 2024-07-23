<?php

namespace App\Http\Controllers\v1\Admin;

use App\Helpers\CommonFunctions;
use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Returns news by id|type[all]
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function news(Request $request): array
    {
        $user = $request->providedUser;
        $params = $request->only(['id', 'type']);

        if (count($params) === 0 && (!isset($params['id']) || !isset($params['type']))) {
            return CommonFunctions::response(BAD_REQUEST, BAD_REQUEST_MSG);
        }

        $news = News::select('id', 'title', 'content', 'created_at')
            ->with([
                'newsImages' => function ($query) {
                    $query->select('user_id', 'news_id', 'name', 'ext', 'fullpath', 'created_at');
                },
                'newsReactions' => function ($query) {
                    $query->select('user_id', 'news_id', 'reaction', 'type', 'created_at');
                }
            ]);

        if (isset($params['id'])) {
            $newsInfo = $news->where('user_id', $user->id)->get();
        }

        if (!isset($params['id']) && isset($params['type']) && ($params['type'] === "all")) {
            $newsInfo = $news->get();
        }

        return [
            'news' => $newsInfo,
        ];
    }
}
