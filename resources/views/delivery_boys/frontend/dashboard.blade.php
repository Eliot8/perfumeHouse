@extends('frontend.layouts.user_panel')
@section('extra-css')
<style>
    .bg-grad-1 {
        background-color: #eb4786;
        background-image: linear-gradient(315deg, #eb4786 0%, #b854a6 74%);
    }
    .bg-grad-5 {
        /* background-color: #8c64dd; 
        background-image: linear-gradient(315deg, #8c64dd 0%, #7764dd 74%); */
        /* background-image: linear-gradient(to right top, #b91c1c, #bc2719, #be3015, #c03911, #c2410c); */
        background-image: linear-gradient(to right top, #991b1b, #a91e1e, #ba2021, #cb2323, #dc2626);
    }
    .bg-grad-6 {
        /* background-color: #fdce4b; 
        background-image: linear-gradient(315deg, #ecdc65 0%, #bd9e49 74%); */
        /* background-image: linear-gradient(to right top, #ca8a04, #ce8602, #d28102, #d57c03, #d97706); */
        background-image: linear-gradient(to right top, #d97706, #d57c03, #d28102, #ce8602, #ca8a04);
    }
    .bg-grad-7 {
        /* background-color: #767676;
        background-image: linear-gradient(315deg, #767676 0%, #282828 74%); */
        background-color: #4473b7; 
        background-image: linear-gradient(315deg, #4473b7 0%, #114781 74%);
    }
    .bg-grad-8 {
        /* background-color: #56bb78; 
        background-image: linear-gradient(315deg, #56bb78 0%, #4f996a 74%); */
         background-image: linear-gradient(to right top, #065f46, #076142, #0a623e, #106439, #166534);
    }
    .bg-grad-9 {
        /* background-color: #00c896; 
        background-image: linear-gradient(315deg, #00c896 0%, #325e9f 74%); */
        background-image: linear-gradient(to right top, #0f766e, #007678, #007681, #007589, #0e7490);
    }
    .bg-grad-10 {
        /* background-color: #00c896; 
        background-image: linear-gradient(315deg, #00c896 0%, #325e9f 74%); */
         background-image: linear-gradient(to right top, #155e75, #065f6f, #005f69, #055f61, #115e59);  
    }
    
    .bg-grad-11 {
      background-image: linear-gradient(to right top, #3730a3, #3234a6, #2c38a9, #263cac, #1e40af);
    }
    
</style>
@endsection
@section('panel_content')

@php 
$delivery_boy_info = \App\Models\DeliveryBoy::where('user_id', Auth::user()->id)->first();
$delegate = \Modules\Delegate\Entities\Delegate::where('user_id', Auth::user()->id)->first();
@endphp
<div class="aiz-titlebar mt-2 mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Dashboard') }}</h1>
        </div>
    </div>
</div>
<div class="row gutters-10">
    <div class="col-md-3">
        <div class="bg-grad-1 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3 text-center">
                <i class="las la-hourglass-half la-4x"></i>
                <div class="opacity-50">{{ translate('Assigned Delivery') }}</div>
                @php   
                $total_assigned_delivery = \App\Models\Order::where('assign_delivery_boy', Auth::user()->id)
                                            ->where('delivery_status', 'confirmed')
                                            ->get();
                @endphp
                 <div class="h3 fw-700">
                    {{ count($total_assigned_delivery) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bg-grad-2 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3 text-center">
                <i class="las la-luggage-cart la-4x"></i>
                <div class="opacity-50">{{ translate('Pick up Delivery') }}</div>
                @php   
                $total_picked_up_delivery = \App\Models\Order::where('assign_delivery_boy', Auth::user()->id)
                                            ->where('delivery_status', 'picked_up')
                                            ->get();
                @endphp
                 <div class="h3 fw-700">
                   {{ count($total_picked_up_delivery) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bg-grad-3 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3 text-center">
                <i class="las la-running la-4x"></i>
                <div class="opacity-50">{{ translate('On The Way Delivery') }}</div>
                @php   
                $total_on_the_way_delivery = \App\Models\Order::where('assign_delivery_boy', Auth::user()->id)
                                            ->where('delivery_status', 'on_the_way')
                                            ->get();
                @endphp
                 <div class="h3 fw-700">
                    {{ count($total_on_the_way_delivery) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bg-grad-4 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3 text-center">
                <i class="las la-shipping-fast la-4x"></i>
                <div class="opacity-50">{{ translate('Completed Delivery') }}</div>
                @php 
                $total_complete_delivery = \App\Models\Order::where('assign_delivery_boy', Auth::user()->id)
                                            ->where('delivery_status', 'delivered')
                                            ->get();
                @endphp
                @if(count($total_complete_delivery))
                <div class="h3 fw-700">
                    {{ count($total_complete_delivery) }}
                </div>
                @else
                <div class="h3 fw-700">0</div>
                @endif
                
            </div>
            
        </div>
    </div>
    <div class="col-md-3">
        <div class="bg-grad-5 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3 text-center">
                <i class="las la-clock la-4x"></i>
                <div class="opacity-50">{{ translate('Pending Delivery') }}</div>
                @php 
                $total_pending_delivery = \App\Models\Order::where('assign_delivery_boy', Auth::user()->id)
                                            ->where('delivery_status', '!=', 'delivered')
                                            ->where('delivery_status', '!=', 'cancelled')
                                            ->get();
                @endphp
                 @if(count($total_pending_delivery))
                <div class="h3 fw-700">
                    {{ count($total_pending_delivery) }}
                </div>
                @else
                <div class="h3 fw-700">0</div>
                @endif
                
            </div>
            
        </div>
    </div>
     <div class="col-md-3">
        <div class="bg-grad-6 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3 text-center">
                <i class="las la-times-circle la-4x"></i>
                <div class="opacity-50">{{ translate('Cancelled Delivery') }}</div>
                 @php 
                $total_cancelled_delivery = \App\Models\Order::where('assign_delivery_boy', Auth::user()->id)
                                            ->where('delivery_status', 'cancelled')
                                            ->get();
                @endphp
                 <div class="h3 fw-700">
                    {{ count($total_cancelled_delivery) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bg-grad-7 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3 text-center">
                <i class="las la-layer-group la-4x"></i>
                <div class="opacity-50">{{ translate('Total Stock') }}</div>
                @php 
                    $delegate_products = \Modules\Delegate\Entities\Stock::where('delegate_id', $delegate->id)->get();
                    $delivery_stock = 0;
                    foreach($delegate_products as $product) {
                        $delivery_stock += $product->stock; 
                    }
                @endphp
                 <div class="h3 fw-700">
                    {{ $delivery_stock }} 
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bg-grad-8 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3 text-center">
                <i class="las la-comment-dollar la-4x"></i>
                <div class="opacity-50">{{ translate('Total Earnings') }}</div>
                @php 
                    $orders = \App\Models\Order::where('assign_delivery_boy', Auth::user()->id)
                        ->where('delivery_status', 'delivered')
                        ->get();
                    $total_earnings = 0;
                    foreach($orders as $order){
                        $total_earnings += $order->orderDetails->sum('price') + $order->orderDetails->sum('shipping_cost');
                    }
                @endphp
                <div class="h3 fw-700">
                    {{ $total_earnings }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bg-grad-9 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3 text-center">
                <i class="las la-comment-dollar la-4x"></i>
                <div class="opacity-50">@lang('delegate::delivery.system_earnings')</div>
                @php 
                    $personal_earnigns = $delegate->province->delegate_cost * ordersCount($delegate->user_id);
                    $system_earnigns = $total_earnings - $personal_earnigns;
                @endphp
                 <div class="h3 fw-700">
                    {{ $system_earnigns }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bg-grad-9 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3 text-center">
                <i class="las la-dollar-sign la-4x"></i>
                <div class="opacity-50">@lang('delegate::delivery.personal_earnings')</div>
                 <div class="h3 fw-700">
                    {{  $personal_earnigns }}
                </div>
            </div>
        </div>
    </div>

    @php 
        $today = date('d-m-Y');
        $week_orders = \Modules\Delegate\Entities\WeekOrders::where('delivery_man_id', $delegate->id)
            ->where('week_end', '>', $today)
            ->first();
    @endphp
    <div class="col-md-3">
        <div class="bg-grad-11 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3 text-center">
                <i class="las la-comment-dollar la-4x"></i>
                <div class="opacity-50">@lang('delegate::delivery.weekly_system_earnings')</div>
                 <div class="h3 fw-700">
                  {{  substr($week_orders->system_earnings, 0, -3) ?? 0 }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bg-grad-11 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3 text-center">
                <i class="las la-dollar-sign la-4x"></i>
                <div class="opacity-50">@lang('delegate::delivery.weekly_personal_earnings')</div>
                 <div class="h3 fw-700">
                   {{  substr($week_orders->personal_earnings, 0, -3) ?? 0 }}
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
