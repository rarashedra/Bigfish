<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Models\wallet;
use App\Models\User;
use App\Models\WalletPayment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\CustomerLogic;

class WalletBigfishController extends Controller
{
    public function payment(Request $request){
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'payment_method' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $customer = User::where('id',$request->user_id)->first();

        $wallet_amount = $request->amount;

        if (!isset($customer)) {
            return response()->json(['errors' => ['message' => 'Customer not found']], 403);
        }

        if (!isset($wallet_amount)) {
            return response()->json(['errors' => ['message' => 'Amount not found']], 403);
        }

        if (!$request->has('payment_method')) {
            return response()->json(['errors' => ['message' => 'Payment not found']], 403);
        }
        //for plugin payment gateway

            $wallet = new WalletPayment();
            $wallet->user_id = $request->user_id;
            $wallet->amount = $request->amount;
            $wallet->payment_status = 'pending';
            $wallet->payment_method = $request->payment_method;
            $wallet->callback = $request->callback;
            $wallet->save();

        $mode = $request->mode ? $request->mode : 'OTP';
        $config = Helpers::get_business_settings('bigfish');
        $perchantage = ($wallet->amount * $config['percentage'])/100;
        $url = $config['mode'] == 'test' ? 'https://system-test.paymentgateway.hu/api/payment/' : 'https://system.paymentgateway.hu/api/payment/';
        $store_name = $config['store_name'];
        $api_key = $config['api_key'];
       if($mode == 'OTP'){
        $dataJson = json_encode(array(
            'StoreName' => $config['store_name'],
            'ProviderName' => 'OTP',
            'Amount' => $wallet->amount + $perchantage,
            'Currency' => 'HUF',
            'ResponseUrl'=>route('bigfish-callback'),
            'NotificationUrl' => route('bigfish-notification'),
            'OrderId' => 'wallet-'.$wallet->id,
            'UserId' => $wallet->user_id,
            'OtpCardPocketId' => '08',
            'Language' => 'EN'
            ));
       }
       if($mode == 'KHBSZEP'){
        $extra_encode =  base64_encode('{"KhbCardPocketId":"3"}');
        $search = array("+","/","=");
        $replace =array("-","_",".");
        $extra = str_replace($search, $replace, $extra_encode);
        $dataJson = json_encode(array(
            'StoreName' => $config['store_name'],
            'ProviderName' => 'KHBSZEP',
            'Amount' => $wallet->amount + $perchantage,
            'Currency' => 'HUF',
            'ResponseUrl'=>route('bigfish-callback'),
            'NotificationUrl' => route('bigfish-notification'),
            'OrderId' => 'wallet-'.$wallet->id,
            'UserId' => $wallet->user_id,
            'Extra' => $extra,
            'Language' => 'EN'
            ));

       }
       if($mode == 'MKBSZEP'){

        $dataJson = json_encode(array(
            'StoreName' => $config['store_name'],
            'ProviderName' => 'MKBSZEP',
            'Amount' => $wallet->amount + $perchantage,
            'Currency' => 'HUF',
            'ResponseUrl'=>route('bigfish-wallet-callback'),
            'NotificationUrl' => route('bigfish-wallet-notification'),
            'OrderId' => 'wallet-'.$wallet->id,
            'UserId' => $wallet->user_id,
            'MkbSzepCafeteriaId'=>'3333',
            'GatewayPaymentPage'=>true,
            'Language' => 'EN'
            ));

       }

        $data = array(
            'method' => 'Init',
            'json' => $dataJson
        );
        $ch = curl_init();
        $domain = $config['domain'];
        $user_agent = "Init | $domain | PHP | 8.0";
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_USERPWD => $store_name . ':' . $api_key,
            CURLOPT_USERAGENT =>  $user_agent,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $result = json_decode($response,true);
        curl_close($ch);
        if(isset($result['TransactionId'])){
            $url = $config['mode'] == 'test' ? 'https://system-test.paymentgateway.hu/Start?TransactionId=' : 'https://system.paymentgateway.hu/Start?TransactionId=';
            $payment_url = $url.$result['TransactionId'];
            return redirect()->to($payment_url);
        }
        if ($wallet->callback != null) {
            return redirect($wallet->callback . '&status=fail');
        }else{
            return \redirect()->route('payment-fail',['wallet_id' => $wallet->id]);
        }
    }
    public function callback(Request $request){
      $config = Helpers::get_business_settings('bigfish');
      $url = $config['mode'] == 'test' ? 'https://system-test.paymentgateway.hu/api/payment/' : 'https://system.paymentgateway.hu/api/payment/';
      $store_name = $config['store_name'];
      $api_key = $config['api_key'];
      $dataJson = json_encode(array(
          'TransactionId' => $request->TransactionId,
          ));

      $data = array(
          'method' => 'Result',
          'json' => $dataJson
      );
      $domain = $config['domain'];
      $user_agent = "Result | $domain | PHP | 8.0";
      $ch = curl_init();
      $options = array(
          CURLOPT_URL => $url,
          CURLOPT_USERPWD => $store_name . ':' . $api_key,
          CURLOPT_USERAGENT => $user_agent,
          CURLOPT_POST => 1,
          CURLOPT_POSTFIELDS => http_build_query($data),
          CURLOPT_RETURNTRANSFER => true
      );

      curl_setopt_array($ch, $options);

      $response = curl_exec($ch);
      $result = json_decode($response,true);
      $wallet_id = substr($result['OrderId'],7);
      $wallet = WalletPayment::where('id',$wallet_id)->first();
      info($wallet);
        if(isset($result['ResultCode']) && $result['ResultCode'] == 'SUCCESSFUL'){
            $wallet = WalletPayment::where('id',$wallet_id)->first();
            $wallet->payment_status='confirmed';
            $wallet->transaction_ref = $result['TransactionId'];
            $wallet->payment_method='bigfish';
            $wallet->save();
            CustomerLogic::create_wallet_transaction($wallet->user_id, $wallet->amount, 'add_fund',$wallet->payment_method);

            if ($wallet->callback != null) {
                return redirect($wallet->callback . '&status=success');
            }

            return \redirect()->route('wallet-payment-success',['wallet_id' => $wallet->id]);
        }
        $wallet->payment_status='failed';
        $wallet->payment_method='bigfish';
        $wallet->save();
        if ($wallet->callback != null) {
            return redirect($wallet->callback . '&status=fail');
        }else{
            return \redirect()->route('payment-fail',['wallet_id' => $wallet->id]);
        }
    }


    public function notification(Request $request){
       $data = $request->all();
      $transactionId = $data['commonData']['transactionId'];

       $config = Helpers::get_business_settings('bigfish');
        $url = $config['mode'] == 'test' ? 'https://system-test.paymentgateway.hu/api/payment/' : 'https://system.paymentgateway.hu/api/payment/';
        $store_name = $config['store_name'];
        $api_key = $config['api_key'];

       $dataJson = json_encode(array(
            'TransactionId' => $transactionId
            ));
        $data = array(
            'method' => 'Result',
            'json' => $dataJson
        );
        $ch = curl_init();
        $domain = $config['domain'];
        $user_agent = "Result | $domain | PHP | 8.0";
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_USERPWD => $store_name . ':' . $api_key,
            CURLOPT_USERAGENT =>  $user_agent,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $result = json_decode($response,true);
        info('notifiction result');
        info($result);
        curl_close($ch);
    }
}
