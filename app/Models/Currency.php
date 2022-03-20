<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static bySymbol(string $symbol);
 */
class Currency extends Model
{
    use HasFactory;

    public const USD_CURRENCY_SYMBOL = 'USD';
    public const RUB_CURRENCY_SYMBOL = 'RUB';

    /**
     * @param Builder$query
     * @param string $symbol
     * @return Builder
     */
    public function scopeBySymbol($query, string $symbol)
    {
        return $query->where('symbol', $symbol)->first();
    }
}
