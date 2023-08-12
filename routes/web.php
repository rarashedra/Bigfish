<?php

use App\CentralLogics\Helpers;
use App\Library\ClientSecret;
use App\Models\NotificationMessage;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('home');
Route::get('lang/{locale}', 'HomeController@lang')->name('lang');
Route::get('terms-and-conditions', 'HomeController@terms_and_conditions')->name('terms-and-conditions');
Route::get('about-us', 'HomeController@about_us')->name('about-us');
Route::get('contact-us', 'HomeController@contact_us')->name('contact-us');
Route::post('send-message', 'HomeController@send_message')->name('send-message');
Route::get('privacy-policy', 'HomeController@privacy_policy')->name('privacy-policy');
Route::get('cancelation', 'HomeController@cancelation')->name('cancelation');
Route::get('refund', 'HomeController@refund')->name('refund');
Route::get('shipping-policy', 'HomeController@shipping_policy')->name('shipping-policy');
Route::post('newsletter/subscribe', 'NewsletterController@newsLetterSubscribe')->name('newsletter.subscribe');

Route::get('authentication-failed', function () {
    $errors = [];
    array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthenticated.']);
    return response()->json([
        'errors' => $errors,
    ], 401);
})->name('authentication-failed');

Route::group(['prefix' => 'payment-mobile'], function () {
    Route::get('/', 'PaymentController@payment')->name('payment-mobile');
    Route::get('/paypal', 'PaymentController@paypal')->name('paypal');
    Route::get('/stripe', 'PaymentController@stripe')->name('stripe');
    Route::get('/mbh', 'PaymentController@mbh')->name('mbh');
    Route::get('/otp', 'PaymentController@otp')->name('otp');
    Route::get('/kh', 'PaymentController@kh')->name('kh');
    Route::get('set-payment-method/{name}', 'PaymentController@set_payment_method')->name('set-payment-method');
});

// SSLCOMMERZ Start
/*Route::get('/example1', 'SslCommerzPaymentController@exampleEasyCheckout');
Route::get('/example2', 'SslCommerzPaymentController@exampleHostedCheckout');*/
Route::post('pay-ssl', 'SslCommerzPaymentController@index')->name('pay-ssl');
Route::post('/success', 'SslCommerzPaymentController@success');
Route::post('/fail', 'SslCommerzPaymentController@fail');
Route::post('/cancel', 'SslCommerzPaymentController@cancel');
Route::post('/ipn', 'SslCommerzPaymentController@ipn');
//SSLCOMMERZ END

/*paypal*/
/*Route::get('/paypal', function (){return view('paypal-test');})->name('paypal');*/
Route::post('pay-paypal', 'PaypalPaymentController@payWithpaypal')->name('pay-paypal');
Route::get('paypal-status', 'PaypalPaymentController@getPaymentStatus')->name('paypal-status');
/*paypal*/

/*Route::get('stripe', function (){
return view('stripe-test');
});*/
//wallet stripe
Route::get('add-fund', 'PaymentController@add_fund');
Route::get('wallet-pay-stripe', 'WalletStripePaymentController@payment_process_3d')->name('wallet-pay-stripe');
Route::get('wallet-pay-stripe/success/{wallet_id}/{transaction_ref}', 'WalletStripePaymentController@success')->name('wallet-pay-stripe.success');
Route::get('wallet-pay-stripe/fail/{wallet_id}', 'WalletStripePaymentController@fail')->name('wallet-pay-stripe.fail');
//wallet bigfish

Route::get('bigfish-wallet', 'WalletBigfishController@payment')->name('bigfish-wallet');
Route::any('bigfish-wallet-callback', 'WalletBigfishController@callback')->name('bigfish-wallet-callback');
Route::any('bigfish-wallet-notification', 'WalletBigfishController@notification')->name('bigfish-wallet-notification');


Route::get('pay-stripe', 'StripePaymentController@payment_process_3d')->name('pay-stripe');
Route::get('pay-stripe/success/{order_id}/{transaction_ref}', 'StripePaymentController@success')->name('pay-stripe.success');
Route::get('pay-stripe/fail', 'StripePaymentController@fail')->name('pay-stripe.fail');

// Get Route For Show Payment Form
Route::get('paywithrazorpay', 'RazorPayController@payWithRazorpay')->name('paywithrazorpay');
Route::post('payment-razor/{order_id}', 'RazorPayController@payment')->name('payment-razor');



Route::get('paywithbigfish', 'BigfishController@payWithBigfish')->name('paywithbigfish');
Route::get('response-bigfish', 'BigfishController@bigfishResponse')->name('bigfishResponse');
Route::get('response-bigfishkh', 'BigfishController@bigfishkhResponse')->name('bigfishResponsekh');
Route::get('response-bigfishotp', 'BigfishController@bigfishotpResponse')->name('bigfishResponseotp');
Route::post('response-notification', 'BigfishController@NotificationResponse')->name('NotificationResponse');
/*Route::fallback(function () {
return redirect('/admin/auth/login');
});*/

