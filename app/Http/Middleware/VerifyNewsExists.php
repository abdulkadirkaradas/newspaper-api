<?php

namespace App\Http\Middleware;

use App\Helpers\CommonFunctions;
use App\Models\News;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyNewsExists
{
    /**
     * Verify provided news id exists and return news model instance
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $newsId = $request->input('newsId');

        if (!CommonFunctions::validateUUID($newsId)) {
            return response()->json(CommonFunctions::response(BAD_REQUEST, INVALID_ID_NO));
        }

        $news = News::find($newsId);

        if (!$news) {
            return response()->json(CommonFunctions::response(BAD_REQUEST, BAD_REQUEST_MSG));
        }

        $request['news'] = $news;

        return $next($request);
    }
}
