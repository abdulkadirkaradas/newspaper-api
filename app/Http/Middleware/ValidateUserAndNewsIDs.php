<?php

namespace App\Http\Middleware;

use App\Helpers\CommonFunctions;
use App\Models\News;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateUserAndNewsIDs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Take id parameter
        $id = $request->route('id');

        if (!CommonFunctions::checkUUIDValid($id)) {
            return response()->json(CommonFunctions::response(BAD_REQUEST, INVALID_ID_NO));
        }

        $instance = str_contains($request->url(), '/post') ? News::find($id) : User::find($id);

        // If user couldn't be found, return response message
        if (!$instance) {
            return response()->json(CommonFunctions::response(BAD_REQUEST, INVALID_ID_NO, 'ID does not match!'));
        }

        $request['model'] = $instance;

        return $next($request);
    }
}
