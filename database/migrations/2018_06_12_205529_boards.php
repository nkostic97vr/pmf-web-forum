<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Boards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('address');
            $table->text('description')->nullable();
            $table->boolean('is_visible')->default(false);
            $table->integer('owner_id');
            $table->integer('directory_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boards');
    }
}
