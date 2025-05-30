<?php

namespace App\Http\Controllers;

use App\Enums\OrderTypeEnum;
use App\Exceptions\InsufficientFundsException;
use App\Factories\OrderHandlerFactory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function place(Request $request)
    {
        $request->validate([
            'type' => ["required" , Rule::enum(OrderTypeEnum::class) ],
            'gold' => 'required|numeric|min:0.001|decimal:0,3',
            'price' => 'required|integer|min:1',
        ]);

        $handler = OrderHandlerFactory::make(
            $request->input('type'),
            $request->input('gold'),
            $request->input('price')
        );

        try {
            $handler->hasSufficientBalance();

            $order = $handler->place();

            return response()->json(['data' => $order], 201);

        } catch (InsufficientFundsException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
