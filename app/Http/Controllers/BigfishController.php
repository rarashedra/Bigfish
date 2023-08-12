<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Models\Order;
use Illuminate\Http\Request;

class BigfishController extends Controller
{
    public function payment(Request $request){
        if ($request->has('callback')) {
            Order::where(['id' => $request->order_id])->update(['callback' => $request['callback']]);
        }

        session()->put('customer_id', $request['customer_id']);
        session()->put('order_id', $request->order_id);

        $mode = $request->mode ? $request->mode : 'OTP';
        $order = Order::where('id',$request->order_id)->first();
        $config = Helpers::get_business_settings('bigfish');
        $url = $config['mode'] == 'test' ? 'https://system-test.paymentgateway.hu/api/payment/' : 'https://system.paymentgateway.hu/api/payment/';
        $store_name = $config['store_name'];
        $api_key = $config['api_key'];
       if($mode == 'OTP'){
        $dataJson = json_encode(array(
            'StoreName' => $config['store_name'],
            'ProviderName' => 'OTP',
            'Amount' => $order->order_amount,
            'Currency' => 'HUF',
            'ResponseUrl'=>route('bigfish-callback'),
            'NotificationUrl' => route('bigfish-notification'),
            'OrderId' => 'ORDER-'.$order->id,
            'UserId' => 'USER-'.$order->customer?->id,
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
            'Amount' => $order->order_amount,
            'Currency' => 'HUF',
            'ResponseUrl'=>route('bigfish-callback'),
            'NotificationUrl' => route('bigfish-notification'),
            'OrderId' => 'ORDER-'.$order->id,
            'UserId' => 'USER-'.$order->customer?->id,
            'Extra' => $extra,
            'Language' => 'EN'
            ));

       }
       if($mode == 'MKBSZEP'){

        $dataJson = json_encode(array(
            'StoreName' => $config['store_name'],
            'ProviderName' => 'MKBSZEP',
            'Amount' => $order->order_amount,
            'Currency' => 'HUF',
            'ResponseUrl'=>route('bigfish-callback'),
           'NotificationUrl' => route('bigfish-notification'),
            'OrderId' => 'ORDER-'.$order->id,
            'UserId' => 'USER-'.$order->customer?->id,
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
        $order->order_status = 'failed';
        $order->failed = now();
        $order->save();
        if ($order->callback != null) {
            return redirect($order->callback . '&status=fail');
        }else{
            return \redirect()->route('payment-fail');
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
      $order_id = substr($result['OrderId'],6);
      $order = Order::where('id',$order_id)->first();
      info($order);
        if(isset($result['ResultCode']) && $result['ResultCode'] == 'SUCCESSFUL'){
              $order->transaction_reference =$result['TransactionId'];
              $order->payment_method = 'bigfish';
              $order->payment_status = 'paid';
              $order->order_status = 'confirmed';
              $order->confirmed = now();
              $order->save();
              Helpers::send_order_notification($order);
              if ($order->callback != null) {
                  return redirect($order->callback . '&status=success');
              }else{
                  return \redirect()->route('payment-success');
              }
        }

        $order->order_status = 'failed';
        $order->failed = now();
        $order->save();
        if ($order->callback != null) {
            return redirect($order->callback . '&status=fail');
        }else{
            return \redirect()->route('payment-fail');
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
