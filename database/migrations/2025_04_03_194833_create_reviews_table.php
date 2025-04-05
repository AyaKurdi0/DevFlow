<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('review_title')->nullable();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('leader_id');
            $table->unsignedBigInteger('developer_id');
            $table->foreign('task_id')->references('id')->on('tasks');
            $table->foreign('leader_id')->references('id')->on('users');
            $table->foreign('developer_id')->references('id')->on('users');
            $table->enum('reviewStatus',['pending','approved','rejected'])->default('pending');
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('reviews');
    }
};
