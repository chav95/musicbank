<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMusicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('musics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('judul');
            $table->string('path');
            $table->string('di_playlist')->nullable();
            $table->string('artis')->nullable();
            $table->string('album')->nullable();
            $table->string('genre')->nullable();
            $table->integer('durasi')->nullable();
            $table->string('status')->default(1);
            $table->timestamp('produced_at');
            $table->integer('uploaded_by')->nullable();
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
        Schema::dropIfExists('musics');
    }
}
