@extends('frontend.layouts.app')

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-start">
                @include('frontend.inc.user_side_nav')
                <div class="aiz-user-panel">
                    <div class="aiz-titlebar mt-2 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h1 class="h3">{{ translate('Affiliate') }}</h1>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">@lang('delegate::delivery.affiliate_commission_tracking')</h5>
                        </div>
                          <div class="card-body">
                              <table class="table aiz-table mb-0">
                                    <thead>
                                        <tr class="gry-color" style="background: #eceff4;">
                                            <th width="35%" class="text-left">{{ translate('Code') }}</th>
                                            <th width="25%" class="text-left">@lang('delegate::delivery.affiliate_commission')</th>
                                            <th width="25%" class="text-left">@lang('delegate::delivery.commission_available_for_withdrawal')</th>
                                            <th width="25%" class="text-left">@lang('delegate::delivery.pending_commission')</th>
                                            <th width="35%" class="text-left">{{ Translate('Date') }}</th>
                                        </tr>
                                    </thead>
                                  <tbody>
                                    @forelse ($affiliate_users_histories as $item)
                                        <tr>
                                            <td>
                                                <a href="{{ route('all_orders.show', encrypt($item->order_id)) }}">{{ $item->order->code }}</a>
                                            </td>
                                            <td>{{ single_price($item->commission) }}</td>
                                            <td>{{ single_price($item->balance) }}</td>
                                            <td>{{ single_price($item->pending_balance) }}</td>
                                            <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                        </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="alert alert-primary" role="alert">
                                                @lang('delegate::delivery.no_commission_found')
                                            </div>          
                                        </td>
                                    </tr>
                                    @endforelse

                                  </tbody>
                              </table>
                              <div class="aiz-pagination">
                                  {{ $affiliate_users_histories->links() }}
                              </div>
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('modal')

    <div class="modal fade" id="affiliate_withdraw_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('Affiliate Withdraw Request') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>

                <form class="" action="{{ route('affiliate.withdraw_request.store') }}" method="post">
                    @csrf
                    <div class="modal-body gry-bg px-3 pt-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label>{{ translate('Amount')}} <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <input type="number" class="form-control mb-3" name="amount" min="1" max="{{ Auth::user()->affiliate_user->balance }}" placeholder="{{ translate('Amount')}}" required>
                            </div>
                        </div>
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-sm btn-primary transition-3d-hover mr-1">{{translate('Confirm')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        function copyToClipboard(btn){
            // var el_code = document.getElementById('referral_code');
            var el_url = document.getElementById('referral_code_url');
            // var c_b = document.getElementById('ref-cp-btn');
            var c_u_b = document.getElementById('ref-cpurl-btn');

            // if(btn == 'code'){
            //     if(el_code != null && c_b != null){
            //         el_code.select();
            //         document.execCommand('copy');
            //         c_b .innerHTML  = c_b.dataset.attrcpy;
            //     }
            // }

            if(btn == 'url'){
                if(el_url != null && c_u_b != null){
                    el_url.select();
                    document.execCommand('copy');
                    c_u_b .innerHTML  = c_u_b.dataset.attrcpy;
                }
            }
        }

        function show_affiliate_withdraw_modal(){
            $('#affiliate_withdraw_modal').modal('show');
        }
    </script>
@endsection
