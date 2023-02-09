@extends('backend.layouts.app')

@section('content')

<div class="card filter-card">
    <div class="card-header row gutters-5">
        <div class="col" data-toggle="collapse" href="#filter" role="button" aria-expanded="false" aria-controls="filter">
            <h5 class="mb-0 h6">{{ translate('Filter') }}</h5>
        </div> 
        @if(request()->query())
        <div class="mb-2 mb-md-0">
            <a href="{{ route('affiliate.withdraw_requests') }}" >{{ translate('Clear Filter') }}</a>
        </div>
        @endif
    </div>
    <div class="card-body collapse show" id="filter">
        <form class="form-horizontal" action="{{ route('affiliate.withdraw_requests') }}" method="GET">
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
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{translate('Status')}}</label>
                    <select class="form-control aiz-selectpicker" name="status">
                        <option value="" selected disabled hidden>{{translate('Status')}}</option>
                       
                            <option value="approved" @if(request()->has('status') && request()->filled('status') && request()->get('status') == 'approved') selected @endif>
                               {{translate('Approved')}}
                            </option>
                       
                            <option value="pending" @if(request()->has('status') && request()->filled('status') && request()->get('status') == 'pending') selected @endif>
                               {{translate('Pending')}}
                            </option>
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
    <div class="card-header">
        <div class="col-3">
            <h5 class="mb-0 h6">{{translate('Affiliate Withdraw Request')}}</h5>
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
                    <li class="dropdown-item column_visibility" id="date" style="cursor: pointer;">{{ translate('Date') }}</li>
                    <li class="dropdown-item column_visibility" id="name" style="cursor: pointer;">{{ translate('Name')}}</li>
                    <li class="dropdown-item column_visibility" id="email_address" style="cursor: pointer;">{{translate('Email Address')}}</li>
                    <li class="dropdown-item column_visibility" id="amount" style="cursor: pointer;">{{translate('Amount')}}</li>
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
                <option value="25" {{ \Cache::get('paginate') == '25' ? 'selected' : '' }}><a href="{{ route('affiliate.withdraw_requests', 25) }}">25</a></option>
                <option value="100" {{ \Cache::get('paginate') == '100' ? 'selected' : '' }}><a href="{{ route('affiliate.withdraw_requests', 100) }}">100</a></option>
                <option value="200" {{ \Cache::get('paginate') == '200' ? 'selected' : '' }}><a href="{{ route('affiliate.withdraw_requests', 200) }}">200</a></option>
                <option value="500" {{ \Cache::get('paginate') == '500' ? 'selected' : '' }}><a href="{{ route('affiliate.withdraw_requests', 500) }}" >500</a></option>
                <option value="1000" {{ \Cache::get('paginate') == '1000' ? 'selected' : '' }}><a href="{{ route('affiliate.withdraw_requests', 1000) }}">1000</a></option>
                <option value="all" {{ \Cache::get('paginate') == 'all' ? 'selected' : '' }}><a href="{{ route('affiliate.withdraw_requests', 'all') }}">{{ translate('all') }}</a></option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
            <tr>
                <th>#</th>
                <th class="date" data-breakpoints="lg">{{translate('Date')}}</th>
                <th class="name">{{translate('Name')}}</th>
                <th class="email_address" data-breakpoints="lg">{{translate('Email')}}</th>
                <th class="amount">{{translate('Amount')}}</th>
                <th class="status" data-breakpoints="lg">{{translate('Status')}}</th>
                <th class="options" data-breakpoints="lg">{{translate('options')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($affiliate_withdraw_requests as $key => $affiliate_withdraw_request)
                @php $status = $affiliate_withdraw_request->status ; @endphp
                @if ($affiliate_withdraw_request->user != null)
                    <tr>
                        <td>{{ ($key+1) + ($affiliate_withdraw_requests->currentPage() - 1)*$affiliate_withdraw_requests->perPage() }}</td>
                        <td class="date">{{ $affiliate_withdraw_request->created_at}}</td>
                        <td class="name">{{ optional($affiliate_withdraw_request->user)->name}}</td>
                        <td class="email_address">{{ optional($affiliate_withdraw_request->user)->email}}</td>
                        <td class="amount">{{ single_price($affiliate_withdraw_request->amount)}}</td>
                        <td class="status">
                            @if($status == 1)
                            <span class="badge badge-inline badge-success">{{translate('Approved')}}</span>
                            @elseif($status == 2)
                            <span class="badge badge-inline badge-danger">{{translate('Rejected')}}</span>
                            @else
                            <span class="badge badge-inline badge-info">{{translate('Pending')}}</span>
                            @endif
                        </td>
                        <td class="options text-right">
                        @if($status == 0)
                            <a href="#" class="btn btn-soft-primary btn-icon btn-circle btn-sm" onclick="show_affiliate_withdraw_modal('{{$affiliate_withdraw_request->id}}');" title="{{ translate('Pay Now') }}">
                                <i class="las la-money-bill"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="affiliate_withdraw_reject_modal('{{route('affiliate.withdraw_request.reject', $affiliate_withdraw_request->id)}}');" title="{{ translate('Reject') }}">
                                <i class="las la-trash"></i>
                            </a>
                            @else
                                {{ translate('No Action Available')}}
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td style="float: left;"><strong>@lang('delegate::delivery.total_amount'):</strong></td>
                <td> {{ single_price($affiliate_withdraw_requests->sum('amount')) }} </td>
                <td></td>
                <td></td>
            </tr>

            </tbody>
        </table>
        <div class="clearfix">
            <div class="pull-right">
                {{ $affiliate_withdraw_requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')

<div class="modal fade" id="affiliate_withdraw_modal">
    <div class="modal-dialog">
        <div class="modal-content" id="modal-content">

        </div>
    </div>
</div>

<div class="modal fade" id="affiliate_withdraw_reject_modal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title h6">{{ translate('Affiliate Withdraw Request Reject')}}</h5>
      <button type="button" class="close" data-dismiss="modal">
      </button>
    </div>
    <div class="modal-body">
      <p>{{translate('Are you sure, You want to reject this?')}}</p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
      <a href="#" id="reject_link" class="btn btn-primary">{{ translate('Reject') }}</a>
    </div>
  </div>
  </div>
</div>

@endsection


@section('script')
    <script type="text/javascript">
        function show_affiliate_withdraw_modal(id){
            $.post('{{ route('affiliate_withdraw_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#affiliate_withdraw_modal #modal-content').html(data);
                $('#affiliate_withdraw_modal').modal('show', {backdrop: 'static'});
                AIZ.plugins.bootstrapSelect('refresh');
            });
        }

        function affiliate_withdraw_reject_modal(reject_link){
            $('#affiliate_withdraw_reject_modal').modal('show');
            document.getElementById('reject_link').setAttribute('href' , reject_link);
        }

        // function changeEntries() {
        //     console.log('event');
        // }
        $('#entries').on('change', function() {
            // let url = '{{ route("affiliate.withdraw_requests", ":paginate") }}';
            // url.replace(':paginate', $(this).val());

            window.location.href = '{{ route("affiliate.withdraw_requests") }}' + '?paginate=' + $(this).val();
        });

        $(".export-to-excel").click(function(e) {
            e.preventDefault();
            $(".aiz-table").table2excel({
                exclude: ".excludeThisClass",
                name: "All affiliate_withdraw_requests",
                filename: "affiliate_withdraw_requests.xls", 
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
                    pdfMake.createPdf(docDefinition).download("affiliate_withdraw_requests.pdf");
                }
            });
        });

        $(".export-to-csv").click(function(e) {
            e.preventDefault();
            $(".aiz-table").tableHTMLExport({
                type:'csv',
                filename: 'affiliate_withdraw_requests.csv',
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
