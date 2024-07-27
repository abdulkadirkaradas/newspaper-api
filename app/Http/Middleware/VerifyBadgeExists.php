<?php

namespace App\Http\Middleware;

use App\Helpers\CommonFunctions;
use App\Models\Badge;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyBadgeExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $badgeId = $request->input('badgeId');

        if (!CommonFunctions::validateUUID($badgeId)) {
            return response()->json(CommonFunctions::response(BAD_REQUEST, INVALID_ID_NO));
        }

        $badge = Badge::find($badgeId);

        if (!$badge) {
            return response()->json(CommonFunctions::response(BAD_REQUEST, BAD_REQUEST_MSG));
        }

        $request['badge'] = $badge;

        return $next($request);
    }
}
