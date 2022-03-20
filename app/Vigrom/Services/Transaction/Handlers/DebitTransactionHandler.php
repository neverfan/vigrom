<?php

namespace App\Vigrom\Services\Transaction\Handlers;

/**
 * Стратегия обработки транзакция списания
 */
class DebitTransactionHandler extends AbstractTransactionHandler
{
    /**
     * Разрешение на транзакцию
     * @return bool
     */
    public function permit(): bool
    {
        $balance = $this->transactionDto->wallet->balance;
        $currencyAmount = $this->transactionDto->amount;

        //проверка остатка счета
        if ($balance < $currencyAmount) {
            return false;
        }

        return true;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $currentBalance = $this->transactionDto->wallet->balance;
        $amount = $this->transactionDto->amount;

        $newBalance = $currentBalance - $amount;

        $this->saveTransaction();
        $this->updateWalletBalance($newBalance);
    }
}
