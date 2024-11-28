<?php

namespace App\Http\Controllers\v1\Users;

use App\Helpers\CommonFunctions;
use App\Validators\UserLoginValidator;
use App\Http\Controllers\Controller;
use App\Models\UserAuthTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request) {
        $validated = CommonFunctions::validateRequest($request, UserLoginValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $token = Auth::guard('api')->attempt($validated);

        if ($token === false) {
            return response()->json([
                'status' => UNAUTHORIZED,
                'message' => UNAUTHORIZED_ACCESS,
            ], UNAUTHORIZED);
        }

        $user = auth()->guard('api')->user();

        $userAuth = UserAuthTokens::create([
            'token' => $token,
            'user_id' => $user->id
        ]);

        if (!$userAuth) {
            return response()->json([
                'status' => FAIL,
                'message' => AN_ERROR_OCCURED,
            ], FAIL);
        }

        return response()->json([
                'status' => SUCCESS,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user;

        $authTokens = UserAuthTokens::where('user_id', $user->id)->get();

        if ($authTokens->isNotEmpty()) {
            foreach ($authTokens as $auth) {
                $auth->expire_date = now();
                $auth->last_login = now();
                $auth->expired = true;
                $auth->save();
                $auth->delete();
            }
        }

        Auth::guard('api')->logout();

        return response()->json([
            'status' => SUCCESS,
            'message' => 'You have been logged out.',
        ]);
    }

    public function refreshAuthToken(Request $request) {
        $user = $request->user;
        $token = Auth::guard('api')->refresh();
        $auth = UserAuthTokens::where('user_id', $request->user->id)->latest()->first();

        if (!$auth) {
            return response()->json([
                'status' => FAIL,
                'message' => "Invalid token"
            ]);
        }

        $auth->expired = true;
        $auth->save();
        $auth->delete();

        $userAuth = UserAuthTokens::create([
            'token' => $token,
            'user_id' => $user->id
        ]);

        return response()->json([
            'status' => SUCCESS,
            'authorisation' => [
                'token' => $userAuth->token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function userInformation(Request $request)
    {
        return response()->json([
            'status' => SUCCESS,
            'userInformation' => $request->user
        ]);
    }
}