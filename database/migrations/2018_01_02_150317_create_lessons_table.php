<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tutor_id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('collection_location_id');
            $table->unsignedInteger('drop_off_location_id');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->integer('duration'); // Duration in minutes
            $table->timestamps();
        });

        Schema::table('lessons', function ($table) {
            $table->foreign('tutor_id')
                ->references('id')
                ->on('tutors');
            $table->foreign('student_id')
                ->references('id')
                ->on('students');
            $table->foreign('collection_location_id')
                ->references('id')
                ->on('locations');
            $table->foreign('drop_off_location_id')
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
        Schema::table('lessons', function ($table) {
            $table->dropForeign(['tutor_id']);
            $table->dropForeign(['student_id']);
            $table->dropForeign(['collection_location_id']);
            $table->dropForeign(['drop_off_location_id']);
        });
        Schema::dropIfExists('lessons');
    }
}
