<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static byName(string $symbol);
 */
class TransactionReason extends Model
{
    use HasFactory;

    //Пополнение средств
    public const STOCK_REASON_NAME = 'stock';

    //Возврат средств
    public const REFUND_REASON_NAME = 'refund';

    public const REASONS = [
        self::STOCK_REASON_NAME => 1,
        self::REFUND_REASON_NAME  => 2,
    ];

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
