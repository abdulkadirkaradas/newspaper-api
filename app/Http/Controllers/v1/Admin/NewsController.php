<?php

namespace App\Http\Controllers\v1\Admin;

use App\Models\News;
use App\Models\NewsCategories;
use App\Models\User;
use App\Models\Warning;
use Illuminate\Http\Request;
use App\Helpers\CommonFunctions;
use App\Http\Controllers\Controller;
use App\Validators\ApproveNewsValidator;
use App\Validators\ChangeNewsVisibilityValidator;
use App\Validators\ChangePostVisiblityValidator;
use App\Validators\CreateNewsCategoryValidator;
use App\Validators\DeleteNewsValidator;
use App\Validators\UpdateNewsCategoryValidator;

class NewsController extends Controller
{
    /**
     * Returns news by id|type[all, approved, unapproved]
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $params = $request->only(['type', 'userId']);

        if (empty($params)) {
            return CommonFunctions::response(BAD_REQUEST, BAD_REQUEST_MSG);
        }

        if (!isset($params['type'])) {
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

        if (isset($params['userId'])) {
            $user = User::find($params['userId']);

            if ($user === null) {
                return CommonFunctions::response(NOT_FOUND, USER_NOT_FOUND);
            }

            $newsInfo = $news->where('user_id', $user->id);
        }

        if (($params['type'] === "all")) {
            $newsInfo = $news;
        } elseif (($params['type'] === "approved")) {
            $newsInfo = $news->where('approved', true);
        } else {
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
    public function approve(News $news, Request $request)
    {
        $validated = CommonFunctions::validateRequest($request, ApproveNewsValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $authorizedUser = $request->user;
        $user = User::find($validated['userId']);
        $approve = (bool) $validated['approve'];

        $userNews = $user->news()->find($news->id);

        if ($userNews === null) {
            return CommonFunctions::response(NOT_FOUND, "News could not be found!");
        }

        if ($approve && $userNews->approved) {
            return CommonFunctions::response(CONFLICT, "News has been already approved!");
        }

        if (!$approve && !$userNews->approved) {
            return CommonFunctions::response(CONFLICT, "News has been already unapproved!");
        }

        $userNews->approved = $approve;
        $userNews->approved_by = $authorizedUser->id;
        $message = $approve ? "News succesfully approved!" : "News succesfully unapproved!";

        return $userNews->save()
            ? CommonFunctions::response(SUCCESS, $message)
            : CommonFunctions::response(SUCCESS, "Failed to update approvment status");
    }

    /**
     * Delete news
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function delete(News $news, Request $request)
    {
        $validated = CommonFunctions::validateRequest($request, DeleteNewsValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $loggedUser = $request->user;
        $user = User::find($validated['userId']);

        $userNews = $user->news()->find($news->id);

        if ($userNews === null) {
            return CommonFunctions::response(NOT_FOUND, "News could not be found!");
        }

        if ($userNews->deleted_at !== null) {
            return CommonFunctions::response(CONFLICT, "News has been already deleted!");
        }

        $userNews->removed_by = $loggedUser->id;

        if ($userNews->save()) {
            $userNews->delete();

            return CommonFunctions::response(SUCCESS, "News successfully deleted!");
        }

        return CommonFunctions::response(INTERNAL_SERVER_ERROR, "News could not be deleted!");
    }

    /**
     * Change post visibility directly or with warning message
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function updateVisibility(News $news, Request $request)
    {
        $validated = CommonFunctions::validateRequest($request, ChangeNewsVisibilityValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $user = User::find($validated['userId']);

        if (!$user) {
            return CommonFunctions::response(NOT_FOUND, USER_NOT_FOUND);
        }

        if ($validated['type'] === 'message') {
            $message = $validated['warning']['message'];
            $reason = $validated['warning']['reason'];
            $warningLevel = $validated['warning']['warningLevel'];
            $visibility = (bool) $validated['visibility'];

            $warning = new Warning();
            $warning->message = $message;
            $warning->reason = $reason;
            $warning->warning_level = $warningLevel;

            if ($user->warnings()->save($warning)) {
                $news->visibility = $visibility;

                if ($news->save()) {
                    return CommonFunctions::response(SUCCESS, "News visibility has been changed successfully!");
                }
            }
        } else if ($validated['type'] === "directly") {
            //TODO In future should be added here default warning message instance
            // Change the post visibility without warning message
            $news->visibility = $validated['visibility'];

            if ($news->save()) {
                return CommonFunctions::response(SUCCESS, "News visibility has been changed successfully!");
            }
        }

        return CommonFunctions::response(INTERNAL_SERVER_ERROR, "News visibility could not be changed!");
    }

    /**
     * Return all news categories
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function categories(Request $request): array
    {
        $categories = NewsCategories::all();

        return [
            "categories" => $categories
        ];
    }

    /**
     * Create a news category
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function createCategory(Request $request)
    {
        $validated = CommonFunctions::validateRequest($request, CreateNewsCategoryValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $category = new NewsCategories();
        $category->name = $validated['name'];
        $category->description = $validated['description'];

        if ($category->save()) {
            return CommonFunctions::response(SUCCESS, [
                "category" => $category,
                "message" => "News category has been created successfully"
            ]);
        }

        return CommonFunctions::response(INTERNAL_SERVER_ERROR, "Failed to create news category!");
    }

    /**
     * Update a category of news record
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function updateCategory(NewsCategories $category, Request $request)
    {
        $validated = CommonFunctions::validateRequest($request, UpdateNewsCategoryValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $news = News::find($validated['newsId']);

        if ($news === null) {
            return CommonFunctions::response(BAD_REQUEST, BAD_REQUEST_MSG);
        }

        $news->category_id = $category->id;

        if ($news->save()) {
            return CommonFunctions::response(SUCCESS, "The news record has been update successfully!");
        }

        return CommonFunctions::response(INTERNAL_SERVER_ERROR, "The news could not be update to the category!");
    }
}