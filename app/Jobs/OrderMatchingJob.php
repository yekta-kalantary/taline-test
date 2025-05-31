<?php

namespace App\Jobs;

use App\Factories\OrderMatchingHandlerFactory;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class OrderMatchingJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        OrderMatchingHandlerFactory::make($this->order)->execute();
    }
}
