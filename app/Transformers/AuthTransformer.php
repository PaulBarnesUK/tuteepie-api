<?php

namespace App\Transformers;

use App\OauthAccessToken;
use League\Fractal\TransformerAbstract;

class AuthTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'user'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(OauthAccessToken $accessToken)
    {
        return [
            'type' => 'accessToken',
            'token' => $accessToken->id,
            'created_at' => $accessToken->created_at,
            'updated_at' => $accessToken->updated_at,
            'expires_at' => $accessToken->expires_at
        ];
    }


    public function includeUser(OauthAccessToken $accessToken)
    {
        $user = $accessToken->user;

        return $this->item($user, new UserTransformer());
    }
}
