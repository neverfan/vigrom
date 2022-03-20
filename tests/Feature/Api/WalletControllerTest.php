<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Wallet;
use App\Models\Currency;
use App\Models\TransactionReason;
use App\Models\TransactionType;
use Database\Factories\CurrencyRateFactory;
use Database\Factories\WalletFactory;

class WalletControllerTest extends TestCase
{
    /**
     * Проверяет, что возвращается ошибка в случае если не передан wallet_id
     * @return void
     */
    public function testMethodGetBalanceShouldReturnValidationExceptionWithRequiredParam()
    {
        $response = $this->json('GET', route('api.wallet.balance.get'));

        $response->assertJsonFragment([
            'errors' => [
                'wallet_id' => ["The wallet id field is required."]
            ]
        ]);
    }

    /**
     * Проверяет, что возвращается ошибка если wallet_id не найден
     * @return void
     */
    public function testMethodGetBalanceShouldReturnValidationExceptionWithModelNotFound()
    {
        $response = $this->json('GET', route('api.wallet.balance.get', ['wallet_id' => 123]));

        $response->assertJsonFragment([
            'errors' => [
                'wallet_id' => ["The selected wallet id is invalid."]
            ]
        ]);
    }

    /**
     * Проверяет, что возвращается баланс переданного wallet
     * @return void
     */
    public function testMethodGetBalanceShouldReturnBalanceForWallet()
    {
        $wallet = Wallet::factory()->create();

        $response = $this->json('GET', route('api.wallet.balance.get', ['wallet_id' => $wallet->id]));

        $response->assertSuccessful();
        $response->assertJsonFragment([
            'balance' => $wallet->balance
        ]);
    }

    /**
     * Проверяет, что возвращается ошибка в случае если не переданы обязательные параметры
     * @return void
     */
    public function testMethodChangeBalanceShouldReturnValidationExceptionWithRequiredParams()
    {
        $response = $this->json('POST', route('api.wallet.balance.change'));

        $response->assertJsonFragment([
            'errors' => [
                'wallet_id' => ["The wallet id field is required."],
                'transaction_type' => ["The transaction type field is required."],
                'currency_amount' => ["The currency amount field is required."],
                'currency_symbol' => ["The currency symbol field is required."],
                'transaction_reason' => ["The transaction reason field is required."],
            ]
        ]);
    }

    /**
     * Проверяет, что возвращается ошибка в случае если переданы невозможные значения полей wallet_id,transaction_type,currency,transaction_reason
     * @return void
     */
    public function testMethodChangeBalanceShouldReturnValidationExceptionWithMoreParams()
    {
        $response = $this->json('POST', route('api.wallet.balance.change', [
            'wallet_id' => 123,
            'transaction_type' => 'unknown_type',
            'currency_amount' => 0.00,
            'currency_symbol' => 'UAE',
            'transaction_reason' => 'my_unknown_reason',
        ]));

        $response->assertJsonFragment([
            'errors' => [
                'wallet_id' => ["The selected wallet id is invalid."],
                'transaction_type' => ["The selected transaction type is invalid."],
                'currency_amount' => ["The selected currency amount is invalid."],
                'currency_symbol' => ["The selected currency symbol is invalid."],
                'transaction_reason' => ["The selected transaction reason is invalid."],
            ]
        ]);
    }

