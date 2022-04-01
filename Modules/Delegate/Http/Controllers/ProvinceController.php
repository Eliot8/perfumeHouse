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
    public function index()
    {
        $provinces =Province::paginate(8);
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
        ]);
        
        if($validator->fails()){
            flash(translate($validator->errors()->first()))->error();
            return back();
        }
        
        $province = new Province();
        $province->name = $request->input('name');
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
        return view('delegate::edit');
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
        ]);

        if ($validator->fails()) {
            flash(translate($validator->errors()->first()))->error();
            return back();
        }

        $province = Province::findOrFail($id);
        $province->name = $request->input('name');
        $province->save();

        flash(Lang::get('delegate::delivery.province_updated'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $province = Province::findOrFail($id);
        $province->zones->

        flash(Lang::get('delegate::delivery.province_deleted'))->success();
        return back();
    }
}
