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
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_currency_id')->index()->comment('Валюта из которой выполняется конвертация');
            $table->unsignedBigInteger('to_currency_id')->index()->comment('Валюта в которую выполняется конвертация');
            $table->double('exchange_rate');
            $table->timestamps();

            $table->foreign('from_currency_id')->references('id')->on('currencies')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('to_currency_id')->references('id')->on('currencies')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_rates');
    }
};
