<?php

namespace Modules\Delegate\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Modules\Delegate\Entities\Delegate;
use Modules\Delegate\Http\Requests\StoreDelegateRequest;
use Modules\Delegate\Http\Requests\UpdateDelegateRequest;

class DelegatesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $delegates = Delegate::paginate(8);
        $type = 'All';
        return view('delegate::delegate.index', compact('delegates', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('delegate::delegate.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(StoreDelegateRequest $request)
    {
        $request->validated();
        
        $delegate = new Delegate();
        $delegate->full_name = $request->input('name');
        $delegate->province_id = $request->input('province_id');
        $delegate->email = $request->input('email');
        $delegate->password = Hash::make($request->input('password'));

        $request->filled('phone_number') ? $delegate->phone_number = $request->input('phone_number') : null;
        $request->filled('address') ? $delegate->address = $request->input('address') : null;
        $request->filled('zones') ? $delegate->zones = json_encode($request->input('zones')) : null;
        $delegate->save();

        flash(translate('Delivery man has been created successfully'))->success();
        return redirect()->route('delegates.index');
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
        $delegate = Delegate::findOrFail($id);
        return view('delegate::delegate.edit', compact('delegate'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(UpdateDelegateRequest $request, $id)
    {
        $request->validated();

        if($request->input('reset_password') === 'true'){
            $request->validate([
                'password' => ['required', 'confirmed', 'min:4'],
            ]);
        }

        $delegate =Delegate::findOrFail($id);
        $delegate->full_name = $request->input('name');
        $delegate->province_id = $request->input('province_id');
        $delegate->email = $request->input('email');
        $delegate->password = Hash::make($request->input('password'));

        $request->filled('phone_number') ? $delegate->phone_number = $request->input('phone_number') : null;
        $request->filled('address') ? $delegate->address = $request->input('address') : null;
        $request->filled('zones') ? $delegate->zones = json_encode($request->input('zones')) : null;
        $delegate->save();

        flash(translate('Delivery man has been updated successfully'))->success();
        return redirect()->route('delegates.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        Delegate::findOrFail($id)->delete();

        flash(translate('Delivery man has been deleted successfully'))->success();
        return redirect()->route('delegates.index');
    }

    public function getModalDeleteByAjax(Request $request){
        if(request()->ajax()) {
            return View::make('delegate::delegate.delete', ['id' => $request->id]);
        } else 
            return false;
    }
    
    public function getZone($id){
        if(request()->ajax()){
            $zones = \DB::table('zones')->where('province_id', $id)->pluck('name', 'id');
            $html = '';
            $i = 0;
            foreach($zones as $key => $value) {
                $html .= '<option value="' . $key . '">' . $value . '</option>';
            }
            $data = [
                'options' => $html,
            ];
            return $data;
        } else 
            return false;
    }
}
