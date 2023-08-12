<?php

namespace App\Http\Controllers;

use App\CentralLogics\CustomerLogic;
use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Stripe\Charge;
use Stripe\Stripe;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\User;
use App\Models\WalletPayment;
use PHPUnit\Exception;


class WalletStripePaymentController extends Controller
{
    public function payment_process_3d(Request $request)
    {
        $tran = Str::random(6) . '-' . rand(1, 1000);
        $wallet_id = $request->wallet_id;
        $wallet = WalletPayment::where(['id' => $wallet_id])->first();
        $config = Helpers::get_business_settings('stripe');
        $perchantage = ($wallet->amount * $config['percentage'])/100;
        Stripe::setApiKey($config['api_key']);
        header('Content-Type: application/json');

        $YOUR_DOMAIN = url('/');

        $products = [];
            array_push($products, [
                'name' => 'wallet',
                'image' => 'def.png'
            ]);


        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => Helpers::currency_code(),
                    'unit_amount' => ($wallet->amount + $perchantage) * 100,
                    'product_data' => [
                        'name' => BusinessSetting::where(['key' => 'business_name'])->first()->value,
                        'images' => [asset('storage/app/public/business') . '/' . BusinessSetting::where(['key' => 'logo'])->first()->value],
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => (String)route('wallet-pay-stripe.success',['wallet_id'=>$wallet->id,'transaction_ref'=>$tran]),
            'cancel_url' => url()->previous(),
        ]);

        return response()->json(['id' => $checkout_session->id]);
    }

    public function success($wallet_id,$transaction_ref)
    {
        $wallet = WalletPayment::where('id',$wallet_id)->first();
        $wallet->payment_status='confirmed';
        $wallet->transaction_ref = $transaction_ref;
        $wallet->payment_method='stripe';
        $wallet->save();
        CustomerLogic::create_wallet_transaction($wallet->user_id, $wallet->amount, 'add_fund',$wallet->payment_method);

        if ($wallet->callback != null) {
            return redirect($wallet->callback . '&status=success');
        }

        return \redirect()->route('wallet-payment-success');
    }

    public function fail($wallet_id)
    {
        $wallet = WalletPayment::find($wallet_id);
        $wallet->payment_status='failed';
        $wallet->payment_method='stripe';
        $wallet->save();
        if ($wallet->callback != null) {
            return redirect($wallet->callback . '&status=fail');
        }
        return \redirect()->route('wallet-payment-fail');
    }
}
