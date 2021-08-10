<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Adapter\ECPayOrderAdapter;

use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(OrderService $orderservice)
    {
        $this->orderservice = $orderservice;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required',
            'payment_vendor' => 'required|string|in:ecpay'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $uuid = $this->orderservice->createOrder($request);

        return response()->json(['order_uuid' => $uuid], 200);
    }

    /**
     * Sync ECPay order payment status
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function syncECPayOrderPaymentStatus(Request $reques)
    {
        $thirdPartyPaymentAdapter = $this->orderservice->getThirdPartyPaymentAdapter(ECPayOrderAdapter::PAYMENT_VENDOR);
        $thirdPartyPaymentAdapter->syncOrderPaymentStatus($reques);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
}
