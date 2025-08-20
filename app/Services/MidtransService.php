<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\CoreApi;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createTransaction($orderId, $amount, $customer)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $customer['first_name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
            ],
        ];

        return Snap::createTransaction($params);
    }

    public function createQrisPayment($orderId, $amount, $customer)
    {
        $params = [
            "payment_type" => "qris",
            "transaction_details" => [
                "order_id" => $orderId,
                "gross_amount" => $amount,
            ],
            "customer_details" => [
                "first_name" => $customer['name'],
                "email" => $customer['email'],
                "phone" => $customer['phone'],
            ]
        ];

        return CoreApi::charge($params);
    }
}
