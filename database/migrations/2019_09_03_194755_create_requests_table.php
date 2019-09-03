<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('user_id');
            $table->string('status');
            $table->unsignedbigInteger('course_id');
            $table->unsignedbigInteger('folder_id')->nullable();
            $table->boolean('is_upgrade')->default(false); 
            $table->json('permissions'); 
            $table->mediumText('notes');
            $table->boolean('authorized')->nullable();
            $table->unsignedbigInteger('authorizer_id')->nullable(); 
            $table->timestamp('requested_at')->useCurrent(); 
            $table->integer('lifespan');
            $table->timestamp('authorized_at')->nullable(); 
            $table->timestamp('expiration_date')->nullable();   
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade'); 
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
            $table->foreign('authorizer_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('requests');
    }
}
