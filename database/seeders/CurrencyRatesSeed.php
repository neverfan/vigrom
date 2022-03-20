<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\CurrencyRate;
use Database\Factories\CurrencyRateFactory;
use Illuminate\Database\Seeder;

class CurrencyRatesSeed extends Seeder
{
    private const BEFORE_DAYS = 10;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usdCurrency = Currency::where('symbol', 'USD')->first();
        $rubCurrency = Currency::where('symbol', 'RUB')->first();

        for ($subDay = self::BEFORE_DAYS; $subDay >= 0; $subDay--) {
            CurrencyRateFactory::new([
                'created_at' => now()->subDays($subDay),
                'updated_at' => now()->subDays($subDay),
            ])
                ->from($rubCurrency)
                ->to($usdCurrency)
                ->create();
        }
    }

}
