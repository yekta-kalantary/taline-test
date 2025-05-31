<?php

namespace App\Http\Controllers;

use App\Enums\AssetEnum;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WalletController extends Controller
{
    public function balance(Request $request)
    {
        $data = $request->validate([
            'asset' => ['required', Rule::enum(AssetEnum::class)],
        ]);

        $asset = $data['asset'];

        $walletService = WalletService::getWallet($asset);
$data = [
    'balance' => $walletService->getBalance(),
    'asset' => $asset,
];
if($asset == 'rial') {
    $data['balance_int_toman'] = $data['balance'] / 10;
}
        return response()->json([
            'data' => $data
        ]);
    }

    public function transactions(Request $request)
    {
        $data = $request->validate([
            'asset' => ['required', Rule::enum(AssetEnum::class)],
        ]);

        $asset = $data['asset'];

        $walletService = WalletService::getWallet($asset);
        $history = $walletService->history(
            $request->input('page', 1),
            $request->input('per_page', 20),
        );
        if($asset == 'rial') {
            $history->getCollection()->map(function ($item) {
                $this->amount = intval($item->amount);
                $item->amount_in_toman = $item->amount / 10;
            });
        }
        return response()->json($history);
    }
}
