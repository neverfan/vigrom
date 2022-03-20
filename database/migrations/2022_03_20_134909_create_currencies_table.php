<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->comment('Буквенное обозначение валюты');
            $table->string('title')->comment('Название валюты');
            $table->timestamps();
        });

        DB::table('currencies')->insert($this->getCurrencies());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }

    private function getCurrencies(): array
    {
        return [
            [
                'symbol' => 'USD',
                'title' => 'Доллар США',
            ],
            [
                'symbol' => 'RUB',
                'title' => 'Российский рубль',
            ]
        ];
    }
};
