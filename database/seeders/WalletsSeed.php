<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class WalletsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i <= 10; $i++) {
            Wallet::factory()->create();
        }
    }
}
