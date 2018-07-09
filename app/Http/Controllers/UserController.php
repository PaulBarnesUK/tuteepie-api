<?php

namespace App\Http\Controllers;

use App\Transformers\UserTransformer;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;
use App\User;
use App\PasswordReset;
Use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class UserController extends ApiController
{
    use JsonableTrait;

    /**
     * POST Request to create a new user.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function create(Request $request)
    {
        $user = new User();
        if (!$user->validate($request->all(), $user->creationRules)) {
            return $user->validationErrorResponse();
        }   

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        if ($user->save() && $this->createPasswordResetToken($request->email)) {
            return fractal()->item($user, new UserTransformer(), 'users')->respond(201);
        } else {
            return response()->json([
                'message' => 'Unable to create user.'
            ], 500);
        }
    }

    /**
     *
     * @param $email
     * @return bool
     */
    private function createPasswordResetToken($email)
    {
        $passwordReset = new PasswordReset();

        $passwordReset->email = $email;
        $passwordReset->token = Hash::make(str_random(255));
        $passwordReset->created_at = Carbon::now();

        if ($passwordReset->save()) {
            return true;
        }

        return false;
    }

    public function update(User $user, Request $request)
    {
        // If user is trying to activate account or change password they need to provide a password reset token
        if ((array_key_exists('activated_at', $request->all())
            || array_key_exists('password', $request->all()))
            && !array_key_exists('token', $request->all())) {
            return $this->errorResponse(422,
                Config::get('constants.response_titles.VALIDATION_ERROR'),
                'Token not provided.');
        }

        // Check that reset token provided matches the one in the database
        $resetToken = PasswordReset::where('email', $user->email)->first();

        if ($resetToken->token !== $request->token) {
            return $this->errorResponse(422,
                config('constants.response_titles.VALIDATION_ERROR'),
                'Token does not match.');
        }

        // Attempt to update the user
        if ($user->updateFields($request->all())) {
            if (PasswordReset::where('email', $user->email)->delete()) {
                return fractal($user, new UserTransformer())->respond(200);
            } else {
                return $this->errorResponse(500,
                    config('constants.response_titles.GENERAL_ERROR'),
                    'Issue when deleting token');
            }
        } else {
            return response()->json([
                'errors' => $user->errors
            ], 422);
        }
    }
}
