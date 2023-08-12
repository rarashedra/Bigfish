<html>
<head>
    <title>Merchant Check Out Page</title>
</head>
<body>
<center><h1>Please do not refresh this page...</h1></center>

             <form class="needs-validation" method="POST" id="payment-form" name="f1"
                                          action="{{route('pay-paypal',request()->getQueryString())}}">
                                        {{ csrf_field() }}
                                        <button class="btn btn-block click-if-alone" type="submit">
                                            <img width="100"
                                                 src="{{asset('public/assets/admin/img/paypal.png')}}"/>
                                        </button>
    @csrf
    <script type="text/javascript">
        document.f1.submit();
    </script>
</form>
</body>
</html>
