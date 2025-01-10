<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team__members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('developer_id');
            $table->unsignedBigInteger('specialization_id');
            $table->foreign('team_id')->references('id')->on('teams');  
            $table->foreign('developer_id')->references('id')->on('users');           
            $table->foreign('specialization_id')->references('id')->on('specializations');
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
        Schema::dropIfExists('team__members');
    }
}
