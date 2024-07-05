<?php

namespace App\Http\Controllers\v1\Users;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    /**
     * Returns logged user news
     *
     * @var Request $request
     * @return array
     */
    public function loggedUserNews(Request $request): array
    {
        $user = $request->user;

        $news = News::select('id', 'title', 'content', 'created_at')
            ->with([
                'newsImages' => function ($query) {
                    $query->select('user_id', 'news_id', 'name', 'ext', 'fullpath', 'created_at');
                },
                'newsReactions' => function ($query) {
                    $query->select('user_id', 'news_id', 'reaction', 'type', 'created_at');
                }
            ])->where('user_id', $user->id)->get();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'lastname' => $user->lastname,
            'username' => $user->username,
            'membership_date' => $user->created_at,
            'news' => $news,
        ];
    }


    /**
     * Returns news by related id
     *
     * @var Request $request
     * @return array
     */
    public function news(Request $request): array
    {
        $news = $request->news;

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
