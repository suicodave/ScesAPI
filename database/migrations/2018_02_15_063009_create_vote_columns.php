<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('votes')) {
            Schema::table('votes', function (Blueprint $table) {
                $table->integer('election_id')->after('id');
                $table->integer('student_id')->after('election_id');
                $table->integer('candidate_id')->after('student_id');
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
