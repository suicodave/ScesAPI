<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIsStartedElectionColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('elections', 'is_started')) {
            Schema::table('elections', function (Blueprint $table) {
                $table->boolean('is_started')->default(0)->change();
            });
        }else{
            Schema::table('elections', function (Blueprint $table) {
                $table->boolean('is_started')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
