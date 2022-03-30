<?php

namespace Modules\Delegate\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Delegate\Entities\Zone;
use Validator;

class ZonesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $zones = Zone::paginate(8);
        return view('delegate::zones.index', compact('zones'));
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'min:3', 'max:50'],
            'province_id' => 'required',
        ]);

        if($validator->fails()) {
            flash($validator->errors()->first())->error();
            return back();
        }

        $zone = new Zone();
        $zone->name = $request->input('name');
        $zone->province_id = $request->input('province_id');
        $zone->save();

        flash('Zone has been created successfully')->success();
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $zone = Zone::findOrFail($id);
        return view('delegate::zones.edit', compact('zone'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        // $validator = Validator::make($request->all(), [
        //     'name' => ['required', 'min:3', 'max:50'],
        //     'province_id' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     flash($validator->errors()->first())->error();
        //     return back();
        // }

        $request->validate([
            'name' => ['required', 'min:3', 'max:50'],
            'province_id' => 'required',
        ]);

        $zone = Zone::findOrFail($id);
        $zone->name = $request->input('name');
        $zone->province_id = $request->input('province_id');
        $zone->save();

        flash('Zone has been updated successfully')->success();
        return redirect()->route('zones.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
