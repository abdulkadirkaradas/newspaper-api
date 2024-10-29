<?php

namespace App\Http\Controllers\v1\Public;

use App\Helpers\CommonFunctions;
use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NewsCategories;

/**
 * All those functions have been added for unauthenticated users
 */
class NewsController extends Controller
{
    /**
     * Returns news by related id
     *
     * @var Request $request
     * @return array
     */
    public function news(Request $request): array
    {
        $providedNews = $request->providedNews;
        $type = $request->input('type');

        if (!isset($type) || is_null($type) || empty($type)) {
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

        if ($type === "user") {
            $news->find($providedNews->id);
        }

        return [
            'news' => $news->get(),
        ];
    }

    /**
     * Return all news categories
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function categories(Request $request): array
    {
        //TODO: Filtering categories by date in the future should be added..
        $categories = NewsCategories::
            select('name', 'description')
            ->get();

        return [
            "categories" => $categories
        ];
    }
}