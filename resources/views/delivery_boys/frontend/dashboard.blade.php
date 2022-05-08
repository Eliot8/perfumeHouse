@extends('frontend.layouts.user_panel')
@section('extra-css')
<style>
    .bg-grad-1 {
        background-color: #eb4786;
        background-image: linear-gradient(315deg, #eb4786 0%, #b854a6 74%);
    }
    .bg-grad-5 {
        background-color: #8c64dd; 
        background-image: linear-gradient(315deg, #8c64dd 0%, #7764dd 74%);
    }
    .bg-grad-6 {
        background-color: #fdce4b; 
        /* background-image: linear-gradient(315deg, #fdce4b 0%, #977914 74%); */
        background-image: linear-gradient(315deg, #ecdc65 0%, #bd9e49 74%);
    }
    .bg-grad-7 {
        /* background-color: #767676;
        background-image: linear-gradient(315deg, #767676 0%, #282828 74%); */
        background-color: #4473b7; 
        background-image: linear-gradient(315deg, #4473b7 0%, #114781 74%);
    }
    .bg-grad-8 {
        background-color: #56bb78; 
        background-image: linear-gradient(315deg, #56bb78 0%, #4f996a 74%);
    }
    .bg-grad-9 {
        background-color: #00c896; 
        background-image: linear-gradient(315deg, #00c896 0%, #325e9f 74%);
    }
    .bg-grad-10 {
        background-color: #00c896; 
        background-image: linear-gradient(315deg, #00c896 0%, #325e9f 74%);
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
                <div class="opacity-50">{{ translate('System Earnings') }}</div>
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
        <div class="bg-grad-10 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3 text-center">
                <i class="las la-dollar-sign la-4x"></i>
                <div class="opacity-50">{{ translate('Personal Earnings') }}</div>
                 <div class="h3 fw-700">
                    {{  $personal_earnigns }}
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
