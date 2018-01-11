<?php

namespace Tests\Feature;

use App\PasswordReset;
use Carbon\Carbon;
use phpDocumentor\Reflection\Types\Parent_;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Illuminate\Support\Facades\Artisan;

class UserCanCreateAccountAndLoginTest extends TestCase
{
    use RefreshDatabase;

    private $user = null;

    /**
     * Create the user's account
     *
     * @return void
     */
    public function testCanLogin()
    {
        // create account
        $this->json('POST', "/api/{$this->apiVersion}/users", [
            'action' => 'create',
            'name' => 'Diane Ruggles',
            'email' => 'dianeruggles@test.com',
            'password' => 'password'
        ])->assertSuccessful();

        $this->user = User::where('name', 'Diane Ruggles')->first();
        $this->assertNotEmpty($this->user);

        $passwordReset = PasswordReset::where('email', 'dianeruggles@test.com')->first();
        $this->assertNotEmpty($passwordReset);

        // activate account
        $this->activateAccount($passwordReset);

        // login
        $loginResponse = $this->attemptLogin();
        $token = $loginResponse->baseResponse->original['token'];

        $this->assertDatabaseMissing('password_resets', [
            'email' => 'dianeruggles@test.com'
        ]);

        // Assert that we can access protected routes
        $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->json('GET', "/api/{$this->apiVersion}/admin")->assertSuccessful();
    }

    public function activateAccount($passwordReset)
    {
        $this->json('PATCH', "/api/{$this->apiVersion}/users/{$this->user->id}", [
            'activated_at' => Carbon::now()->toDateTimeString(),
            'token' => $passwordReset->token
        ])->assertSuccessful();
    }

    public function attemptLogin()
    {
        Artisan::call('passport:install');

        return $this->Json('POST', "/api/{$this->apiVersion}/auth", [
            'email' => 'dianeruggles@test.com',
            'password' => 'password'
        ])->assertSuccessful();
    }
}
