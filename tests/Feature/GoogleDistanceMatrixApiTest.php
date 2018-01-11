<?php

namespace Tests\Feature;

use App\Lesson;
use App\Location;
use App\User;
use GuzzleHttp\Client;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GoogleDistanceMatrixApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->artisan('db:seed');

        $apiKey = env('GOOGLE_DISTANCE_MATRIX_API_KEY');

        $guzzle = new Client();
        // Separate unit test to grab a location postcode so we will mock some postcodes here.
        $response = $guzzle->get("https://maps.googleapis.com/maps/api/distancematrix/json?key={$apiKey}&origins=SN3%205AF&destinations=SN1%202SB");
        $jsonResponse = json_decode($response->getBody()->getContents());
        $duration = $jsonResponse->rows[0]->elements[0]->duration->value;

        // Assert that a duration was able to be retrieved
        $this->assertNotEmpty($duration);
        // Assert successful call
        $this->assertEquals(200, $response->getStatusCode());
    }
}
