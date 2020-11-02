<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            // Фамилия Имя Отчество
            $table->string('full_name')->nullable();
            // Email адрес
            $table->string('email')->nullable();
            // Номер телефона
            $table->string('phone')->nullable();
            // Номера квартир
            $table->string('apartment_numbers')->nullable();
            // Номера парковки
            $table->string('parking_numbers')->nullable();
            // Отправлино ли приглашение?
            $table->boolean('invited')->default(false);
            // Пользователь
            $table->bigInteger('user_id')->unsigned()->index()->nullable();
            $table->string('user_type')->nullable();
            // Дата создания/ редактирования
            $table->timestamps();
            // Мягкое удаление
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
        Schema::dropIfExists('residents');
    }
}
