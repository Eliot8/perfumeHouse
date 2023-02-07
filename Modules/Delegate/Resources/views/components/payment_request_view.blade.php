<table class="table table-md table-bordered">
    <thead>
        <tr class="gry-color" style="background: #eceff4;">
            <th width="35%" class="text-left">{{ translate('Code') }}</th>
            <th width="25%" class="text-left">@lang('delegate::delivery.weekly_personal_earnings')</th>
            <th width="25%" class="text-left">@lang('delegate::delivery.weekly_system_earnings')</th>
            <th width="25%" class="text-left">@lang('delegate::delivery.commission_earnings')</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($payments as $item)
            <tr>
                <td>
                    <a href="{{ route('all_orders.show', encrypt($item->order->id)) }}">{{ $item->order->code }}</a>
                </td>
                <td>{{ single_price($item->personal_earnings) }}</td>
                <td>{{ single_price($item->system_earnings) }}</td>
                <td>{{ single_price($item->commission) }}</td>
            </tr>
        @empty
        <tr>
            <td colspan="4">
                <div class="alert alert-primary" role="alert">
                    @lang('delegate::delivery.no_payments_found')
                </div>          
            </td>
        </tr>
        @endforelse
    </tbody>
    @if($payments->count() > 0)
    <tfoot class="font-weight-bold">
        <tr class="gry-color" style="background: #eceff4;">
            <td>{{ Translate('Total') }}</td>
            <td>{{ single_price($payments->sum('personal_earnings')) }}</td>
            <td>{{ single_price($payments->sum('system_earnings')) }}</td>
            <td>{{ single_price($payments->sum('commission')) }}</td>
        </tr>
    </tfoot>
    @endif
  </table>