<?php

namespace App\Adapter;

use Illuminate\Support\Facades\Redis;

use App\Intrtface\IOrederAdapterInterface;

use ECPay_AllInOne as ECPay;
use ECPay_PaymentMethod as ECPay_PaymentMethod;

use App\Entities\Order;
use App\Entities\Transaction;

class ECPayOrderAdapter implements IOrederAdapterInterface
{
    //payment vendor
    const PAYMENT_VENDOR = 'ecpay';

    public function __construct()
    {
        $this->ecpay = new ECPay();
    }

    public function createOrder($orderData)
    {
        $this->ecpay->ServiceURL  = env('ECPAY_SERVE_URL');
        $this->ecpay->HashKey     = env('ECPAY_HASH_KEY');
        $this->ecpay->HashIV      = env('ECPAY_HASH_IV');
        $this->ecpay->MerchantID  = env('ECPAY_MERCHANT_ID');
        $this->ecpay->EncryptType = '1';

        $merchantTradeNo = env('ECPAY_TRAND_NO_PREFIX') . str_pad($orderData->id, 7, '0', STR_PAD_LEFT);

        $this->ecpay->Send['ReturnURL'] = route('ecpay-sync-order-payment-status');
        $this->ecpay->Send['MerchantTradeNo'] = $merchantTradeNo;
        $this->ecpay->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');
        $this->ecpay->Send['TotalAmount'] = 2000;
        $this->ecpay->Send['TradeDesc'] = "It is  test order";
        $this->ecpay->Send['ChoosePayment'] = ECPay_PaymentMethod::Credit;
        $this->ecpay->Send['IgnorePayment'] = ECPay_PaymentMethod::GooglePay;

        array_push(
            $this->ecpay->Send['Items'],
            [
                'Name' => $orderData->plan_name,
                'Price' => $orderData->amount,
                'Currency' => "元",
                'Quantity' => (int) "1",
            ]
        );

        $ecpayCheckOutHtml = $this->ecpay->CheckOutString();

        Redis::set('ecpay_checkout_html_' . $orderData->uuid, $ecpayCheckOutHtml, 3600);
    }

    public function syncOrderPaymentStatus($request)
    {
        $params = $request->only([
            'MerchantTradeNo',
            'RtnCode',
            'RtnMsg',
            'TradeNo',
            'TradeAmt',
            'PaymentDate',
            'PaymentTypeChargeFee'
        ]);

        $orderId = $this->getOrderIdFromMerchantTradeNo($params['MerchantTradeNo']);
        $order = Order::find($orderId);

        if ($params['RtnCode'] == 1) {
            $order->update([
                'payment_status' => self::PAID,
                'payment_vendor' => self::PAYMENT_VENDOR,
                'payment_datetime' => $params['PaymentDate'],
            ]);
        } else {
            $order->update([
                'payment_status' => self::FAILD,
                'payment_vendor' => self::PAYMENT_VENDOR,
            ]);
        }

        Transaction::create([
            'order_id' => $order->id,
            'ord_number' => $params['MerchantTradeNo'],
            'transaction_number' => $params['TradeNo'],
            'transaction_amount' => $params['TradeAmt'],
            'payment_date' => $params['PaymentDate'],
            'payment_processing_fee' => $params['PaymentTypeChargeFee'],
            'message' => $params['RtnMsg'],
        ]);
    }

    private function getOrderIdFromMerchantTradeNo($merchantTradeNo)
    {
        $merchantTradeNo = str_replace(env('ECPAY_TRAND_NO_PREFIX'), '', $merchantTradeNo);
        $orderId =  ltrim($merchantTradeNo, 0);
        return $orderId;
    }
}
