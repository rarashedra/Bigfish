@extends('layouts.admin.app')

@section('title',translate('messages.Order List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        @php($parcel_order = Request::is('admin/parcel/orders*'))
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-xl-10 col-md-9 col-sm-8 mb-3 mb-sm-0 {{$parcel_order ? 'mb-2':''}}">
                    <h1 class="page-header-title text-capitalize m-0">
                        <span class="page-header-icon">
                            <img src="{{asset('public/assets/admin/img/order.png')}}" class="w--26" alt="">
                        </span>
                        <span>
                            @if ($parcel_order) {{translate('messages.parcel')}} {{translate('messages.orders')}}
                            @elseif(Request::is('admin/refund/*') ) {{translate('messages.Refund')}}  {{translate(str_replace('_',' ',$status))}}
                            @else {{translate(str_replace('_',' ',$status))}} {{translate('messages.orders')}}
                            @endif
                            <span class="badge badge-soft-dark ml-2">{{$total}}</span>
                        </span>
                    </h1>
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->

            <!-- End Header -->

            <!-- Table -->
            <div class="col-lg-4">
                <div class="table-responsive datatable-custom">
                    <table id="datatable"
                           class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table fz--14px"
                           data-hs-datatables-options='{
                         "columnDefs": [{
                            "targets": [0],
                            "orderable": false
                          }],
                         "order": [],
                         "info": {
                           "totalQty": "#datatableWithPaginationInfoTotalQty"
                         },
                         "search": "#datatableSearch",
                         "entries": "#datatableEntries",
                         "isResponsive": false,
                         "isShowPaging": false,
                         "paging": false
                       }'>
                        <thead class="thead-light">
                        <tr>
                            <th class="table-column-pl-0 border-0">{{translate('messages.order_id')}}</th>
                            <th class="border-0">{{translate('messages.order_date')}}</th>
                            <th class="text-center border-0">{{translate('messages.order')}} {{translate('messages.status')}}</th>
                        </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($orders as $key=>$order)

                            <tr class="status-{{$order['order_status']}} class-all">
                                <td class="table-column-pl-0">
                                    <a href="{{route($parcel_order?'admin.parcel.order.details':'admin.order.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                </td>
                                <td>
                                    <div>
                                        <div>
                                            {{date('d M Y',strtotime($order['created_at']))}}
                                        </div>
                                        <div class="d-block text-uppercase">
                                            {{date(config('timeformat'),strtotime($order['created_at']))}}
                                        </div>
                                    </div>
                                </td>
                                <td class="text-capitalize text-center">
                                    @if($order['order_status']=='pending')
                                        <span class="badge badge-soft-info">
                                          {{translate('messages.pending')}}
                                        </span>
                                    @elseif($order['order_status']=='confirmed')
                                        <span class="badge badge-soft-info">
                                          {{translate('messages.confirmed')}}
                                        </span>
                                    @elseif($order['order_status']=='processing')
                                        <span class="badge badge-soft-warning">
                                          {{translate('messages.processing')}}
                                        </span>
                                    @elseif($order['order_status']=='picked_up')
                                        <span class="badge badge-soft-warning">
                                          {{translate('messages.out_for_delivery')}}
                                        </span>
                                    @elseif($order['order_status']=='delivered')
                                        <span class="badge badge-soft-success">
                                          {{translate('messages.delivered')}}
                                        </span>
                                    @elseif($order['order_status']=='failed')
                                        <span class="badge badge-soft-danger">
                                          {{translate('messages.payment')}}  {{translate('messages.failed')}}
                                        </span>
                                    @elseif($order['order_status']=='handover')
                                        <span class="badge badge-soft-danger">
                                          {{translate('messages.handover')}}
                                        </span>
                                    @elseif($order['order_status']=='canceled')
                                        <span class="badge badge-soft-danger">
                                          {{translate('messages.canceled')}}
                                        </span>
                                    @elseif($order['order_status']=='accepted')
                                        <span class="badge badge-soft-danger">
                                          {{translate('messages.accepted')}}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger">
                                          {{str_replace('_',' ',$order['order_status'])}}
                                        </span>
                                    @endif
                                    @if($order['order_type']=='take_away')
                                        <div class="text-info mt-1">
                                            {{translate('messages.take_away')}}
                                        </div>
                                    @else
                                        <div class="text-title mt-1">
                                          {{translate('messages.home Delivery')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="table-responsive datatable-custom">
                    <table id="datatable"
                           class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table fz--14px"
                           data-hs-datatables-options='{
                         "columnDefs": [{
                            "targets": [0],
                            "orderable": false
                          }],
                         "order": [],
                         "info": {
                           "totalQty": "#datatableWithPaginationInfoTotalQty"
                         },
                         "search": "#datatableSearch",
                         "entries": "#datatableEntries",
                         "isResponsive": false,
                         "isShowPaging": false,
                         "paging": false
                       }'>
                        <thead class="thead-light">
                        <tr>
                            <th class="table-column-pl-0 border-0">{{translate('messages.order_id')}}</th>
                            <th class="border-0">{{translate('messages.order_date')}}</th>
                            <th class="text-center border-0">{{translate('messages.order')}} {{translate('messages.status')}}</th>
                        </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($orders as $key=>$order)

                            <tr class="status-{{$order['order_status']}} class-all">
                                <td class="table-column-pl-0">
                                    <a href="{{route($parcel_order?'admin.parcel.order.details':'admin.order.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                </td>
                                <td>
                                    <div>
                                        <div>
                                            {{date('d M Y',strtotime($order['created_at']))}}
                                        </div>
                                        <div class="d-block text-uppercase">
                                            {{date(config('timeformat'),strtotime($order['created_at']))}}
                                        </div>
                                    </div>
                                </td>
                                <td class="text-capitalize text-center">
                                    @if($order['order_status']=='pending')
                                        <span class="badge badge-soft-info">
                                          {{translate('messages.pending')}}
                                        </span>
                                    @elseif($order['order_status']=='confirmed')
                                        <span class="badge badge-soft-info">
                                          {{translate('messages.confirmed')}}
                                        </span>
                                    @elseif($order['order_status']=='processing')
                                        <span class="badge badge-soft-warning">
                                          {{translate('messages.processing')}}
                                        </span>
                                    @elseif($order['order_status']=='picked_up')
                                        <span class="badge badge-soft-warning">
                                          {{translate('messages.out_for_delivery')}}
                                        </span>
                                    @elseif($order['order_status']=='delivered')
                                        <span class="badge badge-soft-success">
                                          {{translate('messages.delivered')}}
                                        </span>
                                    @elseif($order['order_status']=='failed')
                                        <span class="badge badge-soft-danger">
                                          {{translate('messages.payment')}}  {{translate('messages.failed')}}
                                        </span>
                                    @elseif($order['order_status']=='handover')
                                        <span class="badge badge-soft-danger">
                                          {{translate('messages.handover')}}
                                        </span>
                                    @elseif($order['order_status']=='canceled')
                                        <span class="badge badge-soft-danger">
                                          {{translate('messages.canceled')}}
                                        </span>
                                    @elseif($order['order_status']=='accepted')
                                        <span class="badge badge-soft-danger">
                                          {{translate('messages.accepted')}}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger">
                                          {{str_replace('_',' ',$order['order_status'])}}
                                        </span>
                                    @endif
                                    @if($order['order_type']=='take_away')
                                        <div class="text-info mt-1">
                                            {{translate('messages.take_away')}}
                                        </div>
                                    @else
                                        <div class="text-title mt-1">
                                          {{translate('messages.home Delivery')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="table-responsive datatable-custom">
                    <table id="datatable"
                           class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table fz--14px"
                           data-hs-datatables-options='{
                         "columnDefs": [{
                            "targets": [0],
                            "orderable": false
                          }],
                         "order": [],
                         "info": {
                           "totalQty": "#datatableWithPaginationInfoTotalQty"
                         },
                         "search": "#datatableSearch",
                         "entries": "#datatableEntries",
                         "isResponsive": false,
                         "isShowPaging": false,
                         "paging": false
                       }'>
                        <thead class="thead-light">
                        <tr>
                            <th class="table-column-pl-0 border-0">{{translate('messages.order_id')}}</th>
                            <th class="border-0">{{translate('messages.order_date')}}</th>
                            <th class="text-center border-0">{{translate('messages.order')}} {{translate('messages.status')}}</th>
                        </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($orders as $key=>$order)

                            <tr class="status-{{$order['order_status']}} class-all">
                                <td class="table-column-pl-0">
                                    <a href="{{route($parcel_order?'admin.parcel.order.details':'admin.order.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                </td>
                                <td>
                                    <div>
                                        <div>
                                            {{date('d M Y',strtotime($order['created_at']))}}
                                        </div>
                                        <div class="d-block text-uppercase">
                                            {{date(config('timeformat'),strtotime($order['created_at']))}}
                                        </div>
                                    </div>
                                </td>
                                <td class="text-capitalize text-center">
                                    @if($order['order_status']=='pending')
                                        <span class="badge badge-soft-info">
                                          {{translate('messages.pending')}}
                                        </span>
                                    @elseif($order['order_status']=='confirmed')
                                        <span class="badge badge-soft-info">
                                          {{translate('messages.confirmed')}}
                                        </span>
                                    @elseif($order['order_status']=='processing')
                                        <span class="badge badge-soft-warning">
                                          {{translate('messages.processing')}}
                                        </span>
                                    @elseif($order['order_status']=='picked_up')
                                        <span class="badge badge-soft-warning">
                                          {{translate('messages.out_for_delivery')}}
                                        </span>
                                    @elseif($order['order_status']=='delivered')
                                        <span class="badge badge-soft-success">
                                          {{translate('messages.delivered')}}
                                        </span>
                                    @elseif($order['order_status']=='failed')
                                        <span class="badge badge-soft-danger">
                                          {{translate('messages.payment')}}  {{translate('messages.failed')}}
                                        </span>
                                    @elseif($order['order_status']=='handover')
                                        <span class="badge badge-soft-danger">
                                          {{translate('messages.handover')}}
                                        </span>
                                    @elseif($order['order_status']=='canceled')
                                        <span class="badge badge-soft-danger">
                                          {{translate('messages.canceled')}}
                                        </span>
                                    @elseif($order['order_status']=='accepted')
                                        <span class="badge badge-soft-danger">
                                          {{translate('messages.accepted')}}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger">
                                          {{str_replace('_',' ',$order['order_status'])}}
                                        </span>
                                    @endif
                                    @if($order['order_type']=='take_away')
                                        <div class="text-info mt-1">
                                            {{translate('messages.take_away')}}
                                        </div>
                                    @else
                                        <div class="text-title mt-1">
                                          {{translate('messages.home Delivery')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Table -->


            @if(count($orders) !== 0)
            <hr>
            @endif
            <div class="page-area">
                {!! $orders->appends($_GET)->links() !!}
            </div>
            @if(count($orders) === 0)
            <div class="empty--data">
                <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                <h5>
                    {{translate('no_data_found')}}
                </h5>
            </div>
            @endif
        </div>
        <!-- End Card -->
        <!-- Order Filter Modal -->
        <div id="datatableFilterSidebar" class="hs-unfold-content_ sidebar sidebar-bordered sidebar-box-shadow initial-hidden">
            <div class="card card-lg sidebar-card sidebar-footer-fixed">
                <div class="card-header">
                    <h4 class="card-header-title">{{translate('messages.order')}} {{translate('messages.filter')}}</h4>

                    <!-- Toggle Button -->
                    <a class="js-hs-unfold-invoker_ btn btn-icon btn-xs btn-ghost-dark ml-2" href="javascript:;"
                    onclick="$('#datatableFilterSidebar,.hs-unfold-overlay').hide(500)">
                        <i class="tio-clear tio-lg"></i>
                    </a>
                    <!-- End Toggle Button -->
                </div>
                <?php
                $filter_count=0;
                if(isset($zone_ids) && count($zone_ids) > 0) $filter_count += 1;
                if(isset($vendor_ids) && count($vendor_ids)>0) $filter_count += 1;
                if($status=='all')
                {
                    if(isset($orderstatus) && count($orderstatus) > 0) $filter_count += 1;
                    if(isset($scheduled) && $scheduled == 1) $filter_count += 1;
                }

                if(isset($from_date) && isset($to_date)) $filter_count += 1;
                if(isset($order_type)) $filter_count += 1;

                ?>
                <!-- Body -->
                <form class="card-body sidebar-body sidebar-scrollbar" action="{{route('admin.order.filter')}}" method="POST" id="order_filter_form">
                    @csrf
                    <small class="text-cap mb-3">{{translate('messages.zone')}}</small>

                    <div class="mb-2 initial--21">
                        <select name="zone[]" id="zone_ids" class="form-control js-select2-custom" multiple="multiple">
                        @foreach(\App\Models\Zone::all() as $zone)
                            <option value="{{$zone->id}}" {{isset($zone_ids)?(in_array($zone->id, $zone_ids)?'selected':''):''}}>{{$zone->name}}</option>
                        @endforeach
                        </select>
                    </div>
                    @if (!$parcel_order)
                        <hr class="my-4">
                        <small class="text-cap mb-3">{{translate('messages.store')}}</small>
                        <div class="mb-2 initial--21">
                            <select name="vendor[]" id="vendor_ids" class="form-control js-select2-custom" multiple="multiple">
                            @foreach(\App\Models\Store::whereIn('id', $vendor_ids)->get() as $store)
                                <option value="{{$store->id}}" selected >{{$store->name}}</option>
                            @endforeach
                            </select>
                        </div>
                    @endif


                    <hr class="my-4">
                    @if($status == 'all')
                    <small class="text-cap mb-3">{{translate('messages.order')}} {{translate('messages.status')}}</small>

                    <!-- Custom Checkbox -->
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus2" name="orderStatus[]" class="custom-control-input" {{isset($orderstatus)?(in_array('pending', $orderstatus)?'checked':''):''}} value="pending">
                        <label class="custom-control-label" for="orderStatus2">{{translate('messages.pending')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus1" name="orderStatus[]" class="custom-control-input" value="confirmed" {{isset($orderstatus)?(in_array('confirmed', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus1">{{translate('messages.confirmed')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus3" name="orderStatus[]" class="custom-control-input" value="processing" {{isset($orderstatus)?(in_array('processing', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus3">{{translate('messages.processing')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus4" name="orderStatus[]" class="custom-control-input" value="picked_up" {{isset($orderstatus)?(in_array('picked_up', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus4">{{translate('messages.out_for_delivery')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus5" name="orderStatus[]" class="custom-control-input" value="delivered" {{isset($orderstatus)?(in_array('delivered', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus5">{{translate('messages.delivered')}}</label>
                    </div>
                    {{-- <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus6" name="orderStatus[]" class="custom-control-input" value="returned" {{isset($orderstatus)?(in_array('returned', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus6">{{translate('messages.returned')}}</label>
                    </div> --}}
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus7" name="orderStatus[]" class="custom-control-input" value="failed" {{isset($orderstatus)?(in_array('failed', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus7">{{translate('messages.failed')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus8" name="orderStatus[]" class="custom-control-input" value="canceled" {{isset($orderstatus)?(in_array('canceled', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus8">{{translate('messages.canceled')}}</label>
                    </div>
                    @if (!$parcel_order)
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus9" name="orderStatus[]" class="custom-control-input" value="refund_requested" {{isset($orderstatus)?(in_array('refund_requested', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus9">{{translate('messages.refundRequest')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus10" name="orderStatus[]" class="custom-control-input" value="refunded" {{isset($orderstatus)?(in_array('refunded', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus10">{{translate('messages.refunded')}}</label>
                    </div>
                    @endif

                    <hr class="my-4">

                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="scheduled" name="scheduled" class="custom-control-input" value="1" {{isset($scheduled)?($scheduled==1?'checked':''):''}}>
                        <label class="custom-control-label text-uppercase" for="scheduled">{{translate('messages.scheduled')}}</label>
                    </div>
                    @endif
                    @if (!$parcel_order)
                        <hr class="my-4">
                        <small class="text-cap mb-3">{{translate('messages.order')}} {{translate('messages.type')}}</small>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="take_away" name="order_type" class="custom-control-input" value="take_away" {{isset($order_type)?($order_type=='take_away'?'checked':''):''}}>
                            <label class="custom-control-label text-uppercase" for="take_away">{{translate('messages.take_away')}}</label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="delivery" name="order_type" class="custom-control-input" value="delivery" {{isset($order_type)?($order_type=='delivery'?'checked':''):''}}>
                            <label class="custom-control-label text-uppercase" for="delivery">{{translate('messages.delivery')}}</label>
                        </div>
                    @endif

                    <hr class="my-4">

                    <small class="text-cap mb-3">{{translate('messages.date')}} {{translate('messages.between')}}</small>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group m-0">
                                <input type="date" name="from_date" class="form-control" id="date_from" value="{{isset($from_date)?$from_date:''}}">
                            </div>
                        </div>
                        <div class="col-12 text-center">----{{ translate('messages.to') }}----</div>
                        <div class="col-12">
                            <div class="form-group">
                                <input type="date" name="to_date" class="form-control" id="date_to" value="{{isset($to_date)?$to_date:''}}">
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer sidebar-footer">
                        <div class="row gx-2">
                            <div class="col">
                                <button type="reset" class="btn btn-block btn-white" id="reset">{{ translate('Clear all filters') }}</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-block btn-primary">{{ translate('messages.save') }}</button>
                            </div>
                        </div>
                    </div>
                    <!-- End Footer -->
                </form>
            </div>
        </div>
        <!-- End Order Filter Modal -->
@endsection

@push('script_2')
    <!-- <script src="{{asset('public/assets/admin')}}/js/bootstrap-select.min.js"></script> -->
    <script>
        $(document).on('ready', function () {
            @if($filter_count>0)
            $('#filter_count').html({{$filter_count}});
            @endif
            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });

            var zone_id = [];
            $('#zone_ids').on('change', function(){
                if($(this).val())
                {
                    zone_id = $(this).val();
                }
                else
                {
                    zone_id = [];
                }
            });


            $('#vendor_ids').select2({
                ajax: {
                    url: '{{url('/')}}/admin/store/get-stores',
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            zone_ids: zone_id,
                            page: params.page
                        };
                    },
                    processResults: function (data) {
                        return {
                        results: data
                        };
                    },
                    __port: function (params, success, failure) {
                        var $request = $.ajax(params);

                        $request.then(success);
                        $request.fail(failure);

                        return $request;
                    }
                }
            });

            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'd-none'
                    },
                    {
                        extend: 'excel',
                        className: 'd-none',
                        action: function (e, dt, node, config)
                        {
                            window.location.href = '{{route("admin.order.export",['status'=>$status,'file_type'=>'excel','type'=>$parcel_order?'parcel':'order', request()->getQueryString()])}}';
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'd-none',
                        action: function (e, dt, node, config)
                        {
                            window.location.href = '{{route("admin.order.export",['status'=>$status,'file_type'=>'csv','type'=>$parcel_order?'parcel':'order', request()->getQueryString()])}}';
                        }
                    },
                    // {
                    //     extend: 'pdf',
                    //     className: 'd-none'
                    // },
                    {
                        extend: 'print',
                        className: 'd-none'
                    },
                ],
                select: {
                    style: 'multi',
                    selector: 'td:first-child input[type="checkbox"]',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                },
                language: {
                    zeroRecords: '<div class="text-center p-4">' +
                        '<img class="w-7rem mb-3" src="{{asset('public/assets/admin')}}/svg/illustrations/sorry.svg" alt="Image Description">' +

                        '</div>'
                }
            });

            $('#export-copy').click(function () {
                datatable.button('.buttons-copy').trigger()
            });

            $('#export-excel').click(function () {
                datatable.button('.buttons-excel').trigger()
            });

            $('#export-csv').click(function () {
                datatable.button('.buttons-csv').trigger()
            });

            // $('#export-pdf').click(function () {
            //     datatable.button('.buttons-pdf').trigger()
            // });

            $('#export-print').click(function () {
                datatable.button('.buttons-print').trigger()
            });

            $('#datatableSearch').on('mouseup', function (e) {
                var $input = $(this),
                    oldValue = $input.val();

                if (oldValue == "") return;

                setTimeout(function () {
                    var newValue = $input.val();

                    if (newValue == "") {
                        // Gotcha
                        datatable.search('').draw();
                    }
                }, 1);
            });

            $('#toggleColumn_order').change(function (e) {
                datatable.columns(1).visible(e.target.checked)
            })

            $('#toggleColumn_date').change(function (e) {
                datatable.columns(2).visible(e.target.checked)
            })

            $('#toggleColumn_customer').change(function (e) {
                datatable.columns(3).visible(e.target.checked)
            })
            $('#toggleColumn_store').change(function (e) {
                datatable.columns(4).visible(e.target.checked)
            })
            $('#toggleColumn_total').change(function (e) {
                datatable.columns(5).visible(e.target.checked)
            })
            $('#toggleColumn_order_status').change(function (e) {
                datatable.columns(6).visible(e.target.checked)
            })

            // $('#toggleColumn_order_type').change(function (e) {
            //     datatable.columns(7).visible(e.target.checked)
            // })

            $('#toggleColumn_actions').change(function (e) {
                datatable.columns(7).visible(e.target.checked)
            })
            // INITIALIZATION OF TAGIFY
            // =======================================================
            $('.js-tagify').each(function () {
                var tagify = $.HSCore.components.HSTagify.init($(this));
            });

            $("#date_from").on("change", function () {
                $('#date_to').attr('min',$(this).val());
            });

            $("#date_to").on("change", function () {
                $('#date_from').attr('max',$(this).val());
            });
        });

        $('#reset').on('click', function(){
            // e.preventDefault();
            location.href = '{{url('/')}}/admin/order/filter/reset';
        });
    </script>

    <script>
        $('#search-form').on('submit', function (e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.get({
                url: '{{route('admin.order.search')}}',
                data: $(this).serialize(),
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush
