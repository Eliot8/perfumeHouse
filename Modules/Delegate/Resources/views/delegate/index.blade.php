@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">@lang('delegate::delivery.delegates')</h1>
        </div>
        @if(Auth::user()->user_type != 'Seller')
        <div class="col text-right">
            <a href="{{ route('delegates.create') }}" class="btn btn-circle btn-info">
                <span>@lang('delegate::delivery.add_new_delegate')</span>
            </a>
        </div>
        @endif
    </div>
</div>
<br>

<div class="card filter-card">
    <div class="card-header row gutters-5">
        <div class="col" data-toggle="collapse" href="#filter" role="button" aria-expanded="false" aria-controls="filter">
            <h5 class="mb-0 h6">{{ translate('Filter') }}</h5>
        </div> 
        @if(request()->query())
        <div class="mb-2 mb-md-0">
            <a href="{{ route('delegates.index') }}" >{{ translate('Clear Filter') }}</a>
        </div>
        @endif
    </div>
    <div class="card-body collapse show" id="filter">
        <form class="form-horizontal" action="{{ route('delegates.index') }}" method="GET">
            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">@lang('delegate::delivery.province')</label>
                    <select class="form-control aiz-selectpicker" name="province" data-live-search="true">
                        <option value="" selected disabled hidden>@lang('delegate::delivery.province')</option>
                        @foreach (\Modules\Delegate\Entities\Province::select('id', 'name')->get() as $province)
                            <option value="{{ $province->id }}" @if(request()->has('province') && request()->filled('province') && request()->get('province') == $province->id) selected @endif>
                                {{ $province->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Search') }}</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request()->query('search') }}" placeholder="{{ translate('type & Enter') }}">
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
    <div class="card-header row gutters-5">
        <div class="col-3">
            <h5 class="mb-md-0 h6">@lang('delegate::delivery.delegates')</h5>
        </div>

        <div class="col table-links">
            <a href="" class="export-to-csv btn btn-primary btn-sm mb-1"><i class="las la-file-csv fs-18 mr-2"></i>@lang('delegate::delivery.export_to_csv')</a>
            <a href="" class="export-to-excel btn btn-primary btn-sm mb-1"><i class="las la-file-excel fs-18 mr-2"></i>@lang('delegate::delivery.export_to_excel')</a>
            <a href="" class="print btn btn-primary btn-sm mb-1"><i class="las la-print fs-18 mr-2"></i>@lang('delegate::delivery.print')</a>
            <div class="btn-group mb-1">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                    <i class="las la-columns fs-18 mr-2"></i>
                    @lang('delegate::delivery.column_visibility')
                </button>
                <ul class="dropdown-menu">
                    <li class="dropdown-item column_visibility" id="name" style="cursor: pointer;">{{ translate('Name') }}</li>
                    <li class="dropdown-item column_visibility" id="phone_number" style="cursor: pointer;">{{ translate('Phone') }}</li>
                    <li class="dropdown-item column_visibility" id="email" style="cursor: pointer;">{{ translate('Email') }}</li>
                    <li class="dropdown-item column_visibility" id="province" style="cursor: pointer;">@lang('delegate::delivery.province')</li>
                    <li class="dropdown-item column_visibility" id="earnings" style="cursor: pointer;">@lang('delegate::delivery.earnings')</li>
                    <li class="dropdown-item column_visibility" id="personal_earnings" style="cursor: pointer;">@lang('delegate::delivery.weekly_personal_earnings')</li>
                    <li class="dropdown-item column_visibility" id="system_earnings" style="cursor: pointer;">@lang('delegate::delivery.weekly_system_earnings')</li>
                    <li class="dropdown-item column_visibility" id="commission_earnings" style="cursor: pointer;">@lang('delegate::delivery.commission_earnings')</li>
                    <li class="dropdown-item column_visibility" id="orders_count" style="cursor: pointer;">@lang('delegate::delivery.orders_count')</li>
                    <li class="dropdown-item column_visibility" id="options" style="cursor: pointer;">{{translate('Options')}}</li>
                </ul>
            </div>
            <a href="" class="export-to-pdf btn btn-primary btn-sm mb-1"><i class="las la-file-pdf fs-18 mr-2"></i>@lang('delegate::delivery.export_to_pdf')</a>
        </div>
    </div>
    
    <div class="card-body">
        <form class="" id="sort_products" action="" method="GET">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="lg">#</th>
                        <th class="name">{{ translate('Name') }}</th>
                        <th class="phone_number" data-breakpoints="sm">{{ translate('Phone') }}</th>
                        <th class="email" data-breakpoints="md">{{ translate('Email') }}</th>
                        <th class="province" data-breakpoints="md">@lang('delegate::delivery.province')</th>
                        <th class="earnings" data-breakpoints="md">@lang('delegate::delivery.earnings')</th>
                        <th class="personal_earnings" data-breakpoints="md">@lang('delegate::delivery.weekly_personal_earnings')</th>
                        <th class="system_earnings" data-breakpoints="md">@lang('delegate::delivery.weekly_system_earnings')</th>
                        <th class="commission_earnings" data-breakpoints="md">@lang('delegate::delivery.commission_earnings')</th>
                        <th class="orders_count" data-breakpoints="md">@lang('delegate::delivery.orders_count')</th>
                        <th class="options" data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($delegates as $key => $delegate)
                    <tr>
                        <td>{{ ($key+1) + ($delegates->currentPage() - 1)*$delegates->perPage() }}</td>
                        <td class="name">
                            <div class="row gutters-5 w-100px w-md-100px mw-100">
                                <div class="col">
                                    <span class="text-muted text-truncate-2">{{ $delegate->full_name }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="phone_number">{{ $delegate->phone_number }}</td>
                        <td class="email">{{ $delegate->email }}</td>
                        <td class="province">
                            <strong class="badge badge-inline badge-secondary" style="font-size: 12px; font-weight: 600;">{{ \DB::table('provinces')->where('id', $delegate->province_id)->first()->name }}</strong>
                        </td>
                        @php 
                            $ordersCount = ordersCount($delegate->user_id);
                            $today = date('d-m-Y');
                            $week_orders = \Modules\Delegate\Entities\WeekOrders::where('delivery_man_id', $delegate->id)
                                ->where('week_end', '>', $today)
                                ->first();
                        @endphp
                        <td class="earnings">{{ single_price($delegate->all_earnings) }} </td>
                        <td class="personal_earnings">{{ single_price($week_orders->personal_earnings ?? 0)}} </td>
                        <td class="system_earnings">{{ single_price($week_orders->system_earnings ?? 0)}} </td>
                        <td class="commission_earnings">{{ single_price($delegate->commission_earnings ?? 0)}} </td>
                        <td class="orders_count"><span class="badge badge-inline badge-success">{{ $ordersCount }}</span></td>
                        <td class="options text-right">
                            @if($week_orders)
                            <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="javascript:void(0);" onclick="displayPayments({{ $delegate->id }})" title="@lang('delegate::delivery.payments_list')">
                               <i class="las la-search-dollar"></i>
                            </a>
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm" href="javascript:void(0);" onclick="paymentRequestConfirm({{ $delegate->id }}, '{{ $week_orders->week_end }}')" title="@lang('delegate::delivery.payment_request')">
                               <i class="las la-hand-holding-usd"></i>
                            </a>
                            @endif
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('delegates.edit', $delegate->id) }}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm" data-toggle="modal" data-target="#delete-modal{{ $delegate->id }}" data-id="{{ $delegate->id }}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </button>
                            @component('delegate::components.delete', ['name' => 'delegates', 'id' => $delegate->id])@endcomponent
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $delegates->appends(request()->input())->links() }}
            </div>
        </form>
    </div>
