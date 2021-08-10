<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Entities\Plan;
use App\Entities\Order;

class CerateOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderData;
    public $thirdPartyPaymentAdapter;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($orderData, $thirdPartyPaymentAdapter)
    {
        $this->orderData = $orderData;
        $this->thirdPartyPaymentAdapter = $thirdPartyPaymentAdapter;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $plan = Plan::find($this->orderData['plan_id']);

        $this->orderData['plan_name'] = $plan->name;
        $this->orderData['amount'] = $plan->amount;

        $order = Order::create($this->orderData);

        $this->thirdPartyPaymentAdapter->createOrder($order);
    }
}
