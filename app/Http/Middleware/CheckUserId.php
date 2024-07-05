<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\CommonFunctions;
use Symfony\Component\HttpFoundation\Response;

class CheckUserId
{
    /**
     * Handle incoming 'User ID' requests.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Take id parameter
        $id = $request->route('id');

        $uuidRegex = '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/';

        // Compare taken id parameter with the uuid regex string
        if (!preg_match($uuidRegex, $id)) {
            // Return response message if it not match
            return response()->json(CommonFunctions::response(BAD_REQUEST, INVALID_USER_ID));
        }

        $user = User::find($id);

        // If user couldn't be found, return response message
        if (!$user) {
            return response()->json(CommonFunctions::response(BAD_REQUEST, INVALID_USER_ID, 'User ID does not match!'));
        }

        $request['user'] = $user;

        return $next($request);
    }
}
