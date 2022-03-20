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
        Schema::create('transaction_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Техническое название причины изменения счета');
            $table->string('title')->comment('Название причины изменения счета');
            $table->string('description')->comment('Описание причины изменения счета');
            $table->timestamps();
        });

        DB::table('transaction_reasons')->insert($this->transactionReasonsData());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_reasons');
    }

    private function transactionReasonsData(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'stock',
                'title' => 'Пополнение средств',
                'description' => 'Операция для пополнения',
            ],
            [
                'id' => 2,
                'name' => 'refund',
                'title' => 'Возврат средств',
                'description' => 'Операция возврата средств',
            ]
        ];
    }
};
