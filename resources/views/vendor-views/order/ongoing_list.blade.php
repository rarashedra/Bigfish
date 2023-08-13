@extends('layouts.vendor.app')

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
                            <span class="badge badge-soft-dark ml-2">{{$orders->total()}}</span>
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
       <div class="row">
            <!-- Table -->
            <div class="col-lg-4">
                <div>
                    <h4 class="text-center">{{ translate('messages.incomming') }} <span>({{$incoming_orders->total() }})</span></h4>
                </div>
                <div class="table-responsive datatable-custom table-primary">
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
                        @foreach($incoming_orders as $key=>$order)

                            <tr class="status-{{$order['order_status']}} class-all">
                                <td class="table-column-pl-0">
                                    <a href="{{route('vendor.order.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
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
                <div>
                    <h4 class="text-center">{{ translate('messages.outgoing') }} <span>({{ $outgoing_orders->total() }})</span></h4>
                </div>
                <div class="table-responsive datatable-custom table table-success table-striped">
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
                        @foreach($outgoing_orders as $key=>$order)
                            <tr class="status-{{$order['order_status']}} class-all">
                                <td class="table-column-pl-0">
                                    <a href="{{route('vendor.order.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
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
                <div>
                    <h4 class="text-center">{{ translate('messages.Ready') }} <span>({{ $handover_orders->total() }})</span></h4>
                </div>
                <div class="table-responsive datatable-custom table-primary">
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
                        @foreach($handover_orders as $key=>$order)
                            <tr class="status-{{$order['order_status']}} class-all">
                                <td class="table-column-pl-0">
                                    <a href="{{route('vendor.order.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
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
        </div>

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
        <!-- End Order Filter Modal -->
@endsection

@push('script_2')



@endpush
