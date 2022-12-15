<?php

namespace Modules\Delegate\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Modules\Delegate\Entities\Province;
use Validator;

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $provinces = Province::orderBy('id', 'asc');
        $provinces = filterProvinces($request, $provinces);
        $provinces = $provinces->paginate(8);

        return view('delegate::provinces.index', compact('provinces'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('delegate::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validator  = Validator::make($request->all(), [
            'name' => ['required', 'min:3', 'max:50'],
            'delegate_cost' => ['required', 'numeric'],
            'shipping_type' => ['required'],
            'delegate_commission' => ['required', 'numeric', 'max:100', 'min:0'],
        ]);

        if ($validator->fails()) {
            flash(translate($validator->errors()->first()))->error();
            return back();
        }

        $province = new Province();
        $province->name = $request->input('name');
        $province->delegate_cost = $request->input('delegate_cost');
        $province->delegate_commission = $request->input('delegate_commission');

        if ($request->shipping_type == 'free') {
            $province->free_shipping = 1;
        }

        if ($request->shipping_type == 'cost') {
            $province->free_shipping = 0;
            $province->shipping_cost = $request->shipping_cost;
        }
        $province->save();

        flash(Lang::get('delegate::delivery.province_added'))->success();
        return back();
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('delegate::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $province = Province::findOrFail($id);
        return view('delegate::provinces.edit', compact('province'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $validator  = Validator::make($request->all(), [
            'name' => ['required', 'min:3', 'max:50'],
            'delegate_cost' => ['required', 'numeric'],
            'delegate_commission' => ['required', 'numeric', 'max:100', 'min:0'],
            'shipping_type' => ['required'],
        ]);

        if ($validator->fails()) {
            flash(translate($validator->errors()->first()))->error();
            return back();
        }

        $province = Province::findOrFail($id);
        $province->name = $request->input('name');
        $province->delegate_cost = $request->input('delegate_cost');
        $province->delegate_commission = $request->input('delegate_commission');
        
        if ($request->shipping_type == 'free') {
            $province->free_shipping = 1;
            $province->shipping_cost = 0;
        }

        if ($request->shipping_type == 'cost') {
            $province->free_shipping = 0;
            $province->shipping_cost = $request->shipping_cost;
        }
        $province->save();

        flash(Lang::get('delegate::delivery.province_updated'))->success();
        return redirect()->route('provinces.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $province = Province::findOrFail($id);
        $province->zones()->delete();
        $province->delete();

        flash(Lang::get('delegate::delivery.province_deleted'))->success();
        return back();
    }
}
