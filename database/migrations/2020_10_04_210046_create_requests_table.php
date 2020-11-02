<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->json('photo')->nullable();
            $table->string('number')->nullable();

            // Morph to User
            $table->bigInteger('user_id')->unsigned()->index()->nullable();
            $table->string('user_type')->nullable();
            // Step
            $table->bigInteger('step_id')->unsigned()->index()->nullable();
            $table->foreign('step_id')->references('id')->on('steps');

            $table->timestamps();
            $table->softDeletes();
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
