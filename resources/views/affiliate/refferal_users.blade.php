@extends('backend.layouts.app')

@section('content')

<div class="card filter-card">
    <div class="card-header row gutters-5">
        <div class="col" data-toggle="collapse" href="#filter" role="button" aria-expanded="false" aria-controls="filter">
            <h5 class="mb-0 h6">{{ translate('Filter') }}</h5>
        </div> 
        @if(request()->query())
        <div class="mb-2 mb-md-0">
            <a href="{{ route('refferals.users') }}" >{{ translate('Clear Filter') }}</a>
        </div>
        @endif
    </div>
    <div class="card-body collapse show" id="filter">
        <form class="form-horizontal" action="{{ route('refferals.users') }}" method="GET">
            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">@lang('delegate::delivery.affiliate_user')</label>
                    <select class="form-control aiz-selectpicker" name="affiliate_user" data-live-search="true">
                        <option value="" selected disabled hidden>@lang('delegate::delivery.affiliate_user')</option>
                        @foreach (\App\Models\AffiliateUser::select('id', 'user_id')->get() as $affiliate_user)
                            <option value="{{ $affiliate_user->id }}" @if(request()->has('affiliate_user') && request()->filled('affiliate_user') && request()->get('affiliate_user') == $affiliate_user->id) selected @endif>
                                {{ $affiliate_user->user->name ?? ''}}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Search') }}</label>
                    <input type="text" class="form-control" value="{{ request()->query('search') }}" name="search" placeholder="{{ translate('type & enter') }}" autocomplete="off">
                </div> --}}
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
    <div class="card-header">
        <div class="col-3">
            <h5 class="mb-0 h6">{{ translate('Refferal Users')}}</h5>
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
                    <li class="dropdown-item column_visibility" id="name" style="cursor: pointer;">{{ translate('Name')}}</li>
                    <li class="dropdown-item column_visibility" id="phone" style="cursor: pointer;">{{ translate('Phone') }}</li>
                    <li class="dropdown-item column_visibility" id="email_address" style="cursor: pointer;">{{translate('Email Address')}}</li>
                    <li class="dropdown-item column_visibility" id="reffered_by" style="cursor: pointer;">{{translate('Reffered By')}}</li>
                </ul>
            </div>
            <a href="" class="export-to-pdf btn btn-primary btn-sm mb-1"><i class="las la-file-pdf fs-18 mr-2"></i>@lang('delegate::delivery.export_to_pdf')</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
            <tr>
                <th>#</th>
                <th class="name">{{ translate('Name')}}</th>
                <th class="phone" data-breakpoints="lg">{{ translate('Phone')}}</th>
                <th class="email_address" data-breakpoints="lg">{{ translate('Email Address')}}</th>
                <th class="reffered_by"data-breakpoints="lg">{{ translate('Reffered By')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($refferal_users as $key => $refferal_user)
                @if ($refferal_user != null)
                    <tr>
                        <td>{{ ($key+1) + ($refferal_users->currentPage() - 1)*$refferal_users->perPage() }}</td>
                        <td class="name">{{$refferal_user->name}}</td>
                        <td class="phone">{{$refferal_user->phone}}</td>
                        <td class="email_address">{{$refferal_user->email}}</td>
                        <td class="reffered_by">
                            @if (\App\Models\User::find($refferal_user->referred_by) != null)
                                {{ \App\Models\User::find($refferal_user->referred_by)->name }} ({{ \App\Models\User::find($refferal_user->referred_by)->email }})
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $refferal_users->appends(request()->input())->links() }}
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
                name: "All refferal_users",
                filename: "refferal_users.xls", 
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
                    pdfMake.createPdf(docDefinition).download("refferal_users.pdf");
                }
            });
        });

        $(".export-to-csv").click(function(e) {
            e.preventDefault();
            $(".aiz-table").tableHTMLExport({
                type:'csv',
                filename: 'refferal_users.csv',
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
