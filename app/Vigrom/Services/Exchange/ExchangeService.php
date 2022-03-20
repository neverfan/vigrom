<?php

namespace App\Vigrom\Services\Exchange;

use App\Models\Currency;
use App\Models\CurrencyRate;

/**
 * Сервис выполняющий конвертацию валют
 */
class ExchangeService
{
    /**
     * Пересчитать одну валюту в другую
     * @param Currency $fromCurrency
     * @param Currency $toCurrency
     * @param float $amount
     * @return float
     * @throws NotExchangeableCurrenciesException
     */
    public function exchange(Currency $fromCurrency, Currency $toCurrency, float $amount): float
    {
        $this->checkExchangeable($fromCurrency, $toCurrency);

        $exchangeRate = $this->getExchangeRate($fromCurrency, $toCurrency);

        return $this->convert($amount, $exchangeRate);
    }

    /**
     * @param Currency $fromCurrency
     * @param Currency $toCurrency
     * @return void
     * @throws NotExchangeableCurrenciesException
     */
    private function checkExchangeable(Currency $fromCurrency, Currency $toCurrency): void
    {
        if ($fromCurrency->id === $toCurrency->id) {
            throw new NotExchangeableCurrenciesException();
        }
    }

    /**
     * Получить обменный курс для конвертации валюты
     * @param Currency $fromCurrency
     * @param Currency $toCurrency
     * @return float
     */
    public function getExchangeRate(Currency $fromCurrency, Currency $toCurrency): float
    {
        return CurrencyRate::query()
            ->fromCurrency($fromCurrency)
            ->toCurrency($toCurrency)
            ->orderByDesc('created_at')
            ->first()
            ->exchange_rate;
    }

    /**
     * Выполнить конвертацию
     * @param float $amount
     * @param float $exchangeRate
     * @return float
     */
    private function convert(float $amount, float $exchangeRate): float
    {
        return round($amount * $exchangeRate, 2);
    }
}
