<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class BigfishController
{
    
    
    public function bigfishResponse(Request $request){
        
        $t =  session()->get('bigfish_transactionid'); 
      //  return $t;
   $ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://system-test.paymentgateway.hu/api/payment/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'method=Result&json={"TransactionId":"'.$t.'"}');

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
$headers[] = 'user-agent: Result | panel.marwa.hu | PHP | 8.0.28';
$headers[] = 'Authorization: Basic bWFyd2E6NGJlM2UtNTc0N2MtNjNiNmQtM2IxOGMtYjQzOGQ=';

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

//return $result;

       $response = json_decode($result, true); 
     //  return $response;
   //   return $response['OrderId'];
       
         $order = Order::with(['details'])->where(['id' => $response['OrderId']])->first();

         if ($response['ResultCode'] == "SUCCESSFUL") {
             $transactionID = $response['TransactionId'];

              try {
                 $order->transaction_reference = $transactionID;
                 $order->payment_method = 'mbh';
                 $order->payment_status = 'paid';
                 $order->order_status = 'confirmed';
                 $order->confirmed = now();
                 $order->save();
                 Helpers::send_order_notification($order);
              } catch (\Exception $e) {
             }

             if ($order->callback != null) {
                 return redirect($order->callback . '&status=success');
             }else{
                 return \redirect()->route('payment-success');
             }
         }
//         // elseif ($status ==  'cancelled'){
//         //     //Put desired action/code after transaction has been cancelled here
//         // }
        else{
             $order->order_status = 'failed';
             $order->payment_method = 'mbh';
             $order->failed = now();
             $order->save();
             if ($order->callback != null) {
                 return redirect($order->callback . '&status=fail');
             }else{
                 return \redirect()->route('payment-fail');
            }
//         }
        
     }
     }
     
     
      public function bigfishotpResponse(Request $request){
        
        $t =  session()->get('bigfish_transactionid');
      //  return $t;
   $ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://system-test.paymentgateway.hu/api/payment/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'method=Result&json={"TransactionId":"'.$t.'"}');

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
$headers[] = 'user-agent: Result | panel.marwa.hu | PHP | 8.0.28';
$headers[] = 'Authorization: Basic bWFyd2E6NGJlM2UtNTc0N2MtNjNiNmQtM2IxOGMtYjQzOGQ=';

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

//return $result;

       $response = json_decode($result, true); 
   //   return $response['OrderId'];
         $orderid = session()->get('order_id');
       
         $order = Order::with(['details'])->where(['id' => $response['OrderId']])->first();

      if ($response['ResultCode'] == "SUCCESSFUL") {
             $transactionID = $response['TransactionId'];

             try {
                 $order->transaction_reference = $transactionID;
                 $order->payment_method = 'otp';
                 $order->payment_status = 'paid';
                 $order->order_status = 'confirmed';
                 $order->confirmed = now();
                 $order->save();
                 Helpers::send_order_notification($order);
             } catch (\Exception $e) {
             }

             if ($order->callback != null) {
                 return redirect($order->callback . '&status=success');
             }else{
                 return \redirect()->route('payment-success');
             }
         }
//         // elseif ($status ==  'cancelled'){
//         //     //Put desired action/code after transaction has been cancelled here
//         // }
        else{
            $order->order_status = 'failed';
             $order->payment_method = 'otp';
             $order->failed = now();
             $order->save();
              if ($order->callback != null) {
                 return redirect($order->callback . '&status=fail');
             }else{
                 return \redirect()->route('payment-fail');
            }
             
            
            
            
//         }
        
     }
     }
     
     
     
     public function bigfishkhResponse(Request $request){
        
        $t =  session()->get('bigfish_transactionid');
      //  return $t;
   $ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://system-test.paymentgateway.hu/api/payment/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'method=Result&json={"TransactionId":"'.$t.'"}');

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
$headers[] = 'user-agent: Result | panel.marwa.hu | PHP | 8.0.28';
$headers[] = 'Authorization: Basic bWFyd2E6NGJlM2UtNTc0N2MtNjNiNmQtM2IxOGMtYjQzOGQ=';

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

//return $result;

      $response = json_decode($result, true); 
   //   return $response['OrderId'];
       
         $order = Order::with(['details'])->where(['id' => $response['OrderId']])->first();

      if ($response['ResultCode'] == "SUCCESSFUL") {
             $transactionID = $response['TransactionId'];

             try {
                 $order->transaction_reference = $transactionID;
                 $order->payment_method = 'kh';
                 $order->payment_status = 'paid';
                 $order->order_status = 'confirmed';
                 $order->confirmed = now();
                 $order->save();
                 Helpers::send_order_notification($order);
             } catch (\Exception $e) {
             }

             if ($order->callback != null) {
                 return redirect($order->callback . '&status=success');
             }else{
                 return \redirect()->route('payment-success');
             }
         }
//         // elseif ($status ==  'cancelled'){
//         //     //Put desired action/code after transaction has been cancelled here
//         // }
        else{
             $order->order_status = 'failed';
             $order->payment_method = 'kh';
             $order->failed = now();
             $order->save();
             if ($order->callback != null) {
                 return redirect($order->callback . '&status=fail');
             }else{
                 return \redirect()->route('payment-fail');
            }
//         }
        
     }
     }
     
     
     public function NotificationResponse(Request $request){
         
         
         $json  = $request->all();
         
         // Takes raw data from the request
$jsons = file_get_contents('php://input');
// Converts it into a PHP object
$data = json_decode($jsons);
         
            //       $order = Order::with(['details'])->where(['id' => '200737'])->first();
            //   //   $order->order_note = json_decode($json);
            //       $order->coupon_code = $request->transactionId;
            //       $order->order_note = 'jkdhfgidhg';
            //       $order->save();
                  
            //       return 'null';
         
          $t =  session()->get('bigfish_transactionid');
          
          $tnew = $data->commonData->transactionId;
          
          
   $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://system-test.paymentgateway.hu/api/payment/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'method=Result&json={"TransactionId":"'.$tnew.'"}');

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
$headers[] = 'user-agent: Result | panel.marwa.hu | PHP | 8.0.28';
$headers[] = 'Authorization: Basic bWFyd2E6NGJlM2UtNTc0N2MtNjNiNmQtM2IxOGMtYjQzOGQ=';

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

//return $result;

      $response = json_decode($result, true); 

         $order = Order::with(['details'])->where(['id' => '200737'])->first();

      if ($response['ResultCode'] == "SUCCESSFUL") {
          
           return 'SUCCESSFUL';
         }

        else if($response['ResultCode'] == "TIMEOUT"){
                      return 'TIMEOUT';
          

        }else if($response['ResultCode'] == "PENDING"){
                   
                 return 'PENDING';
        }else if($response['ResultCode'] == "OPEN"){
                   
                 return 'OPEN';
        }else if($response['ResultCode'] == "UNSUCCESSFUL"){
                   
                 return 'UNSUCCESSFUL';
        }else{
            return 'ERROR';
        }
        
     }
         
 

     
     
     
    
}


