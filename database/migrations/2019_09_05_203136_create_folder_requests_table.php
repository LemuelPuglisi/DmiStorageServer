<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFolderRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folder_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('user_id');
            $table->string('status');
            $table->unsignedbigInteger('folder_id'); 
            $table->unsignedbigInteger('is_upgrade_of')->nullable()->default(null); 
            $table->json('permissions');
            $table->mediumText('notes');
            $table->integer('lifespan');
            $table->timestamp('requested_at')->useCurrent(); 
            $table->boolean('authorized')->nullable()->default(null);
            $table->unsignedbigInteger('authorizer_id')->nullable()->default(null);
            $table->timestamp('authorized_at')->nullable()->default(null);; 
            $table->timestamp('expiration_date')->nullable()->default(null);; 
        });

        Schema::table('folder_requests', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
            $table->foreign('authorizer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('is_upgrade_of')->references('id')->on('folder_requests');
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
        Schema::dropIfExists('folder_requests');
    }
}
