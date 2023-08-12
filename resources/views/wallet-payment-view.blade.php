@php($currency=\App\Models\BusinessSetting::where(['key'=>'currency'])->first()->value)

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>
        @yield('title')
    </title>
    <!-- SEO Meta Tags-->
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <!-- Viewport-->
    <meta name="_token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon and Touch Icons-->
    <link rel="shortcut icon" href="favicon.ico">
    <!-- Font -->
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/custom.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/style.css">

    <script
        src="{{asset('public/assets/admin')}}/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js"></script>
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/toastr.css">
    {{--stripe--}}
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
    {{--stripe--}}
</head>
<!-- Body-->
{{--loader--}}
<div id="loading" class="initial-hidden">
    <div class="loading-inner">
        <img width="200" src="{{asset('public/assets/front-end/img/loader.gif')}}">
    </div>
</div>
{{--loader--}}
<body class="toolbar-enabled">
<!-- Page Content-->
<div class="container pb-5 mb-2 mb-md-4">
    <div class="row">
        <div class="col-md-12 mb-5 pt-5">
            <center class="">
                <h1>Payment method</h1>
            </center>
        </div>
        <section class="col-lg-12">
            <div class="checkout_details mt-3">
                <div class="row">
       @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
       @if($config['status'])
           <div class="col-md-6 mb-4 cursor-pointer">
               <div class="card">
                   <div class="card-body py-0 h-70px">
                       @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
                       <button class="btn btn-block click-if-alone" type="button" id="checkout-button">
                           <img width="100" src="{{asset('public/assets/admin/img/stripe.png')}}"/>
                       </button>

                       <script type="text/javascript">
                           // Create an instance of the Stripe object with your publishable API key
                           var stripe = Stripe('{{$config['published_key']}}');
                           var checkoutButton = document.getElementById("checkout-button");
                           checkoutButton.addEventListener("click", function () {
                               fetch("{{route('wallet-pay-stripe',['wallet_id'=>$wallet->id])}}", {
                                   method: "GET",
                               }).then(function (response) {
                                   console.log(response)
                                   return response.text();
                               }).then(function (session) {
                                   console.log(JSON.parse(session).id)
                                   return stripe.redirectToCheckout({sessionId: JSON.parse(session).id});
                               }).then(function (result) {
                                   if (result.error) {
                                       alert(result.error.message);
                                   }
                               }).catch(function (error) {
                                   console.error("Error:", error);
                               });
                           });
                       </script>
                   </div>
               </div>
           </div>
       @endif
    </div>
</div>
</section>
</div>
</div>

<!-- JS Front -->
<script src="{{asset('public/assets/admin')}}/js/custom.js"></script>
<script src="{{asset('public/assets/admin')}}/js/vendor.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/sweet_alert.js"></script>
<script src="{{asset('public/assets/admin')}}/js/toastr.js"></script>
<script src="{{asset('public/assets/admin')}}/js/bootstrap.min.js"></script>

{!! Toastr::message() !!}




<script>
    setTimeout(function () {
        $('.stripe-button-el').hide();
        $('.razorpay-payment-button').hide();
    }, 10)
</script>

{{-- @if(env('APP_MODE')=='live')
    <script id="myScript"
            src="https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js"></script>
@else
    <script id="myScript"
            src="https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js"></script>
@endif


<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    function BkashPayment() {
        $('#loading').show();
        // get token
        $.ajax({
            url: "{{ route('bkash-get-token') }}",
            type: 'POST',
            contentType: 'application/json',
            success: function (data) {
                $('#loading').hide();
                $('pay-with-bkash-button').trigger('click');
                if (data.hasOwnProperty('msg')) {
                    showErrorMessage(data) // unknown error
                }
            },
            error: function (err) {
                $('#loading').hide();
                showErrorMessage(err);
            }
        });
    }

    let paymentID = '';
    bKash.init({
        paymentMode: 'checkout',
        paymentRequest: {},
        createRequest: function (request) {
            setTimeout(function () {
                createPayment(request);
            }, 2000)
        },
        executeRequestOnAuthorization: function (request) {
            $.ajax({
                url: '{{ route('bkash-execute-payment') }}',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    "paymentID": paymentID
                }),
                success: function (data) {
                    if (data) {
                        if (data.paymentID != null) {
                            BkashSuccess(data);
                        } else {
                            showErrorMessage(data);
                            bKash.execute().onError();
                        }
                    } else {
                        $.get('{{ route('bkash-query-payment') }}', {
                            payment_info: {
                                payment_id: paymentID
                            }
                        }, function (data) {
                            if (data.transactionStatus === 'Completed') {
                                BkashSuccess(data);
                            } else {
                                createPayment(request);
                            }
                        });
                    }
                },
                error: function (err) {
                    bKash.execute().onError();
                }
            });
        },
        onClose: function () {
            // for error handle after close bKash Popup
        }
    });

    function createPayment(request) {
        // because of createRequest function finds amount from this request
        request['amount'] = "{{round($order->order_amount,2)}}"; // max two decimal points allowed
        $.ajax({
            url: '{{ route('bkash-create-payment') }}',
            data: JSON.stringify(request),
            type: 'POST',
            contentType: 'application/json',
            success: function (data) {
                $('#loading').hide();
                if (data && data.paymentID != null) {
                    paymentID = data.paymentID;
                    bKash.create().onSuccess(data);
                } else {
                    bKash.create().onError();
                }
            },
            error: function (err) {
                $('#loading').hide();
                showErrorMessage(err.responseJSON);
                bKash.create().onError();
            }
        });
    }

    function BkashSuccess(data) {
        $.post('{{ route('bkash-success') }}', {
            payment_info: data
        }, function (res) {
            location.href = '{{ route('payment-success')}}';
        });
    }

    function showErrorMessage(response) {
        let message = 'Unknown Error';
        if (response.hasOwnProperty('errorMessage')) {
            let errorCode = parseInt(response.errorCode);
            let bkashErrorCode = [2001, 2002, 2003, 2004, 2005, 2006, 2007, 2008, 2009, 2010, 2011, 2012, 2013, 2014,
                2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023, 2024, 2025, 2026, 2027, 2028, 2029, 2030,
                2031, 2032, 2033, 2034, 2035, 2036, 2037, 2038, 2039, 2040, 2041, 2042, 2043, 2044, 2045, 2046,
                2047, 2048, 2049, 2050, 2051, 2052, 2053, 2054, 2055, 2056, 2057, 2058, 2059, 2060, 2061, 2062,
                2063, 2064, 2065, 2066, 2067, 2068, 2069, 503,
            ];
            if (bkashErrorCode.includes(errorCode)) {
                message = response.errorMessage
            }
        }
        Swal.fire("Payment Failed!", message, "error");
    }
</script> --}}
<script>
    function click_if_alone() {
        let total = $('.checkout_details .click-if-alone').length;
        if (Number.parseInt(total) == 1) {
            $('.click-if-alone')[0].click()
            $('.checkout_details').html('<div class="text-center"><h1>{{translate('messages.Redirecting_to_the_payment_page')}}......</h1></div>');
        }
    }
    @if(!Session::has('toastr::messages'))
        click_if_alone();
    @endif
</script>
</body>
</html>
