<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\City;
use App\Models\State;
use Auth;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'phone' => 'digits:10',
            'optional_phone' => 'digits:10',
        ], [
            'phone.digits' => 'يجب أن يتكون الهاتف من 10 أرقام.',
            'optional_phone.digits' => 'يجب أن يتكون الهاتف من 10 أرقام.',
        ]);

        $address = new Address;
        if($request->has('customer_id')){
            $address->user_id   = $request->customer_id;
        }
        else{
            $address->user_id   = Auth::user()->id;
        }
        $address->name          = $request->name;
        $address->address       = $request->address;

        $address->province_id   = $request->province;
        $address->zone_id       = $request->zone;
        $address->phone         = $request->phone;

        $address->optional_phone         = $request->input('optional_phone') ?? null;
        $address->save();

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['address_data'] = Address::findOrFail($id);
        $data['states'] = State::where('status', 1)->where('country_id', $data['address_data']->country_id)->get();
        $data['cities'] = City::where('status', 1)->where('state_id', $data['address_data']->state_id)->get();
        
        $returnHTML = view('frontend.partials.address_edit_modal', $data)->render();
        return response()->json(array('data' => $data, 'html'=>$returnHTML));
//        return ;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'phone' => 'digits:10',
            'optional_phone' => 'digits:10',
        ], [
            'phone.digits' => 'يجب أن يتكون الهاتف من 10 أرقام.',
            'optional_phone.digits' => 'يجب أن يتكون الهاتف من 10 أرقام.',
        ]);

        $address = Address::findOrFail($id);
        
        $address->name       = $request->name;
        $address->address       = $request->address;

        $address->province_id   = $request->province;
        $address->zone_id       = $request->zone;
        $address->phone         = $request->phone;

        $address->optional_phone         = $request->input('optional_phone') ?? null;
        
        $address->save();

        flash(translate('Address info updated successfully'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        if(!$address->set_default){
            $address->delete();
            return back();
        }
        flash(translate('Default address can not be deleted'))->warning();
        return back();
    }

    public function getStates(Request $request) {
        $states = State::where('status', 1)->where('country_id', $request->country_id)->get();
        $html = '<option value="">'.translate("Select State").'</option>';
        
        foreach ($states as $state) {
            $html .= '<option value="' . $state->id . '">' . $state->name . '</option>';
        }
        
        echo json_encode($html);
    }
    
    public function getCities(Request $request) {
        $cities = City::where('status', 1)->where('state_id', $request->state_id)->get();
        $html = '<option value="">'.translate("Select City").'</option>';
        
        foreach ($cities as $row) {
            $html .= '<option value="' . $row->id . '">' . $row->getTranslation('name') . '</option>';
        }
        
        echo json_encode($html);
    }

    public function set_default($id){
        foreach (Auth::user()->addresses as $key => $address) {
            $address->set_default = 0;
            $address->save();
        }
        $address = Address::findOrFail($id);
        $address->set_default = 1;
        $address->save();

        return back();
    }
}
