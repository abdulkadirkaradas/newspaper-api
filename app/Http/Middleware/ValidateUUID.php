<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\CommonFunctions;
use App\Models\News;
use Symfony\Component\HttpFoundation\Response;

class ValidateUUID
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $params = $request->only(['user_id', 'news_id']);

        if (count($params) === 0) {
            return $next($request);
        }

        if (!CommonFunctions::validateUUID($params)) {
            return response()->json(CommonFunctions::response(BAD_REQUEST, INVALID_ID_NO));
        }

        $models = [
            'user' => [User::class, 'user_id', 'providedUser', USER_NOT_FOUND],
            'news' => [News::class, 'news_id', 'providedNews', NEWS_NOT_FOUND]
        ];

        foreach ($models as [$modelClass, $paramKey, $responseKey, $notFoundMessage]) {
            if (isset($params[$paramKey])) {
                $model = $modelClass::find($params[$paramKey]);

                if (!$model) {
                    return response()->json(CommonFunctions::response(BAD_REQUEST, $notFoundMessage));
                }

                $request[$responseKey] = $model;
            }
        }

        return $next($request);
    }
}