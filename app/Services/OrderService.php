<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Adapter\ECPayOrderAdapter;

use App\Jobs\CerateOrderJob;

class OrderService
{
    public function getThirdPartyPaymentAdapter($paymentVendor)
    {
        $thirdPartyAdapter = null;

        switch ($paymentVendor) {
            case ECPayOrderAdapter::PAYMENT_VENDOR:
                $thirdPartyAdapter = new ECPayOrderAdapter();
                break;
            default:
                return response()->json(['message' => 'Invalid payment vendor'], 400);
                break;
        }
        return $thirdPartyAdapter;
    }

    public function createOrder($request)
    {
        $params['plan_id'] = $request->input('plan_id');
        $params['uuid'] = Str::uuid();
        $params['user_id'] = Auth::user()->id;

        $thirdPartyPaymentAdapter = $this->getThirdPartyPaymentAdapter($request->input('payment_vendor'));

        $cerateOrderJob = new CerateOrderJob($params, $thirdPartyPaymentAdapter);
        $cerateOrderJob->handle();

        return $params['uuid'];
    }
}
