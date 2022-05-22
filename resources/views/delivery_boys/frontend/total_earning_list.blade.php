@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Earnings History') }}</h5>
        </div>

        @if (count($total_earnings) > 0)
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ translate('Code')}}</th>
                            <th data-breakpoints="lg">{{ translate('Date')}}</th>
                            <th>{{ translate('Amount')}}</th>
                            <th>@lang('delegate::delivery.personal_earnings')</th>
                            <th>@lang('delegate::delivery.system_earnings')</th>
                            <th class="text-right">{{ translate('Options')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($total_earnings as $key => $item)
                        @php
                            $amount = $item->orderDetails->sum('price') + $item->orderDetails->sum('shipping_cost');
                            $personal_earnings = $item->province->delegate_cost;
                            $system_earnings = $amount - $personal_earnings;
                        @endphp
                            <tr>
                                <td>
                                    <a href="#{{ $item->code }}" onclick="show_purchase_history_details({{ $item->id }})">
                                        {{ $item->code }}
                                    </a>
                                </td>
                                <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                <td>{{ single_price($amount) }}</td>
                                <td>{{ single_price($personal_earnings) }}</td>
                                <td>{{ single_price($system_earnings) }}</td>

                                <td class="text-right">
                                    <a href="javascript:void(0)" class="btn btn-soft-info btn-icon btn-circle btn-sm" onclick="show_purchase_history_details({{ $item->id }})" title="{{ translate('Order Details') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                    <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{ route('invoice.download', $item->id) }}" title="{{ translate('Download Invoice') }}">
                                        <i class="las la-download"></i>
                                    </a>
                                </td>
                            </tr>

                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $total_earnings->appends(request()->input())->links() }}
              	</div>
            </div>
        @endif
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')

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

    function update_status(selectObject) {
        var order_id = selectObject.value;
        var status = "picked_up";

        $.post('{{ route('orders.update_delivery_status') }}', {
            _token      : '{{ @csrf_token() }}',
            order_id    : order_id,
            status      : status
        }, function(data){
            AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
            location.reload();
        });
    }
    })(jQuery);

    </script>

@endsection
