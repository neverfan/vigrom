<?php

namespace App\Vigrom\Services\Transaction;

use App\Models\Wallet;
use App\Models\Currency;
use App\Models\TransactionReason;
use App\Models\TransactionType;

class TransactionDto
{
    public Wallet $wallet;
    public TransactionType $transactionType;
    public TransactionReason $transactionReason;
    public Currency $currency;
    public null|float $currencyAmount = null;
    public null|float $amount = null;
    public null|float $exchangeRate = null;

    /**
     * @param mixed $args
     * @return static
     */
    public static function transform(mixed $args): self
    {
        $dto = new self();

        foreach ($args as $property => $value) {
            $dto->{$property} = $value;
        }

        return $dto;
    }
}
