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
            $table->integer('user_id')->nullable();
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string("gender")->nullable();
            $table->date('birthdate')->nullable();
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('home_address');
            $table->integer('department_id');
            $table->integer('year_level_id');
            $table->integer('college_id')->nullable();
            $table->integer('school_year_id');
            $table->integer('processed_by')->nullable();
            $table->string('profile_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
