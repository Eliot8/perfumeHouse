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
                            <strong class="btn-soft-info btn-circle btn-sm" style="transition: all 0.3s ease;">{{ \DB::table('provinces')->where('id', $delegate->province_id)->first()->name }}</strong>
                        </td>
                        @php 
                        $ordersCount = ordersCount($delegate->user_id);
                        $price = $delegate->province->delegate_cost * $ordersCount;
                        @endphp
                        <td class="earnings">{{ single_price($price) }} </td>
                        <td class="orders_count"><span class="badge badge-inline badge-success">{{ $ordersCount }}</span></td>
                        <td class="options text-right">
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
</script>
@endsection






