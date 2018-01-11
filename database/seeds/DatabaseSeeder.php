<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('passport:install');

        // Test data
        factory(App\Tutor::class, 10)->create();
        factory(App\Student::class, 10)->create();
        factory(App\Lesson::class, 50)->create();
    }
}
