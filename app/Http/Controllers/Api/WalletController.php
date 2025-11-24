<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\AddWalletBalanceRequest;
use App\Http\Resources\WalletTransactionResource;
use App\Services\WalletService;
use App\ValueObjects\Money;
use Illuminate\Routing\Controller;

final class WalletController extends Controller
{
    public function __construct(private readonly WalletService $walletService) {}

    public function balance()
    {
        $balance = $this->walletService->getWalletBalance(auth()->id());

        return response()->json([
            'balance' => $balance,
            'currency' => 'INR',
        ]);
    }

    public function addBalance(AddWalletBalanceRequest $request): WalletTransactionResource
    {
        $amount = new Money($request->amount);

        $transaction = $this->walletService->addBalance(
            auth()->id(),
            $amount,
            "Wallet top-up via {$request->payment_method}"
        );

        return new WalletTransactionResource($transaction);
    }

    public function transactionHistory()
    {
        $transactions = $this->walletService->getTransactionHistory(
            auth()->id(),
            request()->all()
        );

        return WalletTransactionResource::collection($transactions);
    }
}
