<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->foreign('task_id')->references('id')->on('tasks');
            $table->string('title');
            $table->longText('details')->nullable();
            $table->unsignedBigInteger('developer_id');
            $table->foreign('developer_id')->references('id')->on('users');
            $table->longText('challenges')->nullable();
            $table->longText('result')->nullable();
            $table->dateTime('Report_date_time')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
