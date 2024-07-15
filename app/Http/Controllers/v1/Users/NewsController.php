<?php

namespace App\Http\Controllers\v1\Users;

use App\Helpers\CommonFunctions;
use App\Models\News;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NewsImages;
use App\Validators\CreateNewsValidator;
use App\Validators\UploadNewsImageValidator;

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

    public function create(Request $request)
    {
        $user = $request->user;

        $validated = CommonFunctions::validateRequest($request, CreateNewsValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $post = new News();
        $post->title = $validated['title'];
        $post->content = $validated['content'];

        if ($user->news()->save($post)) {
            return CommonFunctions::response(SUCCESS, NEWS_CREATED, [
                'newsId' => $post->id
            ]);
        } else {
            return CommonFunctions::response(FAIL, NEWS_CREATION_FAILED);
        }
    }

    public function upload_news_image(Request $request)
    {
        $user = $request->user;
        $news = $request->news;

        $validated = CommonFunctions::validateRequest($request, UploadNewsImageValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        // Remove all characters before index 18 and merge IDs
        $compoundKey = substr($user->id, 19) . '-' . substr($news->id, 19);

        $fullpath = time() . '_' . $compoundKey . '_' . $validated['name'] . '.' . $request['ext'];
        $request->image->move(public_path('images'), $fullpath);

        $newsImage = new NewsImages();
        $newsImage->name = $validated['name'];
        $newsImage->ext = $validated['ext'];
        $newsImage->fullpath = $fullpath;
        $newsImage->user_id = $user->id;

        if ($news->newsImages()->save($newsImage)) {
            return CommonFunctions::response(SUCCESS, NEWS_IMAGE_CREATED);
        } else {
            return CommonFunctions::response(FAIL, NEWS_IMAGE_CREATION_FAILED);
        }
    }

    /**
     * Returns all news reactions of logged user
     *
     * @var Request $request
     * @return array
     */
    public function reactions(Request $request): array
    {
        $user = $request->user;

        $reactions = User::select('id', 'name', 'lastname', 'username')
            ->with([
                'news' => function ($query) {
                    $query->select('id', 'user_id', 'title', 'created_at')
                        ->with([
                            'newsReactions' => function ($query) {
                                //TODO Add time-range based query
                                $query->select('user_id', 'news_id', 'reaction', 'type', 'created_at');
                            }
                        ]);
                }
            ])->findOrFail($user->id);

        return [
            "reactions" => $reactions
        ];
    }
}