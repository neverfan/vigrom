<?php

namespace Database\Factories;

use App\Models\TransactionReason;
use App\Models\TransactionType;
use App\Models\Wallet;
use App\Models\Currency;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $amount = $this->faker->randomFloat(2, 100, 300);

        return [
            'wallet_id' => Wallet::factory(),
            'transaction_type_id' => $this->faker->randomElement(array_values(TransactionType::TYPES)),
            'transaction_reason_id' => $this->faker->randomElement(array_values(TransactionReason::REASONS)),
            'currency_id' => Currency::all()->random(),
            'currency_amount' => $amount,
            'currency_exchange_rate' => null,
            'amount' => $amount,
        ];
    }

    /**
     * Прямая транзакция в валюте кошелька без конвертации
     * @param Wallet|null $wallet
     * @param float|null $amount
     * @return TransactionFactory
     */
    public function withoutExchange(Wallet $wallet = null, float $amount = null)
    {
        $wallet = $wallet ?? Wallet::factory()->create();
        $amount = $amount ?? $this->faker->randomFloat(2, 100, 300);

        return $this->state(function (array $attributes) use ($wallet, $amount) {
            return [
                'wallet_id' => $wallet->id,
                'currency_id' => $wallet->currency->id,
                'currency_amount' => $amount,
                'amount' => $amount
            ];
        });
    }

    /**
     * Транзакция с конвертацией в валюту кошелька
     * @return TransactionFactory
     */
    public function withExchange(Wallet $wallet, Currency $currency, float $currencyAmount = null, float $exchangeRate = null)
    {
        return $this->state(function (array $attributes) use ($wallet, $currency, $currencyAmount, $exchangeRate) {
            return [
                'wallet_id' => $wallet->id,
                'currency_id' => $currency->id,
                'currency_amount' => $currencyAmount,
                'currency_exchange_rate' => $exchangeRate,
                'amount' => $currencyAmount * $exchangeRate
            ];
        });
    }


}
