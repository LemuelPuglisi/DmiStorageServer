<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('user_id');
            $table->string('status');
            $table->unsignedbigInteger('course_id'); 
            $table->mediumText('notes');
            $table->integer('lifespan');
            $table->timestamp('requested_at')->useCurrent(); 
            $table->boolean('authorized')->nullable()->default(null);
            $table->unsignedbigInteger('authorizer_id')->nullable()->default(null);
            $table->timestamp('authorized_at')->nullable()->default(null);; 
            $table->timestamp('expiration_date')->nullable()->default(null);; 
        });

        Schema::table('course_requests', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade'); 
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
        Schema::dropIfExists('course_requests');
    }
}
