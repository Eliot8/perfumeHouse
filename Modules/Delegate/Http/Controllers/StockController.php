<?php

namespace Modules\Delegate\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
        // dd($stocks);
        return view('delegate::stock.index', compact('delegates'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('delegate::stock.create');
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
            flash('Product already in Stock')->error();
            return back();
        }

        $stock = new Stock();
        $stock->product_id = $request->input('product');
        $stock->delegate_id = $request->input('delegate');
        $stock->stock = $request->input('quantity');
        $stock->save();

        flash('Stock has been added successfully')->success();
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
        $stock->stock = $request->input('quantity');
        $stock->save();

        flash('Stock has been updated successfully')->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        Stock::findOrFail($id)->delete();

        flash(translate('Stock has been deleted successfully'))->success();
        return back();
    }


    public function manage($delegate_id)
    {
        $products = Stock::where('delegate_id', $delegate_id)->paginate(8);
        return view('delegate::stock.manage', compact('products', 'delegate_id'));
    }
}
