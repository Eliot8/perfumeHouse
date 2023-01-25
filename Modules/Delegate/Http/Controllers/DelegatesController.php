<?php

namespace Modules\Delegate\Http\Controllers;

use PDF;
use Exception;
use App\Models\User;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Delegate\Entities\Zone;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use App\Models\DeliveredOrdersEarnings;
use Modules\Delegate\Entities\Delegate;
use Illuminate\Contracts\Support\Renderable;
use Modules\Delegate\Http\Requests\StoreDelegateRequest;
use Modules\Delegate\Http\Requests\UpdateDelegateRequest;
use Session;

class DelegatesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $type = 'All';
        $delegates = Delegate::latest();
        
        $delegates = filterDelivery_man($request, $delegates);
        $delegates = $delegates->paginate(8);
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
        
        // CREATE USER
        $user = new User();
        $user->user_type = 'delivery_boy';
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->email_verified_at = date('Y-m-d H:i:s');
        $user->verification_code = null;
        $user->password = Hash::make($request->input('password'));
        $request->filled('address') ? $user->address = $request->input('address') : null;
        $user->save();
        
        $delegate->user_id = $user->id;
        $delegate->save();
        flash(Lang::get('delegate::delivery.delegate_added'))->success();
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
        try{
            $delegate =Delegate::findOrFail($id);
            $delegate->full_name = $request->input('name');
            $delegate->province_id = $request->input('province_id');
    
            if($request->input('email')){
                $delegate->email = $request->input('email');
                $user = User::findOrFail($delegate->user_id);
                $user->email = $request->input('email');
                $user->save();
            }
    
            if($request->input('reset_password') === 'true'){
                $delegate->password = Hash::make($request->input('password'));
                $user = User::findOrFail($delegate->user_id);
                $user->password = $delegate->password;
                $user->save();
            }
    
            $delegate->zones = $request->filled('zones') ?  json_encode($request->input('zones')) : null;
            $request->filled('phone_number') ? $delegate->phone_number = $request->input('phone_number') : null;
            $request->filled('address') ? $delegate->address = $request->input('address') : null;
    
            $delegate->save();
            flash(Lang::get('delegate::delivery.delegate_updated'))->success();
            return redirect()->route('delegates.index');
        } catch(Exception $e){
            flash($e->getMessage())->error();
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $delegate = Delegate::findOrFail($id);
        $delegate->user()->delete();
        $delegate->delete();

        flash(Lang::get('delegate::delivery.delegate_deleted'))->success();
        return redirect()->route('delegates.index');
    }

    public function getModalDeleteByAjax(Request $request){
        if(request()->ajax()) {
            return View::make('delegate::delegate.delete', ['id' => $request->id]);
        } else 
            return false;
    }
    
    public function getZone($province_id)
    {
        if(request()->ajax()){
            $zones = Zone::where('province_id', $province_id)->get();
            $html = '';
            foreach($zones as $zone) {
                $html .= '<optgroup label="' . $zone->name .'">';
                if($zone->neighborhoods->count() > 0){
                    foreach($zone->neighborhoods as $item) {
                        $html .= '<option value="' . $item->id . '">' . $item->name . '</option>';
                    }
                } else {
                    $html .= '<option value="' . $zone->id . '">' . $zone->name . '</option>';
                }
                $html .= '</optgroup>';
            }
            $data = [
                'options' => $html,
            ];
            return $data;
        } else 
            return false;
    }

    public function paymentRequest($delegate_id, $week_end) 
    {
        $ids = DeliveredOrdersEarnings::where(['delegate_id' => $delegate_id, 'status' => 'unpaid'])->pluck('id');
        if (count($ids) == 0) return response()->json(Lang::get("delegate::delivery.payments_empty"), 400);
       
        $week_orders = \Modules\Delegate\Entities\WeekOrders::where('delivery_man_id', $delegate_id)
        ->where('week_end', $week_end)
        ->first();
        $delivery_man = \Modules\Delegate\Entities\Delegate::find($week_orders->delivery_man_id);
        
        $delivery_man->all_earnings += $delivery_man->commission_earnings + $week_orders->personal_earnings;
        $delivery_man->commission_earnings = 0;
        $delivery_man->save();
        
        $week_orders->personal_earnings = 0;
        $week_orders->system_earnings = 0;
        $week_orders->save();
        
        return response()->json(['ids' => $ids, 'delegate_name' => $delivery_man->full_name, 'msg' => Lang::get('delegate::delivery.payment_request_success')], 200);
        
    }

    public function paymentRequestInvoice($ids, $delegate_name)
    {
        $items = DeliveredOrdersEarnings::whereIn('id', explode(',', $ids))->get();
        foreach($items as $item) {
            $item->status = 'paid';
            $item->save();
        }  

        $direction = 'rtl';
        $text_align = 'right';
        $not_text_align = 'left';
        $font_family = "'Baloo Bhaijaan 2','sans-serif'";

        return PDF::loadView('delegate::delegate.invoice', [
            'items' => $items,
            'delegate_name' => $delegate_name,
            'font_family' => $font_family,
            'direction' => $direction,
            'text_align' => $text_align,
            'not_text_align' => $not_text_align
        ], [], [])->download('payment_request_history.pdf');
    }
 }
