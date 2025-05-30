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

        return response()->json([
            'data' => [
                'balance' => $walletService->getBalance(),
                'asset' => $asset,
            ],
        ]);
    }

    public function transactions(Request $request)
    {
        $data = $request->validate([
            'asset' => ['required', Rule::enum(AssetEnum::class)],
        ]);

        $asset = $data['asset'];

        $walletService = WalletService::getWallet($asset);

        return response()->json($walletService->history(
            $request->input('page', 1),
            $request->input('per_page', 20),
        ));
    }
}
