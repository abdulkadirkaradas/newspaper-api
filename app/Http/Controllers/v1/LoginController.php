<?php

namespace App\Http\Controllers\v1;

use App\Validators\UserLoginValidator;
use App\Http\Controllers\Controller;
use App\Models\UserAuthTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request) {
        $validated = UserLoginValidator::validate($request);

        if (gettype($validated) === 'array' && isset($validated['status']) && $validated['status'] === BAD_REQUEST) {
            return $validated;
        }

        Auth::attempt($validated);
        $token = UserAuthTokens::where('user_id', $request->user->id)->first();

        if (!$token) {
            return response()->json([
                'status' => UNAUTHORIZED,
                'message' => UNAUTHORIZED_ACCESS,
            ], 401);
        }

        return response()->json([
                'status' => SUCCESS,
                'authorisation' => [
                    'token' => $token->token,
                    'type' => 'bearer',
                ]
            ]);
    }

    public function refreshAuthToken(Request $request) {
        $token = Auth::guard('api')->refresh();

        $auth = UserAuthTokens::where('user_id', $request->user->id)->first();

        if ($auth) {
            $auth->token = $token;
            $auth->expire_date = now()->addDays(15);
            $auth->save();
        }

        return response()->json([
            'status' => SUCCESS,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }
}