Route::get('payment-success', 'PaymentController@success')->name('payment-success');
Route::get('payment-fail', 'PaymentController@fail')->name('payment-fail');

//wallet

Route::get('wallet-payment-success', 'PaymentController@wallet_success')->name('wallet-payment-success');
Route::get('wallet-payment-fail', 'PaymentController@wallet_fail')->name('wallet-payment-fail');

//senang pay
Route::match(['get', 'post'], '/return-senang-pay', 'SenangPayController@return_senang_pay')->name('return-senang-pay');

// paymob
Route::post('/paymob-credit', 'PaymobController@credit')->name('paymob-credit');
Route::get('/paymob-callback', 'PaymobController@callback')->name('paymob-callback');

//paystack
Route::post('/paystack-pay', 'PaystackController@redirectToGateway')->name('paystack-pay');
Route::get('/paystack-callback', 'PaystackController@handleGatewayCallback')->name('paystack-callback');
Route::get('/paystack', function () {
    return view('paystack');
});


// The route that the button calls to initialize payment
Route::post('/flutterwave-pay', 'FlutterwaveController@initialize')->name('flutterwave_pay');
// The callback url after a payment
Route::get('/rave/callback/{order_id}', 'FlutterwaveController@callback')->name('flutterwave_callback');


// The callback url after a payment
Route::get('mercadopago/home', 'MercadoPagoController@index')->name('mercadopago.index');
Route::post('mercadopago/make-payment', 'MercadoPagoController@make_payment')->name('mercadopago.make_payment');
Route::get('mercadopago/get-user', 'MercadoPagoController@get_test_user')->name('mercadopago.get-user');

//paytabs
Route::any('/paytabs-payment', 'PaytabsController@payment')->name('paytabs-payment');
Route::any('/paytabs-response', 'PaytabsController@callback_response')->name('paytabs-response');

//bkash
Route::group(['prefix' => 'bkash'], function () {
    // Payment Routes for bKash
    Route::post('get-token', 'BkashPaymentController@getToken')->name('bkash-get-token');
    // Route::post('create-payment', 'BkashPaymentController@createPayment')->name('bkash-create-payment');
    // Route::post('execute-payment', 'BkashPaymentController@executePayment')->name('bkash-execute-payment');
    // Route::get('query-payment', 'BkashPaymentController@queryPayment')->name('bkash-query-payment');
    Route::get('make-payment', 'BkashPaymentController@make_tokenize_payment')->name('bkash-make-payment');
    Route::any('callback', 'BkashPaymentController@callback')->name('bkash-callback');

    // Refund Routes for bKash
    // Route::get('refund', 'BkashRefundController@index')->name('bkash-refund');
    // Route::post('refund', 'BkashRefundController@refund')->name('bkash-refund');
});

// The callback url after a payment PAYTM
Route::get('paytm-payment', 'PaytmController@payment')->name('paytm-payment');
Route::any('paytm-response', 'PaytmController@callback')->name('paytm-response');

// The callback url after a payment LIQPAY
Route::get('liqpay-payment', 'LiqPayController@payment')->name('liqpay-payment');
Route::any('liqpay-callback/{order_id}', 'LiqPayController@callback')->name('liqpay-callback');
//bigfish
Route::get('payment-bigfish', 'BigfishController@payment')->name('payment-bigfish');
Route::any('bigfish-callback', 'BigfishController@callback')->name('bigfish-callback');
Route::any('bigfish-notification', 'BigfishController@notification')->name('bigfish-notification');

Route::get('/test', function () {
    // $apple_login=\App\Models\BusinessSetting::where(['key'=>'apple_login'])->first();
    // if($apple_login){
    //     $apple_login = json_decode($apple_login->value)[0];
    // }
    // $keyContent = file_get_contents('storage/app/public/apple-login/'.$apple_login->service_file);
    // $keyContent = file_get_contents('AppleServiceId.p8');
    // dd($keyContent);
//     $clientId = 'com.sixamtech.6amMart';
// $teamId   = '7WSYLQ8Y87';
// $keyId    = 'U7KA7F82UM';
// $certPath = url('/'). '/app/Library/AuthKey_U7KA7F82UM.p8';

// $clientSecret = new ClientSecret($clientId, $teamId, $keyId, $certPath);

// dd($clientSecret->generate());
    dd('Hello tester');
});

Route::get('module-test', function () {
});

//Restaurant Registration
Route::group(['prefix' => 'store', 'as' => 'restaurant.'], function () {
    Route::get('apply', 'VendorController@create')->name('create');
    Route::post('apply', 'VendorController@store')->name('store');
    Route::get('get-all-modules', 'VendorController@get_all_modules')->name('get-all-modules');
});

//Deliveryman Registration
Route::group(['prefix' => 'deliveryman', 'as' => 'deliveryman.'], function () {
    Route::get('apply', 'DeliveryManController@create')->name('create');
    Route::post('apply', 'DeliveryManController@store')->name('store');
});
