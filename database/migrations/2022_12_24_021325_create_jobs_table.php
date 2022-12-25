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
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("title");
            $table->text("image");
            $table->text('description');
            $table->text('keyword');
            $table->string("price",50);
            $table->string("completein",50);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('categ_id');
            $table->unsignedBigInteger('subcateg_id');
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
        Schema::dropIfExists('jobs');
    }
};
