<?php

namespace Modules\Delegate\Http\Controllers;

use App\Models\AttributeValue;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Lang;
use Modules\Delegate\Entities\Delegate;
use Modules\Delegate\Entities\Stock;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $delegates = Delegate::latest();

        $delegates = filterStock($request, $delegates);
        
        $delegates = $delegates->paginate(8);
        
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
        
        $product_stock = ProductStock::where('product_id', $request->input('product'))->first();
        if($product_stock->variant){
            $variation= '';
    
            if($request->get('color')){
                $variation .= Color::where('code', $request->get('color'))->first()->name . '-';
            }
    
            if($request->get('attributes')){
                $variation .= preg_replace("/\s+/", "", implode("-", $request->get('attributes')));
            }

            $delivery_stock = Stock::where([
                'delegate_id' => $request->input('delegate'),
                'product_id' => $request->input('product'),
                'variation' => $variation,
                ])->first();
        }else{
            $delivery_stock = Stock::where([
                'delegate_id' => $request->input('delegate'),
                'product_id' => $request->input('product'),
                ])->first();
        }

        if($delivery_stock){
            flash(Lang::get('delegate::delivery.stock_exist'))->error();
            return back();
        }


        // DECREASE PRODUCT STOCK
        $response = $this->decreaseProductStock($request->product, $request->quantity);
        
        if($response->getStatusCode() !== 200){
           flash(json_decode($response->getContent())->message)->error();
           return back();
        }

        // CREATE NEW INSTANCE OF STOCK
        $stock = new Stock();
        $stock->product_id = $request->input('product');
        $stock->delegate_id = $request->input('delegate');
        $stock->stock = $request->input('quantity');
        
        if($request->get('color')){
            $stock->color = $request->get('color');
        }

        if($request->get('attributes')){
            $stock->attributes = json_encode($request->get('attributes'));
        }

        if(!empty($variation)){
            $stock->variation = $variation;
        }
        
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

        if(!$product_stock){
            return response(['message' => Lang::get('delegate::delivery.no_stock_exist')], 500);
        }

        if ($product_stock->qty - $quantity < 0) {;
            return response(['message' => Lang::get('delegate::delivery.no_enough_stock')], 500);
        }

        $product_stock->qty -= $quantity;
        $product_stock->save();
        return response(200);
    }

    protected function increaseProductStock($product_id, $quantity) {
        $product_stock = ProductStock::where('product_id', $product_id)->first();

        if (!$product_stock) {
            return response(['message' => Lang::get('delegate::delivery.no_stock_exist')], 500);
        }

        if ($product_stock->qty - $quantity < 0) {
            return response(['message' => Lang::get('delegate::delivery.no_enough_stock')], 500);
        }

        $product_stock->qty += $quantity;
        $product_stock->save();
        return response(200);
    }

    public function getColors($id, Request $request) {
        if($request->ajax()){
            $product_colors = Product::select('colors')->where('id', $id)->first()->colors;
            $colors = json_decode($product_colors);

            if(count($colors) == 0){
                return 0;
            }

            $html = '<option value="" disabled>' . translate("Select Color") . '</option>';
            foreach($colors as $code){
                $color = Color::select('id', 'name', 'code')->where('code', $code)->first();
                $html .= '<option  value="' .  $color->code . '" data-content="<span><span class=' ;
                $html .= "'size-15px d-inline-block mr-2 rounded border' style='background:" .  $color->code;
                $html .= "'></span><span>" . $color->name;
                $html .= '</span></span>"></option>';
            }
            return response()->json(json_encode($html));
        } else 
            return false;
    }

    public function getAttributes($id, Request $request) {
        if($request->ajax()){
            $product_attributes = Product::select('attributes')->where('id', $id)->first()->attributes;
            $attributes = json_decode($product_attributes);

            if(count($attributes) == 0) return 0;

            $html = '<option value="" disabled>' . translate("Select Attribute") . '</option>';
            foreach($attributes as $attribute){
                $attribute_values = AttributeValue::select('id', 'attribute_id', 'value')->where('attribute_id', $attribute)->get();
                $html .= '<optgroup label="' . $attribute_values[0]->attribute->name . '">';
                foreach($attribute_values as $attribute_value){
                    $html .= '<option>' . $attribute_value->value . '</option>';
                }
                $html .= '</optgroup>';
            }
            return response()->json(json_encode($html));
        } else 
            return false;
    }

}
