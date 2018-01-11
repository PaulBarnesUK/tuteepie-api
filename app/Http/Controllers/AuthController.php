<?php

namespace App\Http\Controllers;

use App\Transformers\AuthTransformer;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class AuthController extends ApiController
{
    /**
     * Create a new web token for the user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {
        $user = User::where('email', $request->email)->first();

        // User must have been activated
        if (!$user->activated_at) {
            return $this->errorResponse(401, Config::get('constants.response_titles.NOT_ACTIVATED'));
        }

        // Check if password matches
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Password incorrect'
            ], 401);
        }

        $token = $user->createToken('base')->accessToken;

        if (!$token) {
            return $this->errorResponse(
                500,
                Config::get('constants.response_titles.GENERAL_ERROR'),
                'Token could not be generated'
            );
        }

        return response()->json([
            'token' => $token
        ]);
    }
}
