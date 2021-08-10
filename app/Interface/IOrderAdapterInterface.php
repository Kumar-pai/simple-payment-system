<?php

namespace App\Intrtface;

interface IOrederAdapterInterface
{
    //payment status
    const PAID = 'Paid';
    const FAILD = 'Faild';

    public function createOrder($orderData);
    public function syncOrderPaymentStatus($request);
}
