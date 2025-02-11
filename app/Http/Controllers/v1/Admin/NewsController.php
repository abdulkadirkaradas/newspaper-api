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
use App\Validators\ChangePostVisiblityValidator;
use App\Validators\CreateNewsCategoryValidator;
use App\Validators\DeleteNewsValidator;

class NewsController extends Controller
{
    /**
     * Returns news by id|type[all, approved, unapproved]
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function index(Request $request): array
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
            return CommonFunctions::response(BAD_REQUEST, "News could not be found!");
        }

        if ($approve && $userNews->approved) {
            return CommonFunctions::response(BAD_REQUEST, "News has been already approved!");
        }

        if (!$approve && !$userNews->approved) {
            return CommonFunctions::response(BAD_REQUEST, "News has been already unapproved!");
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
            return CommonFunctions::response(BAD_REQUEST, "News could not be found!");
        }

        if ($userNews->deleted_at !== null) {
            return CommonFunctions::response(BAD_REQUEST, "News has been already deleted!");
        }

        $userNews->removed_by = $loggedUser->id;

        if ($userNews->save()) {
            $userNews->delete();

            return CommonFunctions::response(SUCCESS, "News successfully deleted!");
        }

        return CommonFunctions::response(BAD_REQUEST, "News could not be deleted!");
    }

    /**
     * Change post visibility directly or with warning message
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function change_post_visibility(Request $request): array
    {
        // Get all parameters
        $params = $request->only(['type', 'userId', 'newsId', 'visibility', 'warning']);

        // Check if the mandatory parameters exists
        if (count($params) === 0 || !isset($params['type']) || !isset($params['userId']) || !isset($params['newsId']) || !isset($params['visibility'])) {
            return CommonFunctions::response(BAD_REQUEST, BAD_REQUEST_MSG);
        }

        // Get user
        $user = User::find($params['userId']);

        // Check if the user exists
        if (!$user) {
            return CommonFunctions::response(BAD_REQUEST, USER_NOT_FOUND);
        }

        // Get news instance from the user relation with the 'newsId'
        $news = $user->news()->find($params['newsId']);

        // Check if the news exists
        if (!$news) {
            return CommonFunctions::response(BAD_REQUEST, NEWS_NOT_FOUND);
        }

        if ($params['type'] === 'message') {
            // Validate request
            $validated = CommonFunctions::validateRequest($request, ChangePostVisiblityValidator::class);

            // Check if the validator returns error
            if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
                return $validated;
            }

            // Extract values to the related variables
            $message = $validated['message'];
            $reason = $validated['reason'];
            $warningLevel = $validated['warning_level'];
            $visibility = $params['visibility'];

            // Create a warning instance with the variables
            $warning = new Warning();
            $warning->message = $message;
            $warning->reason = $reason;
            $warning->warning_level = $warningLevel;

            // Check if the warning instance saved
            if ($user->warnings()->save($warning)) {
                // If saved changes then change post visibility
                $news->visibility = $visibility;

                //Check if the changes saved
                if ($news->save()) {
                    return CommonFunctions::response(SUCCESS, "News visibility has been changed successfully!");
                }
            }
        }

        //TODO In future should be added here default warning message instance
        // Change the post visibility without warning message
        if ($params['type'] === "directly") {
            $news->visibility = $visibility;

            if ($news->save()) {
                return CommonFunctions::response(SUCCESS, "News visibility has been changed successfully!");
            }
        }

        return CommonFunctions::response(FAIL, "News visibility could not be changed!");
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
    public function create_category(Request $request): array
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
                "categoryId" => $category->id,
                "message" => "News category has been created successfully"
            ]);
        }

        return CommonFunctions::response(FAIL, "Failed to create news category!");
    }

    /**
     * Update a category of news record
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function update_news_category(Request $request): array
    {
        $news = $request->providedNews;
        $category = $request->newsCategory;

        if (is_null($news) || is_null($category)) {
            return CommonFunctions::response(BAD_REQUEST, BAD_REQUEST_MSG);
        }

        $news->category_id = $category->id;

        if ($news->save()) {
            return CommonFunctions::response(SUCCESS, "The news record has been update successfully!");
        }

        return CommonFunctions::response(FAIL, "The news could not be update to the category!");
    }
}