</div>
@endsection
@section('modal')
    <div id="payment_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="payment_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info ">
                    <h4 class="modal-title h6 text-white">@lang('delegate::delivery.payments_list')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div id="payment-modal-body" class="modal-body">
                  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ translate('Cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmation_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="confirmation_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h4 class="modal-title h6 text-white">@lang('delegate::delivery.confirm')</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    @lang('delegate::delivery.confirm')
                    <input type="hidden" id="delegate_id" value="">
                    <input type="hidden" id="week_end" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmation-link" class="btn btn-success btn-sm">{{ translate('Confirm') }}</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ translate('Cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(".export-to-excel").click(function(e) {
        e.preventDefault();
        $(".aiz-table").table2excel({
            exclude: ".excludeThisClass",
            name: "All Delivery men",
            filename: "deliver_men.xls", 
            preserveColors: false 
        });
    });

    $(".export-to-pdf").click(function(e) {
        e.preventDefault();
            html2canvas($('.aiz-table')[0], {
            onrendered: function (canvas) {
                var data = canvas.toDataURL();
                var docDefinition = {
                    content: [{
                        image: data,
                        width: 500
                    }]
                };
                pdfMake.createPdf(docDefinition).download("deliver_men.pdf");
            }
        });
    });

    $(".export-to-csv").click(function(e) {
        e.preventDefault();
        $(".aiz-table").tableHTMLExport({
            type:'csv',
            filename: 'deliver_men.csv',
            separator: ',',
            newline: '\r\n',
            trimContent: true,
            quoteFields: true,
            // CSS selector(s)
            ignoreColumns: '',
            ignoreRows: '',      
            // your html table has html content?
            htmlContent: true,
            // debug
            consoleLog: false,      
        });
    });

    function displayPayments(delegate_id) {
        let url = '{{ route("delegates.payment_request_view", ":delegate_id") }}';
            url = url.replace(':delegate_id', delegate_id);
            
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            url: url,
            type: "GET",
            dataType: "JSON",
            success: function(response) {
                $('#payment-modal-body').html(response);
                $('#payment_modal').modal();
              
            },
            error: function(response) {
                AIZ.plugins.notify('danger', response.responseJSON);
            }
        });
    }

    function paymentRequestConfirm(delegate_id, week_end) {
        $('#confirmation_modal').modal();
        $('#delegate_id').val(delegate_id);
        $(' #week_end').val(week_end);
    }

    $('#confirmation-link').on('click', function() {
        const delegate_id = $('#delegate_id').val();
        const week_end = $('#week_end').val();
        console.log(delegate_id, week_end);
        let url = '{{ route("week.payment.request", [":delegate_id", ":week_end"]) }}';
            url = url.replace(':delegate_id', delegate_id);
            url = url.replace(':week_end', week_end);
            
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            url: url,
            type: "GET",
            dataType: "JSON",
            success: function(response) {
                let url = "{{ route('payment_request.invoice', [':ids', ':name']) }}";
                url = url.replace(':ids', response.ids);
                url = url.replace(':name', response.delegate_name);
                
                AIZ.plugins.notify('success', response.msg);
                window.location.href = url;
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            },
            error: function(response) {
                AIZ.plugins.notify('danger', response.responseJSON);
            }
        });
        $('#confirmation_modal').modal('toggle');
    });
</script>
@endsection






