<html>
<head>
    <title>Merchant Check Out Page</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
 @php($order=\App\Models\Order::find(session('order_id')))
 <div class="checkout_details mt-3">
                      <div class="col-md-6 mb-4 cursor-pointer">
                            <div class="card">
                                <div class="card-body py-0 h-70px">
                                    @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
                                  <button class="btn btn-block click-if-alone" type="button" id="checkout-button" name="f1">
                                        <img width="100" src="{{asset('public/assets/admin/img/stripe.png')}}"/>
                                    </button>

                                    <script type="text/javascript">
                                        // Create an instance of the Stripe object with your publishable API key
                                        var stripe = Stripe('{{$config['published_key']}}');
                                        var checkoutButton = document.getElementById("checkout-button");
                                        checkoutButton.addEventListener("click", function () {
                                            fetch("{{route('pay-stripe',['order_id'=>$order->id])}}", {
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
                        </div>
    <!-- JS Front -->
<script src="{{asset('public/assets/admin')}}/js/custom.js"></script>
<script src="{{asset('public/assets/admin')}}/js/vendor.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/sweet_alert.js"></script>
<script src="{{asset('public/assets/admin')}}/js/toastr.js"></script>
<script src="{{asset('public/assets/admin')}}/js/bootstrap.min.js"></script>

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

 <script type="text/javascript">
        document.f1.submit();
    </script>
    
    
</body>
</html>
