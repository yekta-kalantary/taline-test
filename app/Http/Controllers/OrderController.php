<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Exceptions\InsufficientFundsException;
use App\Factories\CancelOrderHandlerFactory;
use App\Factories\OrderHandlerFactory;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function list(Request $request)
    {
        return Order::query()->where('user_id', Auth::id())->paginate(
            perPage: $request->query('perPage', 15) ,
            page: $request->query('page', 1));
    }

    public function show(Order $order)
    {
        if ($order->user_id != Auth::id()) {
            abort(403, 'you don\'t have permission');
        }

        return response()->json([
            'data' => $order,
        ]);
    }

    public function place(Request $request)
    {
        $request->validate([
            'type' => ['required', Rule::enum(OrderTypeEnum::class)],
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

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id != Auth::user()->id) {
            abort(403, "you don't own this order");
        }
        if ($order->status == OrderStatusEnum::CANCELLED or $order->status == OrderStatusEnum::PARTIALLY_CANCELLED) {
            abort(403, 'this order is cancelled');
        }
        if ($order->status == OrderStatusEnum::FILLED) {
            abort(403, 'this order is filled');
        }

        $handler = CancelOrderHandlerFactory::make($order)->cansel();

    }
}
