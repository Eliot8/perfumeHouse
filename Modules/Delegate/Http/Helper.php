<?php

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