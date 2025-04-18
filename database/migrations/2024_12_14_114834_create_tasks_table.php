<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type');
            $table->text('description');
            $table->enum('status',['To Do','Doing','Done'])->default('To Do');
            $table->date('estimated_end_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('estimated_start_date')->nullable();
            $table->integer('estimated_time_inDays')->nullable();
            $table->integer('actual_time_inDays')->nullable();
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->enum('priority',[1,2,3,4,5])->default(3);
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
        Schema::dropIfExists('tasks');
    }
}
