<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static byName(string $symbol);
 */
class TransactionType extends Model
{
    use HasFactory;

    //Транзакция списания
    public const DEBIT_TRANSACTION_NAME = 'debit';

    //Транзакция пополнения
    public const CREDIT_TRANSACTION_NAME = 'credit';

    public const TYPES = [
        self::DEBIT_TRANSACTION_NAME => 1,
        self::CREDIT_TRANSACTION_NAME  => 2,
    ];

    public function isDebitTransaction(): bool
    {
        return $this->name === self::DEBIT_TRANSACTION_NAME;
    }

    public function isCreditTransaction(): bool
    {
        return $this->name === self::CREDIT_TRANSACTION_NAME;
    }

    /**
     * @param Builder$query
     * @param string $name
     * @return Builder
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name)->first();
    }
}
