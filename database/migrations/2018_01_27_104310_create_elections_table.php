<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->string('department_ids');
            $table->integer('school_year_id');
            $table->boolean('is_active')->default('0');
            $table->boolean('is_published')->default('0');
            $table->boolean('is_party_enabled')->default('0');
            $table->boolean('is_colrep_enabled')->default('0');
            $table->integer('processor_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('elections');
    }
}
