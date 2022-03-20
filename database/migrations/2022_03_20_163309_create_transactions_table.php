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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_id')->index()->comment('Кошелек');
            $table->unsignedBigInteger('transaction_type_id')->index()->comment('Тип транзакции');
            $table->unsignedBigInteger('transaction_reason_id')->comment('Причина изменения счета');
            $table->unsignedBigInteger('currency_id')->index()->comment('Валюта операции');
            $table->double('currency_amount')->comment('Сумма в валюте операции');
            $table->double('currency_exchange_rate')->nullable()->comment('Курс конвертации примененный в операции');
            $table->double('amount')->comment('Сумма транзакции в валюте кошелька');
            $table->timestamps();

            $table->foreign('wallet_id')->references('id')->on('wallets')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('transaction_type_id')->references('id')->on('transaction_types')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('transaction_reason_id')->references('id')->on('transaction_reasons')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('currency_id')->references('id')->on('currencies')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
