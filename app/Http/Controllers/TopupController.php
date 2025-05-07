<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;

class TopupController extends Controller
{
    public function process(Request $request)
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $plan = $request->input('plan');

        $plans = [
            'small' => ['amount' => 15000, 'credit' => 200000],
            'normal' => ['amount' => 60000, 'credit' => 1000000],
            'big' => ['amount' => 250000, 'credit' => 5000000],
        ];

        if (!isset($plans[$plan])) {
            return response()->json(['error' => 'Invalid plan selected.'], 400);
        }

        $orderId = uniqid('TOPUP-');

        $params = array(
            'transaction_details' => array(
                'order_id' => $orderId,
                'gross_amount' => $plans[$plan]['amount'],
            ),
            'customer_details' => array(
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
            ),
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);
        $redirectUrl = "https://app.sandbox.midtrans.com/snap/v2/vtweb/" . $snapToken;

        return response()->json(['snap_token' => $snapToken, 'credit_add' => $plans[$plan]['credit'], 'redirect_url' => $redirectUrl]);
    }

    public function callback(Request $request) {}
}
