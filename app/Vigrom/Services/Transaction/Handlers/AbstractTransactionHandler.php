<?php

namespace App\Vigrom\Services\Transaction\Handlers;

use App\Models\Transaction;
use App\Vigrom\Services\Transaction\TransactionDto;

abstract class AbstractTransactionHandler implements TransactionHandlerInterface
{
    protected TransactionDto $transactionDto;

    public function __construct(TransactionDto $transactionDto)
    {
        $this->transactionDto = $transactionDto;
    }

    /**
     * @return void
     */
    protected function saveTransaction(): void
    {
        $transaction = new Transaction([
            'currency_amount' => $this->transactionDto->currencyAmount,
            'currency_exchange_rate' => $this->transactionDto->exchangeRate,
            'amount' => $this->transactionDto->amount,
        ]);

        $transaction->wallet()->associate($this->transactionDto->wallet);
        $transaction->currency()->associate($this->transactionDto->currency);
        $transaction->type()->associate($this->transactionDto->transactionType);
        $transaction->reason()->associate($this->transactionDto->transactionReason);
        $transaction->save();
    }

    /**
     * Обновить баланс кошелька
     * @param float $newBalance
     * @return void
     */
    protected function updateWalletBalance(float $newBalance): void
    {
        $this->transactionDto->wallet->balance = $newBalance;
        $this->transactionDto->wallet->save();
    }

    abstract public function permit(): bool;

    abstract public function execute(): void;
}