    /**
     * Проверяет, что создается транзакция пополнения баланса кошелька БЕЗ КОНВЕРТАЦИИ
     * @return void
     */
    public function testMethodChangeUpBalanceShouldCreateDirectTransaction()
    {
        $this->seedExchangeRates();

        $wallet = WalletFactory::new([
            'balance' => 5000.0,
            'currency_id' => Currency::bySymbol(Currency::RUB_CURRENCY_SYMBOL),
        ])
            ->create();

        $response = $this->json('POST', route('api.wallet.balance.change', [
            'wallet_id' => $wallet->id,
            'transaction_type' => TransactionType::CREDIT_TRANSACTION_NAME,
            'currency_amount' => 10000.00,
            'currency_symbol' => Currency::RUB_CURRENCY_SYMBOL,
            'transaction_reason' => TransactionReason::STOCK_REASON_NAME,
        ]));

        $response->assertSuccessful();

        $response->assertJsonFragment([
            'result' => true
        ]);

        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $wallet->id,
            'transaction_type_id' => 2,
            'transaction_reason_id' => 1,
            'currency_id' => 2,
            'currency_amount' => 10000.00,
            'currency_exchange_rate' => null,
            'amount' => 10000.00,
        ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'balance' => 15000.00,
        ]);
    }

    /**
     * Проверяет, что создается транзакция списания с баланса кошелька БЕЗ КОНВЕРТАЦИИ
     * @return void
     */
    public function testMethodChangeDownBalanceShouldCreateDirectTransaction()
    {
        $this->seedExchangeRates();

        $wallet = WalletFactory::new([
            'balance' => 10000.0,
            'currency_id' => Currency::bySymbol(Currency::RUB_CURRENCY_SYMBOL),
        ])
            ->create();

        $response = $this->json('POST', route('api.wallet.balance.change', [
            'wallet_id' => $wallet->id,
            'transaction_type' => TransactionType::DEBIT_TRANSACTION_NAME,
            'currency_amount' => 5000.00,
            'currency_symbol' => Currency::RUB_CURRENCY_SYMBOL,
            'transaction_reason' => TransactionReason::REFUND_REASON_NAME,
        ]));

        $response->assertSuccessful();

        $response->assertJsonFragment([
            'result' => true
        ]);

        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $wallet->id,
            'transaction_type_id' => 1,
            'transaction_reason_id' => 2,
            'currency_id' => 2,
            'currency_amount' => 5000.00,
            'currency_exchange_rate' => null,
            'amount' => 5000.00,
        ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'balance' => 5000.00,
        ]);
    }

    /**
     * Проверяет, что создается транзакция пополнения баланса кошелька С КОНВЕРТАЦИЕЙ
     * @return void
     */
    public function testMethodChangeUpBalanceShouldCreateTransactionWithExchange()
    {
        $this->seedExchangeRates();

        $wallet = WalletFactory::new([
            'balance' => 10000.0,
            'currency_id' => Currency::bySymbol(Currency::RUB_CURRENCY_SYMBOL),
        ])
            ->create();

        $response = $this->json('POST', route('api.wallet.balance.change', [
            'wallet_id' => $wallet->id,
            'transaction_type' => TransactionType::CREDIT_TRANSACTION_NAME,
            'currency_amount' => 50.00,//$
            'currency_symbol' => Currency::USD_CURRENCY_SYMBOL,
            'transaction_reason' => TransactionReason::STOCK_REASON_NAME,
        ]));

        $response->assertSuccessful();

        $response->assertJsonFragment([
            'result' => true
        ]);

        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $wallet->id,
            'transaction_type_id' => 2,
            'transaction_reason_id' => 1,
            'currency_id' => 1,
            'currency_amount' => 50.00,
            'currency_exchange_rate' => 110.00,
            'amount' => 5500.00,
        ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'balance' => 15500.00,
        ]);
    }

    /**
     * Проверяет, что создается транзакция списания с баланса кошелька С КОНВЕРТАЦИЕЙ
     * @return void
     */
    public function testMethodChangeDownBalanceShouldCreateTransactionWithExchange()
    {
        $this->seedExchangeRates();

        $wallet = WalletFactory::new([
            'balance' => 10000.0,
            'currency_id' => Currency::bySymbol(Currency::RUB_CURRENCY_SYMBOL),
        ])
            ->create();

        $response = $this->json('POST', route('api.wallet.balance.change', [
            'wallet_id' => $wallet->id,
            'transaction_type' => TransactionType::DEBIT_TRANSACTION_NAME,
            'currency_amount' => 50.00,//$
            'currency_symbol' => Currency::USD_CURRENCY_SYMBOL,
            'transaction_reason' => TransactionReason::REFUND_REASON_NAME,
        ]));

        $response->assertSuccessful();

        $response->assertJsonFragment([
            'result' => true
        ]);

        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $wallet->id,
            'transaction_type_id' => 1,
            'transaction_reason_id' => 2,
            'currency_id' => 1,
            'currency_amount' => 50.00,
            'currency_exchange_rate' => 110.00,
            'amount' => 5500.00,
        ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'balance' => 4500.00,
        ]);
    }

    /**
     * Проверяет, что создается транзакция списания с баланса кошелька С КОНВЕРТАЦИЕЙ
     * не выполняется из-за недостатка средств
     * @return void
     */
    public function testMethodChangeDownBalanceShouldReturnFalse()
    {
        $this->seedExchangeRates();

        $wallet = WalletFactory::new([
            'balance' => 10000.0,
            'currency_id' => Currency::bySymbol(Currency::RUB_CURRENCY_SYMBOL),
        ])
            ->create();

        $response = $this->json('POST', route('api.wallet.balance.change', [
            'wallet_id' => $wallet->id,
            'transaction_type' => TransactionType::DEBIT_TRANSACTION_NAME,
            'currency_amount' => 100.00,//$
            'currency_symbol' => Currency::USD_CURRENCY_SYMBOL,
            'transaction_reason' => TransactionReason::REFUND_REASON_NAME,
        ]));

        $response->assertJsonFragment([
            'result' => false
        ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'balance' => 10000.0,
        ]);
    }

    private function seedExchangeRates()
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
            ->exchangeRate(110.00)
            ->create();
    }
}
