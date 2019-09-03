<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uid')->unique();
            $table->string('name');
            $table->string('author');
            $table->string('extension'); 
            $table->integer('influence');
            $table->unsignedbigInteger('user_id')->nullable();
            $table->unsignedbigInteger('folder_id');
            $table->timestamps();
        });

        Schema::table('files', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
