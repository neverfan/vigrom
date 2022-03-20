<?php

namespace Tests\Unit\Vigrom\Services;

use Tests\TestCase;
use App\Models\Currency;
use Database\Factories\CurrencyRateFactory;
use App\Vigrom\Services\Exchange\ExchangeService;
use App\Vigrom\Services\Exchange\NotExchangeableCurrenciesException;

class ExchangeServiceTest extends TestCase
{
    /**
     * Проверяет, что возвращается исключение в случае попытки обмена одной и той же валюты
     * @return void
     * @throws NotExchangeableCurrenciesException
     */
    public function testExchangeMethodShouldReturnException()
    {
        $usdCurrency = Currency::bySymbol(Currency::USD_CURRENCY_SYMBOL);

        $this->expectException(NotExchangeableCurrenciesException::class);

        (new ExchangeService())->exchange($usdCurrency, $usdCurrency, 10);
    }

    /**
     * Проверяет, что правильно выполняется конвертирование валюты по последнему курсу
     * @return void
     */
    public function testExchangeMethodShouldReturnConvertedAmount()
    {
        $usdCurrency = Currency::bySymbol(Currency::USD_CURRENCY_SYMBOL);
        $rubCurrency = Currency::bySymbol(Currency::RUB_CURRENCY_SYMBOL);

        CurrencyRateFactory::new([
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
            ])
            ->from($usdCurrency)
            ->to($rubCurrency)
            ->exchangeRate(136.10)
            ->create();

        CurrencyRateFactory::new([
            'created_at' => now(),
            'updated_at' => now(),
        ])
            ->from($usdCurrency)
            ->to($rubCurrency)
            ->exchangeRate(107.07)
            ->create();

        $amount = (new ExchangeService())->exchange($usdCurrency, $rubCurrency, 10);

        $this->assertEquals(1070.7, $amount);
    }

}
