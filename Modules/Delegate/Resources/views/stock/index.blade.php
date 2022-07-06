@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">@lang('delegate::delivery.stock_management')</h1>
        </div>
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
            <a href="{{ route('stock.index') }}" >{{ translate('Clear Filter') }}</a>
        </div>
        @endif
    </div>
    <div class="card-body collapse show" id="filter">
        <form class="form-horizontal" action="{{ route('stock.index') }}" method="GET">
            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">@lang('delegate::delivery.delegate')</label>
                    <select class="form-control aiz-selectpicker" name="delegate">
                        <option value="" selected disabled hidden>@lang('delegate::delivery.delegate')</option>
                        @foreach (\Modules\Delegate\Entities\Delegate::select('id', 'full_name')->get() as $delegate)
                            <option value="{{ $delegate->id }}" @if(request()->has('delegate') && request()->filled('delegate') && request()->get('delegate') == $delegate->id) selected @endif>
                                {{ $delegate->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">@lang('delegate::delivery.province')</label>
                    <select class="form-control aiz-selectpicker" name="province">
                        <option value="" selected disabled hidden>@lang('delegate::delivery.province')</option>
                        @foreach (\Modules\Delegate\Entities\Province::select('id', 'name')->get() as $province)
                            <option value="{{ $province->id }}" @if(request()->has('province') && request()->filled('province') && request()->get('province') == $province->id) selected @endif>
                                {{ $province->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Stock') }}</label>
                    <select class="form-control aiz-selectpicker" name="stock">
                        <option value="" selected disabled hidden>{{ translate('Stock') }}</option>
                        <option value="high" @if(request()->has('stock') && request()->filled('stock') && request()->get('stock') == 'high') selected @endif>@lang('delegate::delivery.high')</option>
                        <option value="low" @if(request()->has('stock') && request()->filled('stock') && request()->get('stock') == 'low') selected @endif>@lang('delegate::delivery.low')</option>
                        <option value="empty" @if(request()->has('stock') && request()->filled('stock') && request()->get('stock') == 'empty') selected @endif>@lang('delegate::delivery.empty')</option>
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
                <h5 class="mb-md-0 h6">@lang('delegate::delivery.stock_management')</h5>
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
                        <li class="dropdown-item column_visibility" id="province" style="cursor: pointer;">@lang('delegate::delivery.province')</li>
                        <li class="dropdown-item column_visibility" id="stock" style="cursor: pointer;">{{ translate('Stock') }}</li>
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
                        <th data-breakpoints="lg">#</th>
                        <th class="name">{{ translate('Name') }}</th>
                        <th class="province" data-breakpoints="md">@lang('delegate::delivery.province')</th>
                        <th class="stock" data-breakpoints="lg">{{ translate('Stock') }}</th>
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
                        <td class="province">
                           <span class="text-muted">{{ \DB::table('provinces')->where('id', $delegate->province_id)->first()->name }}</span>
                        </td>
                        <td class="stock">
                            @if(getStockLevel($delegate->id) == 'empty')
                            <span class="badge badge-inline badge-danger btn-circle btn-sm" style="transition: all 0.3s ease;">@lang('delegate::delivery.' . getStockLevel($delegate->id))</span>
                            @else 
                            <span class="badge badge-inline badge-{{ getStockLevel($delegate->id) == 'high' ? 'success' : 'primary' }} btn-circle btn-sm" style="transition: all 0.3s ease;">@lang('delegate::delivery.' . getStockLevel($delegate->id))</span>
                            @endif 
                        </td>
                        <td class="options text-right">
                            <a class="btn btn-soft-dark btn-icon btn-circle btn-sm"  href="{{ route('stock.manage', $delegate->id) }}" target="_blank" title="@lang('delegate::delivery.manage')">
                                <i class="las la-tools"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $delegates->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    $(".export-to-excel").click(function(e) {
            e.preventDefault();
            $(".aiz-table").table2excel({
                exclude: ".excludeThisClass",
                name: "All Stock",
                filename: "stock.xls", 
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
                    pdfMake.createPdf(docDefinition).download("stock.pdf");
                }
            });
        });

        $(".export-to-csv").click(function(e) {
            e.preventDefault();
            $(".aiz-table").tableHTMLExport({
                type:'csv',
                filename: 'stock.csv',
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






