@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card">
        <form action="{{ route('delivery.sort_orders') }}" id="sort_orders" method="POST">
            @csrf
            <div class="card-header row gutters-5">
                @php
                    !isset($delivery_status) ?  $delivery_status = null : '';
                    !isset($date) ?  $date = null : '';
                    !isset($sort_search) ?  $sort_search = null : '';
                @endphp
                <div class="col">
                    <h5 class="mb-0 h6">{{ translate('Total Collection History') }}</h5>
                </div>
                <div class="col-lg-3 ml-auto">
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
                <div class="col-lg-3">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control" id="order_search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                    </div>
                </div>
            </div>
        </form>
        @if (count($total_collections) > 0)
            <div class="card-body">
                <table class="table aiz-table mb-0" id="total_collection_list">
                    <thead>
                        <tr>
                            <th>{{ translate('Code')}}</th>
                            <th data-breakpoints="lg">{{ translate('Date')}}</th>
                            <th>{{ translate('Amount')}}</th>
                            <th>{{ translate('Delivery Status')}}</th>
                            <th class="text-right">{{ translate('Options')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($total_collections as $key => $collection)
                            <tr>
                                <td>
                                    <a href="#{{ $collection->code }}" onclick="show_purchase_history_details({{ $collection->id }})">
                                        {{ $collection->code }}
                                    </a>
                                </td>
                                <td>{{ date('d-m-Y', strtotime($collection->created_at)) }}</td>
                                <td>
                                    {{ single_price($collection->orderDetails->sum('price') + $collection->orderDetails->sum('shipping_cost')) }}
                                </td>
                                <td>
                                    <span class="text-capitalize badge badge-inline
                                    @if($collection->delivery_status == 'delivered') badge-primary
                                    @elseif($collection->delivery_status == 'confirmed') badge-success
                                    @else badge-info
                                    @endif"> {{ $collection->delivery_status == 'on_the_way' ? 'On The Way' : ($collection->delivery_status == 'picked_up' ? 'Pick up' : $collection->delivery_status) }}</span>
                                </td>
                                <td class="text-right">
                                @php
                                    $comments_not_viewed = \Modules\Delegate\Entities\Comment::where('order_id', $collection->id)->where('user_id', '!=', Auth::user()->id)->get();
                                    $count = 0;
                                    foreach($comments_not_viewed as $comment) {
                                        if($comment->viewed == 0) $count ++;
                                    }
                                    $locale = app()->getLocale();
                                @endphp
                                    <a href="javascript:void(0)" class="btn btn-soft-primary btn-icon btn-circle btn-sm position-relative" onclick="show_comments({{ $collection->id }})" title="{{ translate('Order Comments') }}">
                                        <i class="las la-comments"></i>
                                        @if($count > 0)
                                        <span class="badge badge-pill badge-primary position-absolute" style="top: -5px; @if($locale == 'sa') right: -8px; @else left: -8px; @endif">{{ $count }}</span>
                                        @endif
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-soft-info btn-icon btn-circle btn-sm" onclick="show_purchase_history_details({{ $collection->id }})" title="{{ translate('Order Details') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                    <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{ route('invoice.download', $collection->id) }}" title="{{ translate('Download Invoice') }}">
                                        <i class="las la-download"></i>
                                    </a>
                                </td>
                            </tr>

                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $total_collections->appends(request()->input())->links() }}
              	</div>
            </div>
        @endif
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

    <div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div id="order-details-modal-body">

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div id="payment_modal_body">

                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">

    (function($) {
    "use strict";
    $('#order_details').on('hidden.bs.modal', function () {
        location.reload();
    });
    $('#order_comments').on('hidden.bs.modal', function () {
        location.reload();
    });

    // function update_status(selectObject) {
    //     var order_id = selectObject.value;
    //     var status = "picked_up";

    //     $.post('{{ route('orders.update_delivery_status') }}', {
    //         _token      : '{{ @csrf_token() }}',
    //         order_id    : order_id,
    //         status      : status
    //     }, function(data){
    //         AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
    //         location.reload();
    //     });
    // }
    
    
    })(jQuery);
    </script>

@endsection
