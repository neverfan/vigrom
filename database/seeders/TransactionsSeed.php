<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\Wallet;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Seeder;

class TransactionsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransactionFactory::new()->withoutExchange()->create();
    }
}
