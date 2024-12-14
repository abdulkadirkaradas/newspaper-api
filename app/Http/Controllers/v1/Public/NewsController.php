<?php

namespace App\Http\Controllers\v1\Public;

use App\Helpers\CommonFunctions;
use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NewsCategories;
use App\Models\User;

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
        $type = $request->input('type');

        if (empty($type)) {
            return CommonFunctions::response(BAD_REQUEST, BAD_REQUEST_MSG);
        }

        $news = News::select('id', 'user_id', 'title', 'content', 'created_at')
            ->with([
                'newsImages:id,news_id,name,ext,fullpath,created_at',
                'newsReactions:id,news_id,reaction,type,created_at'
            ])->get();

        $news->each(function ($item) {
            $user = $item->user;
            $item->unsetRelation('user');
            $item->author = [
                'name' => $user->name,
                'lastname' => $user->lastname,
                'username' => $user->username,
            ];
        });

        if ($type === 'user') {
            $providedNews = $request->providedNews;
            $news = $news->find($providedNews->id);
        }

        return [
            'news' => $news,
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