<?php

namespace Tests\Unit;

use App\Location;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CanRetrievePostcodeFromLocationTest extends TestCase
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

        $locations = Location::all();
        $postcode = $locations->random()->postcode;
        $this->assertNotNull($postcode);
    }
}
