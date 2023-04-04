<div class="row gutters-10">
    <div class="col-md-4 mx-auto mb-3" >
      <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
        <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
            <i class="las la-dollar-sign la-2x text-white"></i>
        </span>
        <div class="px-3 pt-3 pb-3">
            <div class="h4 fw-700 text-center">{{ single_price($affiliate_user->balance) }}</div>
            <div class="opacity-50 text-center">@lang('delegate::delivery.commission_available_for_withdrawal')</div>
        </div>
      </div>
    </div>
    <div class="col-md-4 mx-auto mb-3" >
      <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
        <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
            <i class="las la-dollar-sign la-2x text-white"></i>
        </span>
        <div class="px-3 pt-3 pb-3">
            <div class="h4 fw-700 text-center">{{ single_price($affiliate_user->balance_pending) }}</div>
            <div class="opacity-50 text-center">@lang('delegate::delivery.pending_commission')</div>
        </div>
      </div>
    </div>
    <div class="col-md-4 mx-auto mb-3" >
      <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
        <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
            <i class="las la-dollar-sign la-2x text-white"></i>
        </span>
        <div class="px-3 pt-3 pb-3">
            <div class="h4 fw-700 text-center">{{ single_price($withdrawal_commission) }}</div>
            <div class="opacity-50 text-center">@lang('delegate::delivery.withdrawal_commission')</div>
        </div>
      </div>
    </div>
</div>
<div class="row">
    <table class="table table-md table-bordered">
        <thead>
            <tr class="gry-color" style="background: #eceff4;">
                <th width="35%" class="text-left">{{ translate('Code') }}</th>
                <th width="25%" class="text-left">@lang('delegate::delivery.affiliate_commission')</th>
                <th width="25%" class="text-left">@lang('delegate::delivery.commission_available_for_withdrawal')</th>
                <th width="25%" class="text-left">@lang('delegate::delivery.pending_commission')</th>
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
</div>