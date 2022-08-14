@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{translate('All Coupons')}}</h1>
		</div>
		<div class="col-md-6 text-md-right">
			<a href="{{ route('coupon.create') }}" class="btn btn-circle btn-info">
				<span>{{translate('Add New Coupon')}}</span>
			</a>
		</div>
	</div>
</div>

<div class="card filter-card">
    <div class="card-header row gutters-5">
        <div class="col" data-toggle="collapse" href="#filter" role="button" aria-expanded="false" aria-controls="filter">
            <h5 class="mb-0 h6">{{ translate('Filter') }}</h5>
        </div> 
        @if(request()->query())
        <div class="mb-2 mb-md-0">
            <a href="{{ route('coupon.index') }}" >{{ translate('Clear Filter') }}</a>
        </div>
        @endif
    </div>
    <div class="card-body collapse show" id="filter">
        <form class="form-horizontal" action="{{ route('coupon.index') }}" method="GET">
            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">@lang('delegate::delivery.affiliate_user')</label>
                    <select class="form-control aiz-selectpicker" name="affiliate_user" data-live-search="true">
                        <option value="" selected disabled hidden>@lang('delegate::delivery.affiliate_user')</option>
                        @foreach (\App\Models\AffiliateUser::select('id', 'user_id')->get() as $affiliate_user)
                            <option value="{{ $affiliate_user->id }}" @if(request()->has('affiliate_user') && request()->filled('affiliate_user') && request()->get('affiliate_user') == $affiliate_user->id) selected @endif>
                                {{ $affiliate_user->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Commission') }}</label>
                    <select class="form-control aiz-selectpicker" name="commission_type">
                        <option value="" selected disabled hidden>{{ translate('Commission') }}</option>
                        <option value="amount" @if(request()->has('commission_type') && request()->filled('commission_type') && request()->get('commission_type') == 'amount') selected @endif>{{ translate('Amount') }}</option>
                        <option value="percent" @if(request()->has('commission_type') && request()->filled('commission_type') && request()->get('commission_type') == 'percent') selected @endif>{{ translate('Percent') }}</option>
                    </select>
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Start Date') }}</label>
                    <input type="text" class="aiz-date-range form-control" value="{{ request()->query('start_date') }}" name="start_date" placeholder="{{ translate('Start Date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>
                {{-- <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('End Date') }}</label>
                    <input type="text" class="aiz-date-range form-control" value="{{ request()->query('end_date') }}" name="end_date" placeholder="{{ translate('End Date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div> --}}
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Search') }}</label>
                    <input type="text" class="form-control" value="{{ request()->query('search') }}" name="search" placeholder="{{ translate('type & enter') }}" autocomplete="off">
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
  <div class="card-header">
        <div class="col-3">
            <h5 class="mb-0 h6">{{translate('Coupon Information')}}</h5>
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
                    <li class="dropdown-item column_visibility" id="affiliate_user" style="cursor: pointer;">@lang('delegate::delivery.affiliate_user')</li>
                    <li class="dropdown-item column_visibility" id="commission" style="cursor: pointer;">{{ translate('Commission') }}</li>
                    <li class="dropdown-item column_visibility" id="code" style="cursor: pointer;">{{translate('Code')}}</li>
                    <li class="dropdown-item column_visibility" id="type" style="cursor: pointer;">{{translate('Type')}}</li>
                    <li class="dropdown-item column_visibility" id="start_date" style="cursor: pointer;">{{translate('Start Date')}}</li>
                    <li class="dropdown-item column_visibility" id="end_date" style="cursor: pointer;">{{translate('End Date')}}</li>
                    <li class="dropdown-item column_visibility" id="options" style="cursor: pointer;">{{translate('Options')}}</li>
                </ul>
            </div>
            <a href="" class="export-to-pdf btn btn-primary btn-sm mb-1"><i class="las la-file-pdf fs-18 mr-2"></i>@lang('delegate::delivery.export_to_pdf')</a>
        </div>
  </div>
  <div class="card-body">
      <table class="table aiz-table p-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg">#</th>
                    <th class="affiliate_user" >@lang('delegate::delivery.affiliate_user')</th>
                    <th class="commission" >{{ translate('Commission') }}</th>
                    <th class="code" >{{translate('Code')}}</th>
                    <th  class="type" data-breakpoints="lg">{{translate('Type')}}</th>
                    <th  class="start_date" data-breakpoints="lg">{{translate('Start Date')}}</th>
                    <th  class="end_date" data-breakpoints="lg">{{translate('End Date')}}</th>
                    <th  class="options" width="10%">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($coupons as $key => $coupon)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td class="affiliate_user">{{ $coupon->affiliate_user->user->name }}</td>
                        <td class="commission">{{ $coupon->commission }} @if($coupon->commission_type == 'percent') % @endif</td>
                        <td class="code">{{$coupon->code}}</td>
                        <td class="type">@if ($coupon->type == 'cart_base')
                                {{ translate('Cart Base') }}
                            @elseif ($coupon->type == 'product_base')
                                {{ translate('Product Base') }}
                        @endif</td>
                        <td class="start_date">{{ date('d-m-Y', $coupon->start_date) }}</td>
                        <td class="end_date">{{ date('d-m-Y', $coupon->end_date) }}</td>
						<td class="options text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('coupon.edit', encrypt($coupon->id) )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('coupon.destroy', $coupon->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $coupons->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
<script>
    $(".export-to-excel").click(function(e) {
            e.preventDefault();
            $(".aiz-table").table2excel({
                exclude: ".excludeThisClass",
                name: "All coupons",
                filename: "coupons.xls", 
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
                    pdfMake.createPdf(docDefinition).download("coupons.pdf");
                }
            });
        });

        $(".export-to-csv").click(function(e) {
            e.preventDefault();
            $(".aiz-table").tableHTMLExport({
                type:'csv',
                filename: 'coupons.csv',
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
