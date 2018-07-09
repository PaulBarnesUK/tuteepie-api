<?php

namespace App\Http\Controllers;

use App\Transformers\AuthTransformer;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use App\Transformers\UserTransformer;

class AuthController extends Controller
{
    use JsonableTrait;

    /**
     * Get authenticated user
     */
    public function getAuthenticatedUser() {
        $user = auth()->user();;

        if (!$user) {
            return response()->json([
                'message' => 'No authenticated user found.'
            ], 404);
        }
        
        return fractal()->item($user, new UserTransformer(), 'users')
            ->includeType()
            ->respond(200); 
    }

    /**
     * Create a new web token for the user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {

        $user = new User();

        if (!$user->validate($request->all(), $user->loginRules)) {
            return $user->validationErrorResponse();
        }

        $user = $user->where('email', $request->email)->first();

        // User must have been activated
        if (!$user->activated_at) {
            return $this->errorResponse(401, config('constants.response_titles.UNAUTHORISED'), 'Account is not activated');
        }

        // Check if password matches
        if (!Hash::check($request->password, $user->password)) {
            $user->errors = [
                'password' => ['The password is incorrect.']
            ];
            return $user->validationErrorResponse();
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
