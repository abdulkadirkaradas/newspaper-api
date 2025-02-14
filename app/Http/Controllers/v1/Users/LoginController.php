<?php

namespace App\Http\Controllers\v1\Users;

use Illuminate\Http\Request;
use App\Models\UserAuthTokens;
use App\Helpers\CommonFunctions;
use App\Http\Controllers\Controller;
use App\Validators\UserLoginValidator;

class LoginController extends Controller
{
    public function login(Request $request) {
        $validated = CommonFunctions::validateRequest($request, UserLoginValidator::class);

        if (isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        $token = auth()->attempt($validated);

        if ($token === false) {
            return response()->json([
                'status' => UNAUTHORIZED,
                'message' => UNAUTHORIZED_ACCESS,
            ], UNAUTHORIZED);
        }

        $user = auth()->user();

        $userAuth = UserAuthTokens::create([
            'token' => $token,
            'user_id' => $user->id
        ]);

        if (!$userAuth) {
            return response()->json([
                'status' => INTERNAL_SERVER_ERROR,
                'message' => AN_ERROR_OCCURED,
            ], INTERNAL_SERVER_ERROR);
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

        auth()->logout();

        return response()->json([
            'status' => SUCCESS,
            'message' => 'You have been logged out.',
        ]);
    }

    public function refreshAuthToken(Request $request) {
        $user = $request->user;
        $auth = UserAuthTokens::where('user_id', $request->user->id)->latest()->first();

        if (!$auth) {
            return response()->json([
                'status' => UNAUTHORIZED,
                'message' => "Invalid token"
            ], UNAUTHORIZED);
        }

        $token = auth()->refresh();
        $this->expireAllTokens($user);

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

    protected function expireAllTokens($user)
    {
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
    }
}