<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\News;
use Illuminate\Http\Request;
use App\Helpers\CommonFunctions;
use Symfony\Component\HttpFoundation\Response;

class CheckNewsId
{
    /**
     * Handle incoming 'News ID' requests.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $newsId = $request->route('id');

        if (!CommonFunctions::checkUUIDValid($newsId)) {
            return response()->json(CommonFunctions::response(BAD_REQUEST, INVALID_NEWS_ID));
        }

        $news = News::find($newsId);

        // If user couldn't be found, return response message
        if (!$news) {
            return response()->json(CommonFunctions::response(BAD_REQUEST, INVALID_NEWS_ID, 'News ID does not match!'));
        }

        $request['news'] = $news;

        return $next($request);
    }
}
