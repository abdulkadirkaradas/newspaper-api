<?php

namespace App\Http\Controllers\v1\Users;

use App\Models\News;
use App\Models\User;
use App\Models\NewsImages;
use App\Models\OppositeNews;
use Illuminate\Http\Request;
use App\Models\NewsCategories;
use App\Helpers\CommonFunctions;
use App\Http\Controllers\Controller;
use App\Validators\CreateNewsValidator;
use App\Validators\UploadNewsImageValidator;
use App\Validators\CreateOppositeNewsValidator;

class NewsController extends Controller
{
    /**
     * Returns logged user news
     *
     * @var Request $request
     * @return array
     */
    public function logged_user_news(Request $request): array
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

    public function store(Request $request)
    {
        $user = $request->user;

        $validated = CommonFunctions::validateRequest($request, CreateNewsValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $category = NewsCategories::find($validated['categoryId']);

        if ($category === null) {
            return CommonFunctions::response(BAD_REQUEST, "Category could not be found!");
        }

        $post = new News();
        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->priority = $user->role === DEFAULT_USER_ROLE ? DEFAULT_NEWS_PRIORITY : $validated['priority'];
        $post->category_id = $category->id;

        if ($user->news()->save($post)) {
            return CommonFunctions::response(SUCCESS, [
                'news' => $post,
                'message' => NEWS_CREATED
            ]);
        }

        return CommonFunctions::response(BAD_REQUEST, NEWS_CREATION_FAILED);
    }

    public function uploadImage(News $news, Request $request)
    {
        $user = $request->user;

        $validated = CommonFunctions::validateRequest($request, UploadNewsImageValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        // Remove all characters before index 18 and merge IDs
        $compoundKey = substr($user->id, 19) . '-' . substr($news->id, 19);

        $fullpath = time() . '_' . $compoundKey . '_' . $validated['name'] . '.' . $request['ext'];
        $request->image->move(public_path('newsImages'), $fullpath);

        $newsImage = new NewsImages();
        $newsImage->name = $validated['name'];
        $newsImage->ext = $validated['ext'];
        $newsImage->fullpath = $fullpath;
        $newsImage->user_id = $user->id;

        if ($news->newsImages()->save($newsImage)) {
            return CommonFunctions::response(SUCCESS, NEWS_IMAGE_CREATED);
        } else {
            return CommonFunctions::response(BAD_REQUEST, NEWS_IMAGE_CREATION_FAILED);
        }
    }

    /**
     * Create opposition to an existing news
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function createOpposition(News $sourceNews, Request $request)
    {
        // Get logged user instance
        $loggedUser = $request->user;

        // Validate parameters
        $validated = CommonFunctions::validateRequest($request, CreateOppositeNewsValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        // Get source user instance
        $sourceUser = User::find($validated['sourceUserId']);

        // Check if source user is exists
        if ($sourceUser === null) {
            return CommonFunctions::response(BAD_REQUEST, USER_NOT_FOUND);
        }

        // Create opposition news
        $post = new News([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'opposition' => true,
            'priority' => $loggedUser->role === DEFAULT_USER_ROLE ? DEFAULT_NEWS_PRIORITY : $validated['priority'],
            'category_id' => $validated['categoryId'],
        ]);

        // Check if opposition news is created
        if (!$loggedUser->news()->save($post)) {
            return CommonFunctions::response(BAD_REQUEST, NEWS_CREATION_FAILED);
        }

        // Create opposition reacord to the pivot table
        $oppositeNews = new OppositeNews([
            'source_user_id' => $sourceUser->id,
            'opposite_user_id' => $loggedUser->id,
            'source_news_id' => $sourceNews->id,
            'opposite_news_id' => $post->id
        ]);

        // Check if pivot record is created
        if (!$oppositeNews->save()) {
            // if not created remove opposition news
            $post->delete();
            return CommonFunctions::response(BAD_REQUEST, "Opposite news could not be created!");
        }

        $post->opposition_news_id = $oppositeNews->id;

        if (!$post->save()) {
            // If post could not be updated, delete opposition news and pivot table record
            $oppositeNews->delete();
            $post->delete();
            return CommonFunctions::response(BAD_REQUEST, "Failed to update opposition news ID!");
        }

        // Update 'opposition_news_id' column of source news
        $sourceNews->opposition_news_id = $post->id;
        if (!$sourceNews->save()) {

        }

        return CommonFunctions::response(SUCCESS, [
            'oppositeNews' => $oppositeNews,
            'message' => "Opposite news has been created successfully!"
        ]);
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