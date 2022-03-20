<?php

namespace App\Vigrom\Services\Transaction;

use Exception;
use App\Models\TransactionType;
use Illuminate\Support\Facades\DB;
use App\Vigrom\Services\Exchange\ExchangeService;
use App\Vigrom\Services\Transaction\Handlers\DebitTransactionHandler;
use App\Vigrom\Services\Transaction\Handlers\CreditTransactionHandler;
use App\Vigrom\Services\Transaction\Handlers\TransactionHandlerInterface;

class TransactionManager
{
    private const TRANSACTION_HANDLERS = [
        TransactionType::CREDIT_TRANSACTION_NAME => CreditTransactionHandler::class,
        TransactionType::DEBIT_TRANSACTION_NAME => DebitTransactionHandler::class,
    ];

    private TransactionDto $transactionDto;

    public function __construct(TransactionDto $transactionDto)
    {
        $this->transactionDto = $transactionDto;
    }

    /**
     * @return bool
     */
    public function handle(): bool
    {
        $this->prepareTransaction();

        $transactionHandler = $this->makeTransactionHandler();

        //проверка допустимости транзакции
        if (!$transactionHandler->permit()) {
            return false;
        }

        return $this->commitTransaction($transactionHandler);
    }

    /**
     * Выполнить операции подготовки транзакции независимо от ее типа
     * @return void
     */
    private function prepareTransaction(): void
    {
        $this->transactionDto->amount = $this->transactionDto->currencyAmount;

        if ($this->transactionDto->wallet->currency->id !== $this->transactionDto->currency->id) {
            $this->exchangeCurrency();
        }
    }

    /**
     * @return void
     */
    private function exchangeCurrency(): void
    {
        $fromCurrency = $this->transactionDto->currency;
        $toCurrency = $this->transactionDto->wallet->currency;
        $currencyAmount = $this->transactionDto->currencyAmount;

        $exchangeService = app(ExchangeService::class);

        $this->transactionDto->amount = $exchangeService->exchange($fromCurrency, $toCurrency, $currencyAmount);
        $this->transactionDto->exchangeRate = $exchangeService->getExchangeRate($fromCurrency, $toCurrency);
    }

    private function makeTransactionHandler(): TransactionHandlerInterface
    {
        $handlerClass = self::TRANSACTION_HANDLERS[$this->transactionDto->transactionType->name];

        return new $handlerClass($this->transactionDto);
    }

    /**
     * Выполнить транзакцию
     * @param TransactionHandlerInterface $transactionHandler
     * @return bool
     */
    private function commitTransaction(TransactionHandlerInterface $transactionHandler): bool
    {
        try {
            DB::beginTransaction();

            $transactionHandler->execute();

        } catch (Exception $exception) {
            //report $exception
            DB::rollBack();
            return false;
        }

        DB::commit();

        return true;
    }
}
