@extends('backend.layouts.app')

@section('content')
<div class="card">
    <div class="card-header row gutters-5" >
        <div class="col">
            <h5 class="mb-0 h6">{{ translate('Filter') }}</h5>
        </div> 
        @if(request()->query())
        <div class="mb-2 mb-md-0">
            <a href="{{ route('all_orders.index') }}" >{{ translate('Clear Filter') }}</a>
        </div>
        @endif
    </div>
    <div class="card-body" >
        <form class="form-horizontal" action="{{ route('all_orders.index') }}" method="GET">
            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Customer') }}</label>
                    <select class="form-control aiz-selectpicker" name="customer">
                        <option value="" selected disabled hidden>{{ translate('customer') }}</option>
                        @foreach (\App\Models\User::where('user_type', 'customer')->get() as $customer)
                            <option value="{{ $customer->id }}" @if(request()->has('customer') && request()->filled('customer') && request()->get('customer') == $customer->id) selected @endif>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Delivery Status') }}</label>
                    <select class="form-control aiz-selectpicker" name="delivery_status">
                        <option value="" selected disabled hidden>{{ translate('delivery status') }}</option>
                        @foreach (getDeliveryStatus() as $key => $value)
                            <option value="{{ $key }}" @if(request()->has('delivery_status') && request()->filled('delivery_status') && request()->get('delivery_status') == $key) selected @endif>
                                 {{ translate($value) }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Payment Status') }}</label>
                    <select class="form-control aiz-selectpicker" name="payment_status">
                        <option value="" selected disabled hidden>{{ translate('payment status') }}</option>
                        @foreach (getPaymentStatus() as $key => $value)
                            <option value="{{ $key }}" @if(request()->has('payment_status') && request()->filled('payment_status') && request()->get('payment_status') == $key) selected @endif> 
                               {{ translate($value) }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Delivery man') }}</label>
                    <select class="form-control aiz-selectpicker" name="delivery_man">
                        <option value="" selected disabled hidden>{{ translate('Delivery man') }}</option>
                        @foreach (\Modules\Delegate\Entities\Delegate::select('id', 'full_name')->get() as $delivery_man)
                            <option value="{{ $delivery_man->id }}" @if(request()->has('delivery_man') && request()->filled('delivery_man') && request()->get('delivery_man') == $delivery_man->id) selected @endif>
                                {{ $delivery_man->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Date') }}</label>
                    <input type="text" class="aiz-date-range form-control" value="{{ request()->query('date') }}" name="date" placeholder="{{ translate('date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Order Code') }}</label>
                    <input type="text" class="form-control" id="order_code" name="order_code" value="{{ request()->query('order_code') }}" placeholder="{{ translate('Type Order code & hit Enter') }}">
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">@lang('delegate::delivery.cancel_request')</label>
                    <div>
                        <label class="aiz-switch aiz-switch-success mb-0">
                        <input value="true" name="cancel_request" type="checkbox" @if(request()->query('cancel_request')) checked @endif>
                        <span class="slider round"></span></label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group mb-0 float-right">
                        <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="card-header row gutters-5">
            <div class="col-3">
                <h5 class="mb-md-0 h6">{{ translate('All Orders') }}</h5>
            </div>


            <div class="col">
                <a href="" class="btn btn-info">{{ translate('Export to CSV') }}</a>
                <a href="" class="btn btn-info">{{ translate('Export to Excel') }}</a>
                <a href="" class="btn btn-info">{{ translate('Print') }}</a>
                <a href="" class="column_visibility btn btn-info">{{ translate('Column visibility') }}</a>
                <a href="" class="btn btn-info">{{ translate('Export to PDF') }}</a>
            </div>

            <div class="dropdown mb-2 mb-md-0">
                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                    {{translate('Bulk Action')}}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" onclick="bulk_delete()"> @lang('delegate::delivery.delete_selection')</a>
                    <a class="dropdown-item" href="#" onclick="bulk_mark_as_confirmed()"> @lang('delegate::delivery.mark_as_confirmed')</a>
                    <a class="dropdown-item" href="#" onclick="bulk_mark_as_paid()"> @lang('delegate::delivery.mark_as_paid')</a>
                </div>
            </div>

            <!-- Change Status Modal -->
            {{-- <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                {{translate('Choose an order status')}}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <select class="form-control aiz-selectpicker" onchange="change_status()" data-minimum-results-for-search="Infinity" id="update_delivery_status">
                                <option value="pending">{{translate('Pending')}}</option>
                                <option value="confirmed">{{translate('Confirmed')}}</option>
                                <option value="picked_up">{{translate('Picked Up')}}</option>
                                <option value="on_the_way">{{translate('On The Way')}}</option>
                                <option value="delivered">{{translate('Delivered')}}</option>
                                <option value="cancelled">{{translate('Cancel')}}</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="delivery_status" id="delivery_status">
                    <option value="">{{translate('Filter by Delivery Status')}}</option>
                    <option value="pending" @if ($delivery_status == 'pending') selected @endif>{{translate('Pending')}}</option>
                    <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>{{translate('Confirmed')}}</option>
                    <option value="picked_up" @if ($delivery_status == 'picked_up') selected @endif>{{translate('Picked Up')}}</option>
                    <option value="on_the_way" @if ($delivery_status == 'on_the_way') selected @endif>{{translate('On The Way')}}</option>
                    <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>{{translate('Delivered')}}</option>
                    <option value="cancelled" @if ($delivery_status == 'cancelled') selected @endif>{{translate('Cancel')}}</option>
                </select>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                </div>
            </div> --}}
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-all">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </th>
                        <th>{{ translate('Order Code') }}</th>
                        <th data-breakpoints="md">{{ translate('Num. of Products') }}</th>
                        <th data-breakpoints="md">{{ translate('Customer') }}</th>
                        <th data-breakpoints="md">{{ translate('Amount') }}</th>
                        <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
                        <th data-breakpoints="md">{{ translate('Payment Status') }}</th>
                        @if (addon_is_activated('refund_request'))
                        <th>{{ translate('Refund') }}</th>
                        @endif
                        <th>@lang('delegate::delivery.cancel_request')</th>
                        <th>@lang('delegate::delivery.delegate')</th>
                        <th class="text-right" width="15%">{{translate('options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $key => $order)
                    <tr>
                        <td>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-one" name="id[]" value="{{$order->id}}">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $order->code }}
                        </td>
                        <td>
                            {{ count($order->orderDetails) }}
                        </td>
                        <td>
                            @if ($order->user != null)
                            {{ $order->user->name }}
                            @else
                            Guest ({{ $order->guest_id }})
                            @endif
                        </td>
                        <td>
                            {{ single_price($order->grand_total) }}
                        </td>
                        <td>
                            @php
                                $status = $order->delivery_status;
                                if($order->delivery_status == 'cancelled') {
                                    $status = '<span class="badge badge-inline badge-danger">'.translate('Cancel').'</span>';
                                }

                            @endphp
                            {!! $status !!}
                        </td>
                        <td>
                            @if ($order->payment_status == 'paid')
                            <span class="badge badge-inline badge-success">{{translate('Paid')}}</span>
                            @else
                            <span class="badge badge-inline badge-danger">{{translate('Unpaid')}}</span>
                            @endif
                        </td>
                        @if (addon_is_activated('refund_request'))
                        <td>
                            @if (count($order->refund_requests) > 0)
                            {{ count($order->refund_requests) }} {{ translate('Refund') }}
                            @else
                            {{ translate('No Refund') }}
                            @endif
                        </td>
                        @endif
                        <td>
                            @if ($order->cancel_request == 1)
                            <span class="badge badge-inline badge-warning">{{  $order->cancel_request_at }}</span>
                            @else
                            <span class="badge badge-inline badge-success"><i class="las la-clipboard-check" style="font-size: 18px;"></i></span>
                            @endif
                        </td>
                        <td>
                            {{ Modules\Delegate\Entities\Delegate::where('user_id', $order->assign_delivery_boy)->first()->full_name ?? 'لم يحدد بعد'}}
                        </td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('all_orders.show', encrypt($order->id))}}" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            @php
                                $comments_not_viewed = \Modules\Delegate\Entities\Comment::where('order_id', $order->id)->where('user_id', '!=', Auth::user()->id)->get();
                                $count = 0;
                                foreach($comments_not_viewed as $comment) {
                                    if($comment->viewed == 0) $count ++;
                                }
                                $locale = app()->getLocale();
                            @endphp
                            <a href="javascript:void(0)" class="btn btn-soft-success btn-icon btn-circle btn-sm position-relative" onclick="show_comments({{ $order->id }})" title="{{ translate('Order Comments') }}">
                                <i class="las la-comments"></i>
                                @if($count > 0)
                                <span class="badge badge-pill badge-primary position-absolute" style="top: -5px; @if($locale == 'sa') right: -8px; @else left: -8px; @endif">{{ $count }}</span>
                                @endif
                            </a>

                            <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="{{ route('invoice.download', $order->id) }}" title="{{ translate('Download Invoice') }}">
                                <i class="las la-download"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('orders.destroy', $order->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="aiz-pagination">
                {{ $orders->appends(request()->input())->links() }}
            </div>

        </div>
    </form>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
     <div class="modal fade" id="order_comments" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div id="order-comments-modal-body">

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if(this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });

        function bulk_delete() {
            var data = new FormData($('#sort_orders')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-order-delete')}}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if(response == 1) {
                        location.reload();
                    }
                }
            });
        }

        function bulk_mark_as_confirmed() {
            var data = new FormData($('#sort_orders')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-order-confirmed')}}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if(response == 1) {
                        AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
                        location.reload();
                    }
                }
            });
        }
        function bulk_mark_as_paid(id){
            var data = new FormData($('#sort_orders')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-order-paid')}}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if(response == 1) {
                        AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
                        location.reload();
                    }
                }
            });
        }

        function show_comments(order_id){
            $('#order-comments-modal-body').html(null);

            if(!$('#modal-size').hasClass('modal-lg')){
                $('#modal-size').addClass('modal-lg');
            }

            $.post('{{ route('purchase_history.comments') }}', { _token : AIZ.data.csrf, order_id : order_id}, function(data){
                $('#order-comments-modal-body').html(data);
                $('#order_comments').modal();
                $('.c-preloader').hide();
            });
        }

        function column_visibility(e){
            e.preventDefault();
            console.log('visibility');
        }
        
    </script>
@endsection
