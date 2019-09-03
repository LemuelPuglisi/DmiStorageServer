<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('display_name')->unique();
            $table->string('storage_name')->unique();
            $table->integer('influence');
            $table->unsignedbigInteger('subfolder_of')->nullable();
            $table->unsignedbigInteger('course_id');
            $table->unsignedbigInteger('creator_id')->nullable();
            $table->timestamps();
        });

        Schema::table('folders', function (Blueprint $table) {
            $table->foreign('subfolder_of')->references('id')->on('folders')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
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
        Schema::dropIfExists('folders');
    }
}
