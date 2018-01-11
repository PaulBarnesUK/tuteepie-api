<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTutorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tutors', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('default_start_point')->nullable();
            $table->timestamps();
        });

        Schema::table('tutors', function ($table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->foreign('company_id')
                ->references('id')
                ->on('companies');
            $table->foreign('default_start_point')
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
        Schema::table('tutors', function ($table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['company_id']);
            $table->dropForeign(['default_start_point']);
        });
        Schema::dropIfExists('tutors');
    }
}
