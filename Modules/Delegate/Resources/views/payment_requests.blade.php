@extends('backend.layouts.app')

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('assets/css/zoom.css') }}">
@endsection

@section('content')

<div class="card filter-card">
    <div class="card-header row gutters-5">
        <div class="col" data-toggle="collapse" href="#filter" role="button" aria-expanded="false" aria-controls="filter">
            <h5 class="mb-0 h6">{{ translate('Filter') }}</h5>
        </div> 
        @if(request()->query())
        <div class="mb-2 mb-md-0">
            <a href="{{ route('delegates.payment_requests') }}" >{{ translate('Clear Filter') }}</a>
        </div>
        @endif
    </div>
    <div class="card-body collapse show" id="filter">
        <form class="form-horizontal" action="{{ route('delegates.payment_requests') }}" method="GET">
            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">@lang('delegate::delivery.delegate')</label>
                    <select class="form-control aiz-selectpicker" name="delegate" data-live-search="true">
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
                    <label class="col-from-label">{{ translate('Status') }}</label>
                    <select class="form-control aiz-selectpicker" name="status">
                        <option value="" selected disabled hidden>{{ translate('Status') }}</option>
                        <option value="approved" @if(request()->has('status') && request()->filled('status') && request()->get('status') == 'approved') selected @endif>
                            {{translate('Approved')}}
                        </option>
                        <option value="pending" @if(request()->has('status') && request()->filled('status') && request()->get('status') == 'pending') selected @endif>
                            {{translate('Pending')}}
                        </option>
                    </select>
                </div>
                
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Date') }}</label>
                    <input type="text" class="aiz-date-range form-control" value="{{ request()->query('date') }}" name="date" placeholder="{{ translate('date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>

                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Code') }}</label>
                    <input type="text" class="form-control" id="code" name="code" value="{{ request()->query('code') }}" placeholder="{{ translate('Code') }}">
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
            <h5 class="mb-0 h6">@lang('delegate::delivery.payment_requests')</h5>
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
                    <li class="dropdown-item column_visibility" id="code" style="cursor: pointer;">{{ translate('Code') }}</li>
                    <li class="dropdown-item column_visibility" id="date" style="cursor: pointer;">{{ translate('Date') }}</li>
                    <li class="dropdown-item column_visibility" id="name" style="cursor: pointer;">{{ translate('Name')}}</li>
                    <li class="dropdown-item column_visibility" id="province" style="cursor: pointer;">@lang('delegate::delivery.province')</li>
                    <li class="dropdown-item column_visibility" id="system_earnings" style="cursor: pointer;">@lang('delegate::delivery.weekly_system_earnings')</li>
                    <li class="dropdown-item column_visibility" id="status" style="cursor: pointer;">{{translate('Status')}}</li>
                    <li class="dropdown-item column_visibility" id="options" style="cursor: pointer;">{{translate('Options')}}</li>
                </ul>
            </div>
            <a href="" class="export-to-pdf btn btn-primary btn-sm mb-1"><i class="las la-file-pdf fs-18 mr-2"></i>@lang('delegate::delivery.export_to_pdf')</a>
        </div>
        <div class="col-2" style="display: flex; align-items: center;">
            <h6 style="flex: 3;">
                @lang('delegate::delivery.show_entries')
            </h6>
            <select name="entries" id="entries" class="form-control" style="flex: 2;">
                <option value="25" {{ \Cache::get('paginate') == '25' ? 'selected' : '' }}><a href="{{ route('delegates.payment_requests', 25) }}">25</a></option>
                <option value="100" {{ \Cache::get('paginate') == '100' ? 'selected' : '' }}><a href="{{ route('delegates.payment_requests', 100) }}">100</a></option>
                <option value="200" {{ \Cache::get('paginate') == '200' ? 'selected' : '' }}><a href="{{ route('delegates.payment_requests', 200) }}">200</a></option>
                <option value="500" {{ \Cache::get('paginate') == '500' ? 'selected' : '' }}><a href="{{ route('delegates.payment_requests', 500) }}" >500</a></option>
                <option value="1000" {{ \Cache::get('paginate') == '1000' ? 'selected' : '' }}><a href="{{ route('delegates.payment_requests', 1000) }}">1000</a></option>
                <option value="all" {{ \Cache::get('paginate') == 'all' ? 'selected' : '' }}><a href="{{ route('delegates.payment_requests', 'all') }}">{{ translate('all') }}</a></option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
            <tr>
                <th class="code">{{ Translate('Code') }}</th>
                <th class="date" data-breakpoints="lg">{{translate('Date')}}</th>
                <th class="name">{{translate('Name')}}</th>
                <th class="province">@lang('delegate::delivery.province')</th>
                <th class="system_earnings" data-breakpoints="md">@lang('delegate::delivery.weekly_system_earnings')</th>
                <th class="status" data-breakpoints="lg">{{translate('Status')}}</th>
                <th class="options" data-breakpoints="lg">{{translate('options')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($payment_requests as $key => $item)
                <tr>
                    <td class="code">{{ $item->code }}</td>
                    <td class="date">{{ $item->date_request }}</td>
                    <td class="name">{{ $item->delegate->full_name }}</td>
                    <td class="province">
                        <span class="font-weight-bold badge badge-inline badge-dark">{{ $item->delegate->province->name }}</span>
                    </td>
                    <td class="system_earnings">{{ single_price($item->amount) }}</td>
                    <td class="status">
                        @if($item->status == 'pending')
                        <span class="text-capitalize badge badge-inline badge-info">@lang('delegate::delivery.' . $item->status)</span>
                        @elseif($item->status == 'approved')
                        <span class="text-capitalize badge badge-inline badge-success">@lang('delegate::delivery.' . $item->status)</span>
                        @else
                        <span class="text-capitalize badge badge-inline badge-danger">@lang('delegate::delivery.' . $item->status)</span>
                        @endif
                    </td>
                    <td class="options text-right">
                        <a href="javascript:void(0);" class="btn btn-soft-primary btn-icon btn-circle btn-sm" onclick="view_payment_request('{{$item->id}}')" title="{{ translate('View') }}">
                            <i class="las la-eye"></i>
                        </a>
                        <a href="javascript:void(0);" onclick="delete_payment_request('{{ route('delegates.delete_payment_request', $item->id) }}');" class="btn btn-soft-danger btn-icon btn-circle btn-sm" title="{{ translate('Delete') }}">
                            <i class="las la-trash"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="clearfix">
            <div class="pull-right">
                {{ $payment_requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')

<div id="view_payment_request" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="payment_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title h6 text-white font-weight-bold">@lang('delegate::delivery.payment_request_details')</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div id="view_payment_request_body">
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delete_payment_request">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
    <div class="modal-header bg-danger">
      <h5 class="modal-title h6 text-white">@lang('delegate::delivery.payment_request_delete')</h5>
      <button type="button" class="close text-white" data-dismiss="modal">
      </button>
    </div>
    <div class="modal-body">
      <p>@lang('delegate::delivery.delete_confirm')</p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
      <a href="#" id="delete_link" class="btn btn-danger">{{ translate('Delete') }}</a>
    </div>
  </div>
  </div>
</div>

@include('partials.zoom.modal')
@endsection

@section('script')
    <script src="{{ asset('assets/js/zoom.js') }}"></script>
    <script type="text/javascript">
        
        function view_payment_request(id) {
            let url = "{{ route('delegates.view_payment_request', ':id') }}";
                url = url.replace(':id', id);
                
            $.get(url, function(data) {
                $('#view_payment_request #view_payment_request_body').html(data);
                $('#view_payment_request').modal('show', {backdrop: 'static'});
            })
            .fail(function (response) {
                AIZ.plugins.notify('danger', response.responseJSON);
            });
        }

        function delete_payment_request(delete_link){
            $('#delete_payment_request').modal('show');
            document.getElementById('delete_link').setAttribute('href' , delete_link);
        }
        $('#entries').on('change', function() {
            window.location.href = '{{ route("delegates.payment_requests") }}' + '?paginate=' + $(this).val();
        });

        $(".export-to-excel").click(function(e) {
            e.preventDefault();
            $(".aiz-table").table2excel({
                exclude: ".excludeThisClass",
                name: "All payment_requests",
                filename: "payment_requests.xls", 
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
                    pdfMake.createPdf(docDefinition).download("payment_requests.pdf");
                }
            });
        });

        $(".export-to-csv").click(function(e) {
            e.preventDefault();
            $(".aiz-table").tableHTMLExport({
                type:'csv',
                filename: 'payment_requests.csv',
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
