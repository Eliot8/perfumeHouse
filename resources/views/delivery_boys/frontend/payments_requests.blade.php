@extends('frontend.layouts.user_panel')
@section('extra-css')
    <style>
        .file-box {
            width: 70%;
            height: 285px;
            border: 1px dashed #52495a;
            background-color: #f8f9fa;
            position: relative;
        }

        .file-box img {
            width: 100%;
            height: 100%;
        }
        .file-box span {
            position: absolute;
            right: 5px;
            top: 2px;
            font-size: 16px;
            cursor: pointer;
            color: red;
            z-index: 10;
        }

        @media (min-width: 996px) {
            .table-responsive {
                overflow-x: initial !important;
            }
        }

        .img-modal-target {
            width: 300px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .img-modal-target:hover {
            opacity: 0.7;
        }

        .img-modal {
            display: none;
            position: fixed;
            z-index: 2050;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.8);
        }

        .img-modal-content {
            margin: auto;
            display: block;
            width: 80%;
            opacity: 1 !important;
            max-width: 1200px;
        }

        .img-modal-caption {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 1200px;
            text-align: center;
            color: white;
            font-weight: 700;
            font-size: 1em;
            margin-top: 32px;
        }

        .img-modal-content,
        .img-modal-caption {
            -webkit-animation-name: zoom;
            -webkit-animation-duration: 0.6s;
            animation-name: zoom;
            animation-duration: 0.6s;
        }

        @-webkit-keyframes zoom {
            from {
                -webkit-atransform: scale(0);
            }
            to {
                -webkit-transform: scale(1);
            }
        }

        @keyframes zoom {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        /* The Close Button */
        .img-modal-close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .img-modal-close:hover,
        .img-modal-close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
@endsection
@section('panel_content')
    <div class="row gutters-10">
        @php
            $pending_payment_request = \App\Models\DeliveryBoyPaymentRequest::where('delivery_man_id', auth()->user()->delegate->id)->where('status', 'pending')->first();
            $pending_payment_request = $pending_payment_request ? number_format($pending_payment_request->amount, 0) : 0;
            $weekly_system_earnings =  $week_orders ? number_format(substr($week_orders->system_earnings, 0, -3), 0) : 0;
        @endphp
        <div class="col-md-4 mx-auto mb-3" >
            <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                    <i class="las la-dollar-sign la-2x text-white"></i>
                </span>
                <div class="px-3 pt-3 pb-3">
                    <div class="h4 fw-700 text-center">{{ $pending_payment_request === 0 ? $weekly_system_earnings : 0 }}</div>
                    <div class="opacity-50 text-center">@lang('delegate::delivery.weekly_system_earnings')</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mx-auto mb-3" >
            <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                    <i class="las la-dollar-sign la-2x text-white"></i>
                </span>
                <div class="px-3 pt-3 pb-3">
                    <div class="h4 fw-700 text-center">{{ $pending_payment_request }}</div>
                    <div class="opacity-50 text-center">@lang('delegate::delivery.pending_payment_request')</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mx-auto mb-3" >
            <div class="p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition" data-toggle="modal" data-target="#payment_modal">
                <span class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                    <i class="las la-plus la-3x text-white"></i>
                </span>
                <div class="fs-18 text-primary">@lang('delegate::delivery.payment_request')</div>
            </div>
        </div>
    </div>
    <div class="card">
        <form action="{{ route('delivery_boy.payments_requests') }}" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-0 h6">@lang('delegate::delivery.payment_requests')</h5>
                </div>
                <div class="col-lg-2">
                    <div class="form-group mb-0">
                        <input type="text" class="aiz-date-range form-control" value="{{ request()->query('date') }}" name="date" placeholder="{{ translate('date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control" id="code" name="code" value="{{ request()->query('code') }}" placeholder="{{ translate('Code') }}">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body">
            <table class="table aiz-table mb-0" id="total_collection_list">
                <thead>
                    <tr>
                        <th>@lang('delegate::delivery.id')</th>
                        <th data-breakpoint:s="lg">@lang('delegate::delivery.date_request')</th>
                        <th>@lang('delegate::delivery.amount')</th>
                        <th>@lang('delegate::delivery.attached_pieces')</th>
                        <th>{{ translate('Comment') }}</th>
                        <th>{{ translate('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payment_requests as $key => $item)
                        <tr>
                            <td>{{ $item->code }}</td>
                            <td>{{ date('d-m-Y', strtotime($item->date_request)) }}</td>
                            <td>{{ single_price($item->amount) }}</td>
                            <td>
                                <a href="javascript:void(0)" class="btn btn-soft-info btn-icon btn-circle btn-sm" onclick="show_attached_pieces({{ $item->id }})" title="@lang('delegate::delivery.view_screenshots')">
                                    <i class="las la-eye"></i>
                                </a>
                            </td>
                            <td>{{ Str::limit($item->comment, 20, '...') }}</td>
                            <td>
                                @if($item->status == 'pending')
                                <span class="text-capitalize badge badge-inline badge-info">@lang('delegate::delivery.' . $item->status)</span>
                                @elseif($item->status == 'approved')
                                <span class="text-capitalize badge badge-inline badge-success">@lang('delegate::delivery.' . $item->status)</span>
                                @else
                                <span class="text-capitalize badge badge-inline badge-danger">@lang('delegate::delivery.' . $item->status)</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if (count($payment_requests) > 0)
                <div class="aiz-pagination">
                    {{ $payment_requests->appends(request()->input())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('modal')
    <div id="payment_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="payment_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title h6 text-white">@lang('delegate::delivery.payment_request')</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ route('delivery_boy.send_payment_request') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="payment-modal-body" class="modal-body">
                        <div class="form-group mb-3">
                            <label for="amount">{{ translate('Amount') }}<span class="text-danger">*</span></label>
                            <input type="text" name="amount" class="form-control" value="{{ number_format(substr($week_orders->system_earnings, 0, -3), 0) }}" readonly required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="attached_pieces">@lang('delegate::delivery.attached_pieces')<span class="text-danger">*</span></label>
                            <input type="file" name="attached_pieces[]" id="input-file" class="form-control" accept="image/png, image/jpeg" multiple required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="comment">{{ translate('Comment') }}</label>
                            <textarea name="comment" id="comment" cols="30" rows="2" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm">@lang('delegate::delivery.send')</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="attached_pieces_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="payment_request_details" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">@lang('delegate::delivery.attached_pieces')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div id="attached-pieces-modal-body" class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- IMAGE MODAL -->
    <div id="img-modal" class="img-modal">
        <span id="img-modal-close" class="img-modal-close">&times;</span>
        <img id="img-modal-content" class="img-modal-content">
        <div id="img-modal-caption" class="img-modal-caption"></div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var modal = document.getElementById('img-modal');

        var modalClose = document.getElementById('img-modal-close');
        modalClose.addEventListener('click', function() {
            modal.style.display = "none";
        });

        document.addEventListener('click', function(e) {
            if (e.target.className.indexOf('img-modal-target') !== -1) {
                var img = e.target;
                var modalImg = document.getElementById("img-modal-content");
                var captionText = document.getElementById("img-modal-caption");
                modal.style.display = "block";
                modalImg.src = img.src;
                captionText.innerHTML = img.alt;
            }
        });
        $('#input-file').val('');

        function show_attached_pieces(payment_request_id) {
            $('#attached-pieces-modal-body').html(null);
            let url = "{{ route('payment_request.attached_pieces', ':id') }}";
                url = url.replace(':id', payment_request_id);
                
            $.get(url, function(data){
                $('#attached-pieces-modal-body').html(data);
                $('#attached_pieces_modal').modal();
                $('.c-preloader').hide();
            });
        }
    </script>

@endsection
