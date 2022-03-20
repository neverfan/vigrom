<?php

namespace App\Http\Controllers\Api;

use App\Models\Wallet;
use App\Models\Currency;
use App\Models\TransactionType;
use Illuminate\Http\JsonResponse;
use App\Models\TransactionReason;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Http\Requests\Api\WalletGetBalanceRequest;
use App\Vigrom\Services\Transaction\TransactionDto;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\Api\WalletChangeBalanceRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Vigrom\Services\Transaction\TransactionManager;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WalletController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Получить текущий баланс
     * @param WalletGetBalanceRequest $request
     * @return JsonResponse
     */
    public function getBalance(WalletGetBalanceRequest $request)
    {
        $wallet = Wallet::find($request->get('wallet_id'));

        return response()->json([
            'balance' => $wallet->balance
        ]);
    }

    /**
     * Изменить текущий баланс
     * @param WalletChangeBalanceRequest $request
     * @return JsonResponse
     */
    public function changeBalance(WalletChangeBalanceRequest $request)
    {
        $dto = TransactionDto::transform([
            'wallet' => Wallet::find($request->get('wallet_id')),
            'transactionType'  => TransactionType::byName($request->get('transaction_type')),
            'transactionReason' => TransactionReason::byName($request->get('transaction_reason')),
            'currency' => Currency::bySymbol($request->get('currency_symbol')),
            'currencyAmount' => $request->get('currency_amount'),
        ]);

        return response()->json([
            'result' => (new TransactionManager($dto))->handle()
        ]);
    }
}
