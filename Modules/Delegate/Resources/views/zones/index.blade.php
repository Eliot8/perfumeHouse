@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">@lang('delegate::delivery.zones')</h1>
        </div>
        @if(Auth::user()->user_type != 'Seller')
        <div class="col text-right">
            <button type="button" class="btn btn-circle btn-info" data-toggle="modal" data-target="#create-modal">
                <span>@lang('delegate::delivery.add_zone')</span>
            </button>
            <button type="button" class="btn btn-circle btn-success" data-toggle="modal" data-target="#create-neighborhood-modal">
                <span>@lang('delegate::delivery.add_neighborhood')</span>
            </button>
            @include('delegate::zones.create')
            @include('delegate::components.create_neighborhood')
        </div>
        @endif
    </div>
</div>
<br>

<div class="card">
 
    <div class="card-header row gutters-5">
        <div class="col-3">
            <h5 class="mb-md-0 h6">@lang('delegate::delivery.zones')</h5>
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
                    <li class="dropdown-item column_visibility" id="options" style="cursor: pointer;">{{translate('Options')}}</li>
                </ul>
            </div>
            <a href="" class="export-to-pdf btn btn-primary btn-sm mb-1"><i class="las la-file-pdf fs-18 mr-2"></i>@lang('delegate::delivery.export_to_pdf')</a>
        </div>

        <div class="col-lg-3 form-group">
            <form class="" id="filter_by_province" action="{{ route('zones.index') }}" method="GET">
                <select class="form-control aiz-selectpicker" data-live-search="true" name="province" onchange="filter_by_province()">
                    <option value="" selected disabled hidden>@lang('delegate::delivery.province')</option>
                    @foreach (\Modules\Delegate\Entities\Province::select('id', 'name')->get() as $province)
                        <option value="{{ $province->id }}" @if(request()->has('province') && request()->filled('province') && request()->get('province') == $province->id) selected @endif>
                            {{ $province->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="sm">#</th>
                    <th class="name">{{ translate('Name') }}</th>
                    <th class="province" data-breakpoints="sm">@lang('delegate::delivery.province')</th>
                    <th class="options" data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($zones as $key => $zone)
                <tr>
                    <td>{{ ($key+1) + ($zones->currentPage() - 1)*$zones->perPage() }}</td>
                    <td class="name">
                        <div class="row gutters-5 w-100px w-md-100px mw-100">
                            <div class="col">
                                <span class="text-muted text-truncate-2">{{ $zone->name }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="province"><strong class="btn-soft-info btn-circle btn-sm" style="transition: all 0.3s ease;">{{ $zone->province ? $zone->province->name : '' }}</strong></td>
                    <td class="options text-right">
                        <button type="button" class="btn btn-soft-success btn-icon btn-circle btn-sm" data-toggle="modal" data-target="#view-neighborhoods-modal{{ $zone->id }}" title="@lang('delegate::delivery.view_neighborhood')">
                            <i class="las la-list"></i>
                        </button>
                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('zones.edit', $zone->id) }}" title="{{ translate('Edit') }}">
                            <i class="las la-edit"></i>
                        </a>
                        <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm" data-toggle="modal" data-target="#delete-modal{{ $zone->id }}" title="{{ translate('Delete') }}">
                            <i class="las la-trash"></i>
                        </button>
                        @component('delegate::components.neighborhoods', ['zone' => $zone])@endcomponent
                        @component('delegate::components.delete', ['name' => 'zones', 'id' => $zone->id])@endcomponent
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $zones->appends(request()->input())->links() }}
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
    setInterval(() => {
        $('.province-select').each(function(i, element) {
            $(element).children(':first').children().eq(1).remove();
            $(element).children(':first').children().eq(2).remove();
        })
        
    }, 2000);

    $(".export-to-excel").click(function(e) {
            e.preventDefault();
            $(".aiz-table").table2excel({
                exclude: ".excludeThisClass",
                name: "All Zones",
                filename: "zones.xls", 
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
                    pdfMake.createPdf(docDefinition).download("zones.pdf");
                }
            });
        });

        $(".export-to-csv").click(function(e) {
            e.preventDefault();
            $(".aiz-table").tableHTMLExport({
                type:'csv',
                filename: 'zones.csv',
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

        function filter_by_province(){
            $('#filter_by_province').submit();
        }
</script>
@endsection






