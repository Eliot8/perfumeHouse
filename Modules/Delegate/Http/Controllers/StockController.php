<?php

namespace Modules\Delegate\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Lang;
use Modules\Delegate\Entities\Delegate;
use Modules\Delegate\Entities\Stock;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $delegates = Delegate::paginate(8);
        return view('delegate::stock.index', compact('delegates'));
    }
    
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'product' => 'required',
            'quantity' => ['required', 'numeric'],
            'delegate' => 'required',
        ]);
        
        $already_exist = Stock::where(['delegate_id' => $request->input('delegate'), 'product_id' => $request->input('product')])->first();
        if($already_exist){
            flash(Lang::get('delegate::delivery.stock_exist'))->error();
            return back();
        }
        // DECREASE PRODUCT STOCK
        $this->decreaseProductStock($request->product, $request->quantity);

        $stock = new Stock();
        $stock->product_id = $request->input('product');
        $stock->delegate_id = $request->input('delegate');
        $stock->stock = $request->input('quantity');
        $stock->save();

        flash(Lang::get('delegate::delivery.stock_added'))->success();
        return back();
    }
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => ['required', 'numeric'],
        ]);

        $stock = Stock::findOrFail($id);

        // DECREASE & INCREASE PRODUCT STOCK 
        $this->increaseProductStock($stock->product_id, $stock->stock);
        $this->decreaseProductStock($stock->product_id, $request->input('quantity'));

        $stock->stock = $request->input('quantity');
        $stock->save();

        flash(Lang::get('delegate::delivery.stock_updated'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $delivery_stock = Stock::findOrFail($id);

        // INCREASE PRODUCT STOCK
        $this->increaseProductStock($delivery_stock->product_id, $delivery_stock->stock);

        // DELETE DELIVERY STOCK
        $delivery_stock->delete();
        flash(Lang::get('delegate::delivery.stock_deleted'))->success();
        return back();
    }


    public function manage($delegate_id)
    {
        $products = Stock::where('delegate_id', $delegate_id)->paginate(8);
        return view('delegate::stock.manage', compact('products', 'delegate_id'));
    }

    protected function decreaseProductStock($product_id, $quantity) {
        $product_stock = ProductStock::where('product_id', $product_id)->first();
        if ($product_stock->qty - $quantity < 0) {
            flash('stock error')->error();
            return back();
        }
        $product_stock->qty -= $quantity;
        $product_stock->save();
    }

    protected function increaseProductStock($product_id, $quantity) {
        $product_stock = ProductStock::where('product_id', $product_id)->first();
        if ($product_stock->qty - $quantity < 0) {
            flash('stock error')->error();
            return back();
        }
        $product_stock->qty += $quantity;
        $product_stock->save();
    }

}
