<?php

namespace App\Http\Controllers\v1\Public;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $news = $request->model;

        $news = News::select('id', 'title', 'content', 'created_at')
            ->with([
                'newsImages' => function ($query) {
                    $query->select('user_id', 'news_id', 'name', 'ext', 'fullpath', 'created_at');
                },
                'newsReactions' => function ($query) {
                    $query->select('user_id', 'news_id', 'reaction', 'type', 'created_at');
                }
            ])->findOrFail($news->id);

        return [
            'news' => $news,
        ];
    }
}