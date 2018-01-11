<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('tutor_id')->nullable();
            $table->unsignedInteger('default_collection_location')->nullable();
            $table->unsignedInteger('default_drop_off_location')->nullable();
            $table->timestamps();
        });

        Schema::table('students', function ($table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->foreign('tutor_id')
                ->references('id')
                ->on('tutors');
            $table->foreign('default_collection_location')
                ->references('id')
                ->on('locations');
            $table->foreign('default_drop_off_location')
                ->references('id')
                ->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function ($table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['tutor_id']);
            $table->dropForeign(['default_collection_location']);
            $table->dropForeign(['default_drop_off_location']);
        });
        Schema::dropIfExists('students');
    }
}
