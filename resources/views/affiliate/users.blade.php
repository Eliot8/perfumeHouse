@extends('backend.layouts.app')

@section('content')

<div class="card filter-card">
    <div class="card-header row gutters-5">
        <div class="col" data-toggle="collapse" href="#filter" role="button" aria-expanded="false" aria-controls="filter">
            <h5 class="mb-0 h6">{{ translate('Filter') }}</h5>
        </div> 
        @if(request()->query())
        <div class="mb-2 mb-md-0">
            <a href="{{ route('affiliate.users') }}" >{{ translate('Clear Filter') }}</a>
        </div>
        @endif
    </div>
    <div class="card-body collapse show" id="filter">
        <form class="form-horizontal" action="{{ route('affiliate.users') }}" method="GET">
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
                    <label class="col-from-label">{{ translate('Approval') }}</label>
                    <div>
                        <label class="aiz-switch aiz-switch-success mb-0">
                        <input value="true" name="approval" type="checkbox" @if(request()->query('approval')) checked @endif>
                        <span class="slider round"></span></label>
                    </div>
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
            <h5 class="mb-0 h6">{{ translate('Affiliate Users')}}</h5>
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
                    <li class="dropdown-item column_visibility" id="verification_info" style="cursor: pointer;">{{translate('Verification Info')}}</li>
                    <li class="dropdown-item column_visibility" id="approval" style="cursor: pointer;">{{translate('Approval')}}</li>
                    <li class="dropdown-item column_visibility" id="due_amount" style="cursor: pointer;">{{translate('Due Amount')}}</li>
                    <li class="dropdown-item column_visibility" id="options" style="cursor: pointer;">{{translate('Options')}}</li>
                </ul>
            </div>
            <a href="" class="export-to-pdf btn btn-primary btn-sm mb-1"><i class="las la-file-pdf fs-18 mr-2"></i>@lang('delegate::delivery.export_to_pdf')</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table aiz-table">
            <thead>
            <tr>
                <th>#</th>
                <th class="name">{{ translate('Name') }}</th>
                <th class="phone" data-breakpoints="lg">{{ translate('Phone') }}</th>
                <th class="email_address" data-breakpoints="lg">{{ translate('Email Address')}}</th>
                <th class="verification_info" data-breakpoints="lg">{{ translate('Verification Info')}}</th>
                <th class="approval">{{ translate('Approval')}}</th>
                <th class="due_amount" data-breakpoints="lg">{{  translate('Due Amount') }}</th>
                <th class="options" width="10%" class="text-right">{{ translate('Options')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($affiliate_users as $key => $affiliate_user)
                @if($affiliate_user->user != null)
                    <tr>
                        <td>{{ ($key+1) + ($affiliate_users->currentPage() - 1)*$affiliate_users->perPage() }}</td>
                        <td class="name">{{$affiliate_user->user->name}}</td>
                        <td class="phone">{{$affiliate_user->user->phone}}</td>
                        <td class="email_address">{{$affiliate_user->user->email}}</td>
                        <td class="verification_info">
                            @if ($affiliate_user->informations != null)
                                <a href="{{ route('affiliate_users.show_verification_request', $affiliate_user->id) }}">
                                    <span class="badge badge-inline badge-info">{{translate('Show')}}</span>
                                </a>
                            @endif
                        </td>
                        <td class="approval">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_approved(this)" value="{{ $affiliate_user->id }}" type="checkbox" <?php if($affiliate_user->status == 1) echo "checked";?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td class="due_amount">
                            @if ($affiliate_user->balance >= 0)
                                {{ single_price($affiliate_user->balance) }}
                            @endif
                        </td>
                        <td class="options text-right">
                            <a href="#" class="btn btn-soft-primary btn-icon btn-circle btn-sm" onclick="show_payment_modal('{{$affiliate_user->id}}');" title="{{ translate('Pay Now') }}">
                                <i class="las la-money-bill"></i>
                            </a>
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm" href="{{route('affiliate_user.payment_history', encrypt($affiliate_user->id))}}" title="{{ translate('Payment History') }}">
                                <i class="las la-history"></i>
                            </a>
                            <!-- <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('sellers.destroy', $affiliate_user->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a> -->
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
          {{ $affiliate_users->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection

@section('modal')

    @include('modals.delete_modal')

		<div class="modal fade" id="payment_modal">
		    <div class="modal-dialog">
		        <div class="modal-content" id="modal-content">

		        </div>
		    </div>
		</div>

@endsection

@section('script')
    <script type="text/javascript">
        function show_payment_modal(id){
            $.post('{{ route('affiliate_user.payment_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#payment_modal #modal-content').html(data);
                $('#payment_modal').modal('show', {backdrop: 'static'});
                AIZ.plugins.bootstrapSelect('refresh');
            });
        }

        function update_approved(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('affiliate_user.approved') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Approved sellers updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        $(".export-to-excel").click(function(e) {
            e.preventDefault();
            $(".aiz-table").table2excel({
                exclude: ".excludeThisClass",
                name: "All affiliate_users",
                filename: "affiliate_users.xls", 
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
                    pdfMake.createPdf(docDefinition).download("affiliate_users.pdf");
                }
            });
        });

        $(".export-to-csv").click(function(e) {
            e.preventDefault();
            $(".aiz-table").tableHTMLExport({
                type:'csv',
                filename: 'affiliate_users.csv',
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
