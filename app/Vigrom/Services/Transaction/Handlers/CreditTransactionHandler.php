<?php

namespace App\Vigrom\Services\Transaction\Handlers;

/**
 * Стратегия обработки транзакция пополнения
 */
class CreditTransactionHandler extends AbstractTransactionHandler
{
    /**
     * Разрешение на транзакцию
     * @return bool
     */
    public function permit(): bool
    {
        //по умолчанию разрешаем любые операции пополнения
        return true;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $currentBalance = $this->transactionDto->wallet->balance;
        $amount = $this->transactionDto->amount;

        $newBalance = $currentBalance + $amount;

        $this->saveTransaction();
        $this->updateWalletBalance($newBalance);
    }
}
