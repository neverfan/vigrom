<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class WalletChangeBalanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'wallet_id' => 'required|integer|exists:wallets,id',
            'transaction_type' => 'required|string|exists:transaction_types,name',
            'currency_symbol' => 'required|string|exists:currencies,symbol',
            'currency_amount' => 'required|regex:/^\d{1,13}(\.\d{1,4})?$/|not_in:0,0.00',
            'transaction_reason' => 'required|string|exists:transaction_reasons,name',
        ];
    }

}
