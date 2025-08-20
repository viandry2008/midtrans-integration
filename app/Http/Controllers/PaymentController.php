<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MidtransService;

class PaymentController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    // Generate Snap Token
    public function createTransaction(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'amount' => 'required|numeric',
            'first_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);

        $transaction = $this->midtrans->createTransaction(
            $request->order_id,
            $request->amount,
            [
                'first_name' => $request->first_name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]
        );

        return response()->json([
            'success' => true,
            'snap_token' => $transaction->token,
            'redirect_url' => $transaction->redirect_url,
        ]);
    }

    // Callback dari Midtrans snap
    // public function callback(Request $request)
    // {
    //     $data = $request->all();

    //     // Validasi signature key midtrans
    //     $signatureKey = hash(
    //         'sha512',
    //         $data['order_id'] . $data['status_code'] . $data['gross_amount'] . config('midtrans.server_key')
    //     );

    //     if ($signatureKey !== $data['signature_key']) {
    //         return response()->json(['message' => 'Invalid signature'], 403);
    //     }

    //     // Simpan status pembayaran ke database (sesuai kebutuhan)
    //     // Contoh:
    //     // Order::where('order_id', $data['order_id'])->update(['payment_status' => $data['transaction_status']]);

    //     return response()->json(['message' => 'Callback processed']);
    // }

    public function createQris(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'amount'   => 'required|numeric|min:1000',
            'name'     => 'required|string',
            'email'    => 'required|email',
            'phone'    => 'required|string',
        ]);

        $payment = $this->midtrans->createQrisPayment(
            $request->order_id,
            $request->amount,
            [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]
        );

        return response()->json($payment);
    }

    // Endpoint untuk menerima callback dari Midtrans
    public function callback(Request $request)
    {
        // simpan log dulu
        // \Log::info('Midtrans Callback: ', $request->all());

        $transactionStatus = $request->transaction_status;
        $orderId = $request->order_id;

        if ($transactionStatus == 'settlement') {
            // update status transaksi di DB jadi PAID
        } elseif ($transactionStatus == 'pending') {
            // update status transaksi jadi PENDING
        } elseif ($transactionStatus == 'expire') {
            // update status transaksi jadi EXPIRED
        }

        return response()->json(['message' => 'Callback received']);
    }
}
