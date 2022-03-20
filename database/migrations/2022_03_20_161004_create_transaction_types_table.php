<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Техническое название транзакции');
            $table->string('title')->comment('Название транзакции');
            $table->string('description')->comment('Описание транзакции');
            $table->timestamps();
        });

        DB::table('transaction_types')->insert($this->transactionTypesData());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_types');
    }

    private function transactionTypesData(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'debit',
                'title' => 'Транзакция списания',
                'description' => 'Операция подразумевающая списание средств со счета',
            ],
            [
                'id' => 2,
                'name' =>'credit',
                'title' => 'Транзакция пополнения',
                'description' => 'Операция подразумевающая пополнение средств на счете',
            ]
        ];
    }
};
