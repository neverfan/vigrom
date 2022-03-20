<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    use HasFactory;

    public function fromCurrency()
    {
        return $this->belongsTo(Currency::class, 'from_currency_id', 'id');
    }

    public function toCurrency()
    {
        return $this->belongsTo(Currency::class, 'to_currency_id', 'id');
    }

    /**
     * @param Builder $query
     * @param Currency $currency
     * @return mixed
     */
    public function scopeFromCurrency($query, Currency $currency)
    {
        return $query->where('from_currency_id', $currency->id);
    }

    /**
     * @param Builder $query
     * @param Currency $currency
     * @return mixed
     */
    public function scopeToCurrency($query, Currency $currency)
    {
        return $query->where('to_currency_id', $currency->id);
    }
}
