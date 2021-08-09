<?php

namespace App\Adapter;

use App\Intrtface\IOrederAdapterInterface;

use ECPay_AllInOne as ECPay;
use ECPay_PaymentMethod as ECPay_PaymentMethod;

class ECPayOrderAdapter implements IOrederAdapterInterface
{
    const PAYMENT_VENDOR = 'ecpay';

    public function createOrder($orderData)
    {
        $ecpay = new ECPay();
        $ecpay->ServiceURL  = env('ECPAY_SERVE_URL');
        $ecpay->HashKey     = env('ECPAY_HASH_KEY');
        $ecpay->HashIV      = env('ECPAY_HASH_IV');
        $ecpay->MerchantID  = env('ECPAY_MERCHANT_ID');
        $ecpay->EncryptType = '1';

        $MerchantTradeNo = str_pad(env('ECPAY_TRAND_NO_PREFIX'), 0, 10, '0');

        $ecpay->Send['ReturnURL'] = env('ECPAY_RETURN_URL');
        $ecpay->Send['MerchantTradeNo'] = $MerchantTradeNo;
        $ecpay->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');
        $ecpay->Send['TotalAmount'] = 2000;
        $ecpay->Send['ChoosePayment'] = ECPay_PaymentMethod::Credit;
        $ecpay->Send['IgnorePayment'] = ECPay_PaymentMethod::GooglePay;

        array_push($ecpay->Send['Items'], array(
            'Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"2000",
            'Currency' => "元", 'Quantity' => (int) "1", 'URL' => "dedwed"
        ));

        $ecpay->CheckOut();
    }
}
