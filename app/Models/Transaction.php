<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency_amount',
        'currency_exchange_rate',
        'amount',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id', 'id');
    }

    public function reason()
    {
        return $this->belongsTo(TransactionReason::class, 'transaction_reason_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }
}
