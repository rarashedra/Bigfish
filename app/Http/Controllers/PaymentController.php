<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payment(Request $request)
    {
        if ($request->has('callback')) {
            Order::where(['id' => $request->order_id])->update(['callback' => $request['callback']]);
        }

        session()->put('customer_id', $request['customer_id']);
        session()->put('order_id', $request->order_id);

        $customer = User::find($request['customer_id']);

        $order = Order::where(['id' => $request->order_id, 'user_id' => $request['customer_id']])->first();

        if (isset($customer) && isset($order)) {
            $data = [
                'name' => $customer['f_name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
            ];
            session()->put('data', $data);
            return view('payment-view');
        }

        return response()->json(['errors' => ['code' => 'order-payment', 'message' => 'Data not found']], 403);
    }
    
    public function paypal(Request $request)
    {
        if ($request->has('callback')) {
            Order::where(['id' => $request->order_id])->update(['callback' => $request['callback']]);
        }

        session()->put('customer_id', $request['customer_id']);
        session()->put('order_id', $request->order_id);

        $customer = User::find($request['customer_id']);

        $order = Order::where(['id' => $request->order_id, 'user_id' => $request['customer_id']])->first();

        if (isset($customer) && isset($order)) {
            $data = [
                'name' => $customer['f_name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
            ];
            session()->put('data', $data);
            return view('paypal-payment-view');
        }

        return response()->json(['errors' => ['code' => 'order-payment', 'message' => 'Data not found']], 403);
    }
    
    
        public function stripe(Request $request)
    {
        if ($request->has('callback')) {
            Order::where(['id' => $request->order_id])->update(['callback' => $request['callback']]);
        }

        session()->put('customer_id', $request['customer_id']);
        session()->put('order_id', $request->order_id);

        $customer = User::find($request['customer_id']);

        $order = Order::where(['id' => $request->order_id, 'user_id' => $request['customer_id']])->first();

        if (isset($customer) && isset($order)) {
            $data = [
                'name' => $customer['f_name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
            ];
            session()->put('data', $data);
            return view('stripe-payment');
        }

        return response()->json(['errors' => ['code' => 'order-payment', 'message' => 'Data not found']], 403);
    }
    
     public function mbh(Request $request)
    {
        if ($request->has('callback')) {
            Order::where(['id' => $request->order_id])->update(['callback' => $request['callback']]);
        }

        session()->put('customer_id', $request['customer_id']);
        session()->put('order_id', $request->order_id);

        $customer = User::find($request['customer_id']);

        $order = Order::where(['id' => $request->order_id, 'user_id' => $request['customer_id']])->first();

        if (isset($customer) && isset($order)) {
            $data = [
                'name' => $customer['f_name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
            ];
            session()->put('data', $data);
          //  return view('paypal-payment-view');
          
           $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://system-test.paymentgateway.hu/api/payment/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'method=Init&json=    {      
    "StoreName":"marwa",
    "ProviderName":"MKBSZEP",
    "ResponseUrl":"https://panel.marwa.hu/response-bigfish",
    "NotificationUrl" : "https://panel.marwa.hu/response-notification",
    "Amount":"'.$order->order_amount.'",
    "Currency":"HUF",
    "OrderId":"'.$order->id.'",
    "UserId":"'.$order->user_id.'",
    "Extra" : "jkufgskug",
    "MkbSzepCafeteriaId" : "3333",
    "GatewayPaymentPage" : true
    }');

$headers = array();
$headers[] = 'Authorization: Basic bWFyd2E6NGJlM2UtNTc0N2MtNjNiNmQtM2IxOGMtYjQzOGQ=';
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
$headers[] = 'user-agent: Init | panel.marwa.hu | PHP | 8.0.28';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);
$response = json_decode($result, true);


    //  return $response;
      $transactionid = $response['TransactionId'];
      session()->put('bigfish_transactionid',$transactionid);
      
      $url = "https://system-test.paymentgateway.hu/Start?TransactionId=".$transactionid;
      return redirect()->to($url);
        }

        return response()->json(['errors' => ['code' => 'order-payment', 'message' => 'Data not found']], 403);
    }
    
    public function otp(Request $request)
    {
        log('Hello hi');
        if ($request->has('callback')) {
            Order::where(['id' => $request->order_id])->update(['callback' => $request['callback']]);
        }

        session()->put('customer_id', $request['customer_id']);
        session()->put('order_id', $request->order_id);

        $customer = User::find($request['customer_id']);

        $order = Order::where(['id' => $request->order_id, 'user_id' => $request['customer_id']])->first();
      //  $amount = "500";
        if (isset($customer) && isset($order)) {
            $data = [
                'name' => $customer['f_name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
            ];
            session()->put('data', $data);
          //  return view('paypal-payment-view');
          $amount = (int) $order->order_amount;
           $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://system-test.paymentgateway.hu/api/payment/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'method=Init&json=    {      
    "StoreName":"marwa",
    "ProviderName":"OTP",
    "ResponseUrl":"https://panel.marwa.hu/response-bigfishotp",
    "NotificationUrl" : "https://panel.marwa.hu/response-notification",
    "Amount":"'.$amount.'",
    "Currency":"HUF",
    "OrderId":"'.$order->id.'",
    "UserId":"'.$order->user_id.'",
    "OtpCardPocketId" : "01"
    }');

$headers = array();
$headers[] = 'Authorization: Basic bWFyd2E6NGJlM2UtNTc0N2MtNjNiNmQtM2IxOGMtYjQzOGQ=';
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
$headers[] = 'user-agent: Init | panel.marwa.hu | PHP | 8.0.28';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);
$response = json_decode($result, true);


      $transactionid = $response['TransactionId'];
      session()->put('bigfish_transactionid',$transactionid);
      
      $url = "https://system-test.paymentgateway.hu/Start?TransactionId=".$transactionid;
      return redirect()->to($url);
        }

        return response()->json(['errors' => ['code' => 'order-payment', 'message' => 'Data not found']], 403);
    }
    
    public function kh(Request $request)
    {
        if ($request->has('callback')) {
            Order::where(['id' => $request->order_id])->update(['callback' => $request['callback']]);
        }

        session()->put('customer_id', $request['customer_id']);
        session()->put('order_id', $request->order_id);

        $customer = User::find($request['customer_id']);

        $order = Order::where(['id' => $request->order_id, 'user_id' => $request['customer_id']])->first();

        if (isset($customer) && isset($order)) {
            $data = [
                'name' => $customer['f_name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
            ];
            session()->put('data', $data);
          //  return view('paypal-payment-view');
          
           $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://system-test.paymentgateway.hu/api/payment/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'method=Init&json=    {      
    "StoreName":"marwa",
    "ProviderName":"KHBSZEP",
    "ResponseUrl":"https://panel.marwa.hu/response-bigfishkh",
    "NotificationUrl" : "https://panel.marwa.hu/response-notification",
    "Amount":"'.$order->order_amount.'",
    "Currency":"HUF",
    "OrderId":"'.$order->id.'",
    "UserId":"'.$order->user_id.'",
    "Extra" : "eyJLaGJDYXJkUG9ja2V0SWQiOiIzIn0"
    }');

$headers = array();
$headers[] = 'Authorization: Basic bWFyd2E6NGJlM2UtNTc0N2MtNjNiNmQtM2IxOGMtYjQzOGQ=';
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
$headers[] = 'user-agent: Init | panel.marwa.hu | PHP | 8.0.28';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);
$response = json_decode($result, true);


      
      $transactionid = $response['TransactionId'];
      session()->put('bigfish_transactionid',$transactionid);
      
      $url = "https://system-test.paymentgateway.hu/Start?TransactionId=".$transactionid;
      return redirect()->to($url);
        }

        return response()->json(['errors' => ['code' => 'order-payment', 'message' => 'Data not found']], 403);
    }

    public function success()
    {
        $order = Order::where(['id' => session('order_id'), 'user_id'=>session('customer_id')])->first();
        if (isset($order) && $order->callback != null) {
            return redirect($order->callback . '&status=success');
        }
        return response()->json(['message' => 'Payment succeeded'], 200);
    }

    public function fail()
    {
        $order = Order::where(['id' => session('order_id'), 'user_id'=>session('customer_id')])->first();
        if (isset($order) && $order->callback != null) {
            return redirect($order->callback . '&status=fail');
        }
        return response()->json(['message' => 'Payment failed'], 403);
    }
    
}
