<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CurrencyRates>
 */
class CurrencyRateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'exchange_rate' => $this->faker->randomFloat(2, 75, 250)
        ];
    }

    public function from(Currency $currency)
    {
        return $this->state(function (array $attributes) use ($currency) {
            return [
                'from_currency_id' => $currency->id,
            ];
        });
    }

    public function to(Currency $currency)
    {
        return $this->state(function (array $attributes) use ($currency) {
            return [
                'to_currency_id' => $currency->id,
            ];
        });
    }

    public function exchangeRate(float $rate)
    {
        return $this->state(function (array $attributes) use ($rate) {
            return [
                'exchange_rate' => $rate,
            ];
        });
    }
}
