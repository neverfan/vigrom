<?php

namespace App\Vigrom\Services\Transaction\Handlers;

use App\Vigrom\Services\Transaction\TransactionDto;

interface TransactionHandlerInterface
{
    public function __construct(TransactionDto $transactionDto);

    /**
     * Разрешение на транзакцию
     * @return bool
     */
    public function permit(): bool;

    /**
     * Выполнить операцию
     * @return void
     */
    public function execute(): void;
}
