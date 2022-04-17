<?php

use App\Models\Order;
use Modules\Delegate\Entities\Province;
use Modules\Delegate\Entities\Stock;

function getStockLevel($delegate_id){
    $delegate_products = Stock::where('delegate_id', $delegate_id)->get();
    $stock = 0;
    foreach($delegate_products as $product) {
        $stock += $product->stock; 
    }
    if($stock == 0) return 'empty'; 
    if($stock <= 10) return 'low'; 
    return 'high';
}

function getPercentageEarnings($user_id) {
    $earnings = 0;
    $orders = Order::select('assign_delivery_boy', 'percentage_earnings')->where('assign_delivery_boy', $user_id)->get();
    foreach($orders as $order) {
        $earnings += $order->percentage_earnings != null ? $order->percentage_earnings : 0;
    }
    return $earnings;
}

function getDeliveryBoyEarnings($user_id) {
    $earnings = 0;
    $orders = Order::select('assign_delivery_boy', 'delegate_earnings')->where('assign_delivery_boy', $user_id)->get();
    foreach ($orders as $order) {
        $earnings += $order->delegate_earnings != null ? $order->delegate_earnings : 0;
    }
    return $earnings;
}

function getDeliveryBoyEarnings2($province_id, $user_id) {
    $province = Province::find($province_id);
    return $province->profit_ratio * ordersCount($user_id);
}

function ordersCount($user_id){
    $orders = Order::where('assign_delivery_boy', $user_id)->get();
    return $orders->count();
}