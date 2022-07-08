@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">@lang('delegate::delivery.provinces')</h1>
        </div>
        @if(Auth::user()->user_type != 'Seller')
        <div class="col text-right">
            <button type="button" class="btn btn-circle btn-info" data-toggle="modal" data-target="#create-modal">
                <span>@lang('delegate::delivery.add_province')</span>
            </button>
            @include('delegate::provinces.create')
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
            <a href="{{ route('provinces.index') }}" >{{ translate('Clear Filter') }}</a>
        </div>
        @endif
    </div>
    <div class="card-body collapse show" id="filter">
        <form class="form-horizontal" action="{{ route('provinces.index') }}" method="GET">
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
                    <label class="col-from-label">{{ translate('Delivery man') }}</label>
                    <select class="form-control aiz-selectpicker" name="delivery_man" data-live-search="true">
                        <option value="" selected disabled hidden>{{ translate('Delivery man') }}</option>
                        @foreach (\Modules\Delegate\Entities\Delegate::select('id', 'full_name')->get() as $delivery_man)
                            <option value="{{ $delivery_man->id }}" @if(request()->has('delivery_man') && request()->filled('delivery_man') && request()->get('delivery_man') == $delivery_man->id) selected @endif>
                                {{ $delivery_man->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">@lang('delegate::delivery.shipping_cost')</label>
                    <select class="form-control aiz-selectpicker" name="shipping_cost">
                        <option value="" selected disabled hidden>@lang('delegate::delivery.shipping_cost')</option>
                        <option value="free" @if(request()->has('shipping_cost') && request()->filled('shipping_cost') && request()->get('shipping_cost') == 'free') selected @endif>@lang('delegate::delivery.free_shipping')</option>
                        <option value="paid" @if(request()->has('shipping_cost') && request()->filled('shipping_cost') && request()->get('shipping_cost') == 'paid') selected @endif>{{translate('Paid')}}</option>
                    </select>
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
    <form class="" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col-3">
                <h5 class="mb-md-0 h6">@lang('delegate::delivery.provinces')</h5>
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
                        <li class="dropdown-item column_visibility" id="delegate" style="cursor: pointer;">@lang('delegate::delivery.delegate')</li>
                        <li class="dropdown-item column_visibility" id="shipping_cost" style="cursor: pointer;">@lang('delegate::delivery.shipping_cost')</li>
                        <li class="dropdown-item column_visibility" id="delegate_cost" style="cursor: pointer;">@lang('delegate::delivery.delegate_cost')</li>
                        <li class="dropdown-item column_visibility" id="options" style="cursor: pointer;">{{translate('Options')}}</li>
                    </ul>
                </div>
                <a href="" class="export-to-pdf btn btn-primary btn-sm mb-1"><i class="las la-file-pdf fs-18 mr-2"></i>@lang('delegate::delivery.export_to_pdf')</a>
            </div>
        </div>
    
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="sm">#</th>
                        <th class="name">{{ translate('Name') }}</th>
                        <th class="delegate" data-breakpoints="sm">@lang('delegate::delivery.delivery_man')</th>
                        <th class="delegate_cost" data-breakpoints="sm">@lang('delegate::delivery.delegate_cost')</th>
                        <th class="shipping_cost" data-breakpoints="sm">@lang('delegate::delivery.shipping_cost')</th>
                        <th class="options" data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($provinces as $key => $province)
                    <tr>
                        <td>{{ ($key+1) + ($provinces->currentPage() - 1)*$provinces->perPage() }}</td>
                        <td class="name">
                            <div class="row gutters-5 w-100px w-md-100px mw-100">
                                <div class="col">
                                    <span class="text-muted text-truncate-2">{{ $province->name }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="delegate">
                            @if($province->delegates->count() > 0)
                            @foreach($province->delegates as $delegate)
                            <span class="btn-soft-success btn-circle btn-sm" style="transition: all 0.3s ease;">{{ $delegate->full_name }}</span>
                            @endforeach
                            @else
                            <span class="btn-soft-danger btn-circle btn-sm" style="transition: all 0.3s ease;">@lang('delegate::delivery.province_empty')</span>
                            @endif
                        </td>
                        <td class="delegate_cost">
                            {{ single_price($province->delegate_cost) }} 
                        </td>
                        <td class="shipping_cost">
                            @if($province->free_shipping)
                            <span class="badge badge-info badge-inline px-2">@lang('delegate::delivery.free_shipping')</span>
                            @else 
                             {{ single_price($province->shipping_cost) }} 
                            @endif
                        </td>
                        <td class="options text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('provinces.edit', $province->id) }}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm" data-toggle="modal" data-target="#delete-modal{{ $province->id }}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </button>
                             @component('delegate::components.delete', ['name' => 'provinces', 'id' => $province->id])@endcomponent
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $provinces->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
    <script type="text/javascript">
        $("[name=shipping_type]").on("change", function (){
            $(".flat_rate_shipping_div").hide();

            if($(this).val() == 'cost'){
                $(".flat_rate_shipping_div").show();
            }

        });

         $(".export-to-excel").click(function(e) {
            e.preventDefault();
            $(".aiz-table").table2excel({
                exclude: ".excludeThisClass",
                name: "All Provinces",
                filename: "provinces.xls", 
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
                    pdfMake.createPdf(docDefinition).download("provinces.pdf");
                }
            });
        });

        $(".export-to-csv").click(function(e) {
            e.preventDefault();
            $(".aiz-table").tableHTMLExport({
                type:'csv',
                filename: 'provinces.csv',
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






