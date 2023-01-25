<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Mail;
use Session;
use App\Models\Cart;
use App\Models\User;
use App\Models\Color;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Address;
use App\Models\Product;
use App\OtpConfiguration;
use App\Models\CouponUsage;
use App\Models\OrderDetail;
use App\Models\SmsTemplate;
use App\Utility\SmsUtility;
use App\Models\ProductStock;
use CoreComponentRepository;
use Illuminate\Http\Request;
use App\Models\AffiliateUser;
use App\Models\CombinedOrder;
use App\Models\BusinessSetting;
use App\Mail\InvoiceEmailManager;
use App\Models\CommissionHistory;
use App\Utility\NotificationUtility;
use Illuminate\Support\Facades\Lang;
use Modules\Delegate\Entities\Stock;
use App\Models\AffiliateProductPrice;
use App\Models\DeliveredOrdersEarnings;
use Modules\Delegate\Entities\Delegate;
use Modules\Delegate\Entities\Province;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\ClubPointController;
use App\Http\Controllers\OTPVerificationController;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource to seller.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $orders = DB::table('orders')
            ->orderBy('id', 'desc')
            //->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('seller_id', Auth::user()->id)
            ->select('orders.id')
            ->distinct();

        if ($request->payment_status != null) {
            $orders = $orders->where('payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }

        $orders = $orders->paginate(15);

        foreach ($orders as $key => $value) {
            $order = \App\Models\Order::find($value->id);
            $order->viewed = 1;
            $order->save();
        }

        return view('frontend.user.seller.orders', compact('orders', 'payment_status', 'delivery_status', 'sort_search'));
    }

    // All Orders
    public function all_orders(Request $request)
    {
        
        CoreComponentRepository::instantiateShopRepository();

        $date = $request->date;
        $sort_search = null;
        $delivery_status = null;


        $orders = Order::orderBy('id', 'desc');

        $orders = fitlerOrders($request, $orders);
        
        $orders = $orders->paginate(15);
        return view('backend.sales.all_orders.index', compact('orders', 'sort_search', 'delivery_status', 'date'));
    }

    public function all_orders_show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order_shipping_address = json_decode($order->shipping_address);
        // $delivery_boys = User::where('city', $order_shipping_address->city)
        $delivery_boys = User::where('user_type', 'delivery_boy')
            ->get();

        return view('backend.sales.all_orders.show', compact('order', 'delivery_boys'));
    }

    // Inhouse Orders
    public function admin_orders(Request $request)
    {
        CoreComponentRepository::instantiateShopRepository();

        $date = $request->date;
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $admin_user_id = User::where('user_type', 'admin')->first()->id;
        $orders = Order::orderBy('id', 'desc')
                        ->where('seller_id', $admin_user_id);

        if ($request->payment_type != null) {
            $orders = $orders->where('payment_status', $request->payment_type);
            $payment_status = $request->payment_type;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($date != null) {
            $orders = $orders->whereDate('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }

        $orders = $orders->paginate(15);
        return view('backend.sales.inhouse_orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'admin_user_id', 'date'));
    }

    public function show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order_shipping_address = json_decode($order->shipping_address);
        $delivery_boys = User::where('city', $order_shipping_address->city)
            ->where('user_type', 'delivery_boy')
            ->get();

        $order->viewed = 1;
        $order->save();
        return view('backend.sales.inhouse_orders.show', compact('order', 'delivery_boys'));
    }

    // Seller Orders
    public function seller_orders(Request $request)
    {
        CoreComponentRepository::instantiateShopRepository();

        $date = $request->date;
        $seller_id = $request->seller_id;
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $admin_user_id = User::where('user_type', 'admin')->first()->id;
        $orders = Order::orderBy('code', 'desc')
            ->where('orders.seller_id', '!=', $admin_user_id);

        if ($request->payment_type != null) {
            $orders = $orders->where('payment_status', $request->payment_type);
            $payment_status = $request->payment_type;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($date != null) {
            $orders = $orders->whereDate('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }
        if ($seller_id) {
            $orders = $orders->where('seller_id', $seller_id);
        }

        $orders = $orders->paginate(15);
        return view('backend.sales.seller_orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'admin_user_id', 'seller_id', 'date'));
    }

    public function seller_orders_show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order->viewed = 1;
        $order->save();
        return view('backend.sales.seller_orders.show', compact('order'));
    }


    // Pickup point orders
    public function pickup_point_order_index(Request $request)
    {
        $date = $request->date;
        $sort_search = null;
        $orders = Order::query();
        if (Auth::user()->user_type == 'staff' && Auth::user()->staff->pick_up_point != null) {
            $orders->where('shipping_type', 'pickup_point')
                    ->where('pickup_point_id', Auth::user()->staff->pick_up_point->id)
                    ->orderBy('code', 'desc');

            if ($request->has('search')) {
                $sort_search = $request->search;
                $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
            }
            if ($date != null) {
                $orders = $orders->whereDate('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
            }

            $orders = $orders->paginate(15);

            return view('backend.sales.pickup_point_orders.index', compact('orders', 'sort_search', 'date'));
        } else {
            $orders->where('shipping_type', 'pickup_point')->orderBy('code', 'desc');

            if ($request->has('search')) {
                $sort_search = $request->search;
                $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
            }
            if ($date != null) {
                $orders = $orders->whereDate('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
            }

            $orders = $orders->paginate(15);

            return view('backend.sales.pickup_point_orders.index', compact('orders', 'sort_search', 'date'));
        }
    }

    public function pickup_point_order_sales_show($id)
    {
        if (Auth::user()->user_type == 'staff') {
            $order = Order::findOrFail(decrypt($id));
            $order_shipping_address = json_decode($order->shipping_address);
            $delivery_boys = User::where('city', $order_shipping_address->city)
                ->where('user_type', 'delivery_boy')
                ->get();

            return view('backend.sales.pickup_point_orders.show', compact('order', 'delivery_boys'));
        } else {
            $order = Order::findOrFail(decrypt($id));
            $order_shipping_address = json_decode($order->shipping_address);
            $delivery_boys = User::where('city', $order_shipping_address->city)
                ->where('user_type', 'delivery_boy')
                ->get();

            return view('backend.sales.pickup_point_orders.show', compact('order', 'delivery_boys'));
        }
    }

    /**
     * Display a single sale to admin.
     *
     * @return \Illuminate\Http\Response
     */


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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $carts = Cart::where('user_id', Auth::user()->id)->get();
        
        if ($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->warning();
            return redirect()->route('home');
        }

        $address = Address::where('id', $carts[0]['address_id'])->first();
        $province = \Modules\Delegate\Entities\Province::select('id', 'name', 'delegate_commission')->find($address->province_id);
        $shippingAddress = [];
        if ($address != null) {
            $shippingAddress['name']        = Auth::user()->name;
            $shippingAddress['email']       = Auth::user()->email;
            $shippingAddress['address']     = $address->address;
            
            $shippingAddress['province']     = $province->name;
            $shippingAddress['zone']         = $address->zone_id == null ? '' : \Modules\Delegate\Entities\Neighborhood::find($address->zone_id)->name;

            $shippingAddress['phone']       = $address->phone;
            $shippingAddress['optional_phone']       = $address->optional_phone;
            if ($address->latitude || $address->longitude) {
                $shippingAddress['lat_lang'] = $address->latitude . ',' . $address->longitude;
            }
        }

        $combined_order = new CombinedOrder;
        $combined_order->user_id = Auth::user()->id;
        $combined_order->shipping_address = json_encode($shippingAddress);
        $combined_order->save();

        $seller_products = array();
        foreach ($carts as $cartItem){
            $product_ids = array();
            $product = Product::find($cartItem['product_id']);
            if(isset($seller_products[$product->user_id])){
                $product_ids = $seller_products[$product->user_id];
            }
            array_push($product_ids, $cartItem);
            $seller_products[$product->user_id] = $product_ids;
        }

        foreach ($seller_products as $seller_product) {
            $order = new Order;
            $order->combined_order_id = $combined_order->id;
            $order->user_id = Auth::user()->id;
            $order->shipping_address = $combined_order->shipping_address;
            $order->shipping_type = $carts[0]['shipping_type'];
            if ($carts[0]['shipping_type'] == 'pickup_point') {
                $order->pickup_point_id = $cartItem['pickup_point'];
            }
            $order->payment_type = $request->payment_option;
            $order->delivery_viewed = '0';
            $order->payment_status_viewed = '0';
            $order->code = date('Ymd-His') . rand(10, 99);
            $order->date = strtotime('now');
            //
            $order->province_id = $address->province_id;
            $order->zone_id = $address->zone_id;
            //
            $order->save();

            $subtotal = 0;
            $tax = 0;
            $shipping = 0;
            $coupon_discount = 0;
            
            $discount = 0;
            $over_price = 0;
            $commission = 0;
            
            //Order Details Storing
            foreach ($seller_product as $cartItem) {
                $product = Product::find($cartItem['product_id']);
                
                if (Auth::check() && Auth::user()->affiliate_user != null && Auth::user()->affiliate_user->status) {
                    if($cartItem['affiliate_price_type'] == 'discount') {
                        $discount = $cartItem['affiliate_price'];
                    } else {
                        $over_price = $cartItem['affiliate_price'];
                    }
                }

                $subtotal += $cartItem['price'] * $cartItem['quantity'];
                $tax += $cartItem['tax'] * $cartItem['quantity'];
                $coupon_discount += $cartItem['discount'];
                $product_variation = $cartItem['variation'];
                $product_stock = $product->stocks->where('variant', $product_variation)->first();

                if ($product->digital != 1 && $cartItem['quantity'] > $product_stock->qty) {
                    flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                    $order->delete();
                    return redirect()->route('cart')->send();
                }

                $order_detail = new OrderDetail;
                $order_detail->order_id = $order->id;
                $order_detail->seller_id = $product->user_id;
                $order_detail->product_id = $product->id;
                $order_detail->variation = $product_variation;
                $order_detail->commission = $cartItem['commission'];
                $order_detail->affiliate_price_type = $cartItem['affiliate_price_type'];
                $order_detail->affiliate_price = $cartItem['affiliate_price'];
                $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
                $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
                $order_detail->shipping_type = $cartItem['shipping_type'];
                $order_detail->product_referral_code = $cartItem['product_referral_code'];
                $order_detail->shipping_cost = $address->province->shipping_cost ?? 0;
                
                $shipping += $order_detail->shipping_cost;
                
                $order_detail->quantity = $cartItem['quantity'];
                $order_detail->save();

                $product->num_of_sale += $cartItem['quantity'];
                $product->save();

                $order->seller_id = $product->user_id;

                if ($product->added_by == 'seller' && $product->user->seller != null){
                    $seller = $product->user->seller;
                    $seller->num_of_sale += $cartItem['quantity'];
                    $seller->save();
                }
                
                $coupon_code = $cartItem['coupon_code'];
                $commission += $cartItem['commission'];
            }

            $user = Auth::user();
            if(Auth::check() && Auth::user()->affiliate_user != null && Auth::user()->affiliate_user->status) {

                // SET COUPON ID
                $coupon = Coupon::where('code', $coupon_code)->first();
                if($coupon) {
                    $order->coupon_id = $coupon->id;
    
                    $affiliateController = new AffiliateController;
                    $affiliateController->processAffiliateStats($user->id, 0, $order_detail->quantity, 0, 0);
    
                    
                    // CALCUL COMMISSION
                    if ($coupon->commission_type == 'percent') {
                        $calculate_comission = $subtotal * ($coupon->commission / 100);
                    } else {
                        $calculate_comission = $coupon->commission;
                    }
    
                    $order->commission_calculated = $calculate_comission;
                    $calculate_comission = $calculate_comission - $discount + $over_price;

                    $user->affiliate_user->balance_pending += $calculate_comission;
                    $user->affiliate_user->save();
                    
    
                    $coupon_usage = new CouponUsage;
                    $coupon_usage->user_id = $user->id;
                    $coupon_usage->coupon_id = $coupon->id;
                    $coupon_usage->order_id = $order->id;
                    $coupon_usage->commission = isset($calculate_comission) ? $calculate_comission : 0;
                    $coupon_usage->save();
                }
            } 

            $shipping_cost = $address->province->shipping_cost ?? 0;
            $order->administrative_expenses = $subtotal * ($province->delegate_commission / 100);
            $order->grand_total = $subtotal + $tax + $shipping_cost + $order->administrative_expenses;
           
            if ($seller_product[0]->coupon_code != null) {
                $order->coupon_discount = $coupon_discount;
                $order->grand_total -= $coupon_discount;
                $coupon =  Coupon::where('code', $seller_product[0]->coupon_code)->first();

                if ($coupon->commission_type == 'percent') {
                    $calculate_comission = $subtotal * ($coupon->commission / 100);
                } else {
                    $calculate_comission = $coupon->commission;
                }

                $coupon_usage = new CouponUsage;
                $coupon_usage->user_id = Auth::user()->id;
                $coupon_usage->coupon_id = $coupon->id;
                $coupon_usage->order_id = $order->id;
                $coupon_usage->commission = $calculate_comission;
                $coupon_usage->save();

                // SET COUPON ID
                $order->coupon_id = $coupon->id;
            }

            $order->grand_total = $order->grand_total + $over_price - $discount;
            $combined_order->grand_total += $order->grand_total;

            $order->save();
        }

        $combined_order->save();

        $request->session()->put('combined_order_id', $combined_order->id);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        if ($order != null) {
            foreach ($order->orderDetails as $key => $orderDetail) {
                // try {

                //     $product_stock = ProductStock::where('product_id', $orderDetail->product_id)->where('variant', $orderDetail->variation)->first();
                //     if ($product_stock != null) {
                //         $product_stock->qty += $orderDetail->quantity;
                //         $product_stock->save();
                //     }

                // } catch (\Exception $e) {

                // }

                // DECREASE AFFILIATE BALANCE
                
                $orderDetail->delete();
            }
            
            
                $coupon_usage = CouponUsage::where('order_id', $id)->first();
                
                if($coupon_usage){
                    $affiliate_user = AffiliateUser::where('user_id', $coupon_usage->user_id)->first();
                    $affiliate_user->balance_pending -= $coupon_usage->commission;
                    $affiliate_user->save();
                }
            
            $order->delete();
            flash(translate('Order has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }

    public function bulk_order_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $order_id) {
                $this->destroy($order_id);
            }
        }

        return 1;
    }

    public function bulk_order_mark_as_confirmed(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $order_id) {
                $order = Order::find($order_id);
                if($order->delivery_status == 'pending'){
                    $request->merge(['status' => 'confirmed', 'order_id' => $order_id]);
                    $result = $this->update_delivery_status($request);
                    // dd($result->getData()->status, $result->getData('message'));
                    if ($result->getData()->status === 400) {
                        return response()->json($result->getData()->message, 400);
                    }
                } 
            }
        }
        return 1;
    }

    public function bulk_order_mark_as_paid(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $order_id) {
                $order = Order::find($order_id);
                if($order->payment_status == 'unpaid'){
                    $order->payment_status = 'paid';
                    $order->save();
                } 
            }
        }
        return 1;
    }

    public function order_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->save();
        return view('frontend.user.seller.order_details_seller', compact('order'));
    }

    public function update_delivery_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        if ($request->status == 'delivered') {
            
            # DELIVERY MAN CODE
            $delegate = Delegate::select('id', 'commission_earnings')->where('user_id', $order->assign_delivery_boy)->first();
            if(!$delegate){
                return response()->json([
                    'status' => 400,
                    'message' => __('delegate::delivery.no_delegate_selected'),
                ]);
            }
            
            # MANAGE DELIVERY MAN STOCK
            foreach ($order->orderDetails as $orderDetail) {
                if ($orderDetail->product->variant_product) {
                    $delivery_stock = Stock::where([
                        'delegate_id'   => $delegate->id,
                        'product_id'    => $orderDetail->product_id,
                        'variation'    => $orderDetail->variation,
                    ])->first();
                } else {
                    $delivery_stock = Stock::where([
                        'delegate_id'   => $delegate->id,
                        'product_id'    => $orderDetail->product_id,
                    ])->first();
                }
                
                if (!$delivery_stock) {
                    return false;
                }

                if ($delivery_stock->stock - $orderDetail->quantity < 0) {
                    $delivery_stock->stock = 0;
                } else {
                    $delivery_stock->stock -= $orderDetail->quantity;
                }

                $delivery_stock->save();
                updateOfficialProductStock($orderDetail->product_id, $delivery_stock->variation);
            }

            # WEEK BALANCE
            if($order->province->delegate_cost == null) {
                return response()->json([
                    'status' => 401,
                    'message' => Lang::get('delegate::delivery.delivery_man_cost_unset'),
                ]);
            }

            # TRANSFORM COMMISSION FROM AFFILIATE BALANCE PENDING TO AFFILIATE BALANCE
            if($order->coupon_id != null){
                $affiliate_user = Coupon::find($order->coupon_id)->affiliate_user;
                $coupon_usage = CouponUsage::where('order_id', $order->id)->first();

                $affiliate_user->balance_pending    -= $coupon_usage->commission;
                $affiliate_user->balance            += $coupon_usage->commission;

                if($affiliate_user->balance_pending < 0 ) {
                    $affiliate_user->balance_pending = 0;
                }
                
                $affiliate_user->save();
            }

            # COMMISSION EARNINGS FOR DELIVERY MAN
            $commission_earning_from_order = $order->orderDetails->sum('price') * ($order->province->delegate_commission / 100);
            $delegate->commission_earnings += $commission_earning_from_order;
            $delegate->save();



            insertIntoWeekOrders($delegate->id, $order->grand_total, $order->province->delegate_cost, $commission_earning_from_order);

            # INSERT INTO DELIVERED_ORDERS_EARNINGS
            $delivered_order = new DeliveredOrdersEarnings();
            $delivered_order->delegate_id = $delegate->id;
            $delivered_order->order_id = $order->id;
            $delivered_order->system_earnings = $order->grand_total - ($order->province->delegate_cost + $commission_earning_from_order); // system = total - (personal + commission)
            $delivered_order->personal_earnings = $order->province->delegate_cost;
            $delivered_order->commission = $commission_earning_from_order;
            $delivered_order->status = 'unpaid';
            $delivered_order->save();
        }

        $order->delivery_viewed = '0';
        $order->delivery_status = $request->status;

        // **********************
        if($request->status == 'confirmed'){
            //  GET ASSIGNED DELIVERY MAN
            $delegates = Delegate::where('province_id', $order->province_id)->get();
            $delivery_man = assigned_delivery_man($delegates, $order->zone_id);

            // IF NO DELIVERY MAN FOUND IN THE PROVINCE
            if(!$delivery_man){
                return response()->json([
                    'status' => 400,
                    'message' => __('delegate::delivery.no_delegate_found'),
                ], 400);
            }
            
            // CHECH IF STOCK IS NOT EMPTY
            if(!check_delivey_man_stock($order, $delivery_man->id)) {
                return response()->json([
                    'status' => 400,
                    'message' => trans('delegate::delivery.stock_error', ['delegate' => $delivery_man->full_name]),
                ], 400);
            }
            // ASSIGNED DELIVERY MAN
            $order->assign_delivery_boy = $delivery_man->user_id;

            $order->delivery_status = $request->status;
        }
        $order->save();
        
        if ($request->status == 'cancelled' && $order->payment_type == 'wallet') {
            $user = User::where('id', $order->user_id)->first();
            $user->balance += $order->grand_total;
            $user->save();
            
            // DEDUCTION OF COMMISSION 
            if ($order->coupon_id != null) {
                $affiliate_user = Coupon::find($order->coupon_id)->affiliate_user;
                $coupon_usage = CouponUsage::where('order_id', $order->id)->first();
                $affiliate_user->balance_pending    -= $coupon_usage->commission;
                $affiliate_user->save();
                $coupon_usage->delete();
            }
        }

        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();

                if ($request->status == 'cancelled') {
                    $variant = $orderDetail->variation;
                    if ($orderDetail->variation == null) {
                        $variant = '';
                    }

                    // $product_stock = ProductStock::where('product_id', $orderDetail->product_id)
                    //     ->where('variant', $variant)
                    //     ->first();

                    // if ($product_stock != null) {
                    //     $product_stock->qty += $orderDetail->quantity;
                    //     $product_stock->save();
                    // }

                }
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {

                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();

                if ($request->status == 'cancelled') {
                    $variant = $orderDetail->variation;
                    if ($orderDetail->variation == null) {
                        $variant = '';
                    }

                    $product_stock = ProductStock::where('product_id', $orderDetail->product_id)
                        ->where('variant', $variant)
                        ->first();

                    if ($product_stock != null) {
                        $product_stock->qty += $orderDetail->quantity;
                        $product_stock->save();
                    }
                    // DEDUCTION OF COMMISSION 
                    if ($order->coupon_id != null) {
                        $affiliate_user = Coupon::find($order->coupon_id)->affiliate_user;
                        $coupon_usage = CouponUsage::where('order_id', $order->id)->first();
                        $affiliate_user->balance_pending    -= $coupon_usage->commission;
                        if($affiliate_user->balance_pending < 0){
                            $affiliate_user->balance_pending = 0;
                        }
                        $affiliate_user->save();
                    }
                }

                if (addon_is_activated('affiliate_system')) {
                    if (($request->status == 'delivered' || $request->status == 'cancelled') &&
                        $orderDetail->product_referral_code) {

                        $no_of_delivered = 0;
                        $no_of_canceled = 0;

                        if ($request->status == 'delivered') {
                            $no_of_delivered = $orderDetail->quantity;
                        }
                        if ($request->status == 'cancelled') {
                            $no_of_canceled = $orderDetail->quantity;
                        }

                        $referred_by_user = User::where('referral_code', $orderDetail->product_referral_code)->first();

                        $affiliateController = new AffiliateController;
                        $affiliateController->processAffiliateStats($referred_by_user->id, 0, 0, $no_of_delivered, $no_of_canceled);
                    }
                }
            }
        }
        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'delivery_status_change')->first()->status == 1) {
            try {
                SmsUtility::delivery_status_change(json_decode($order->shipping_address)->phone, $order);
            } catch (\Exception $e) {

            }
        }

        //sends Notifications to user
        NotificationUtility::sendNotification($order, $request->status);
        if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order updated !";
            $status = str_replace("_", "", $order->delivery_status);
            $request->text = " Your order {$order->code} has been {$status}";

            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            NotificationUtility::sendFirebaseNotification($request);
        }


        if (addon_is_activated('delivery_boy')) {
            if (Auth::user()->user_type == 'delivery_boy') {
                $deliveryBoyController = new DeliveryBoyController;
                $deliveryBoyController->store_delivery_history($order);
            }
        } 
        // return 1;
        return response()->json([
            'status' => 200,
            'message' => translate('Delivery status has been updated'),
        ]);
    }

   public function update_tracking_code(Request $request) {
        $order = Order::findOrFail($request->order_id);
        $order->tracking_code = $request->tracking_code;
        $order->save();

        return 1;
   }

    public function update_payment_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->payment_status_viewed = '0';
        $order->save();

        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        }

        $status = 'paid';
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($orderDetail->payment_status != 'paid') {
                $status = 'unpaid';
            }
        }
        $order->payment_status = $status;
        $order->save();


        if ($order->payment_status == 'paid' && $order->commission_calculated == 0) {
            calculateCommissionAffilationClubPoint($order);
        }

        //sends Notifications to user
        NotificationUtility::sendNotification($order, $request->status);
        if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order updated !";
            $status = str_replace("_", "", $order->payment_status);
            $request->text = " Your order {$order->code} has been {$status}";

            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            NotificationUtility::sendFirebaseNotification($request);
        }


        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'payment_status_change')->first()->status == 1) {
            try {
                SmsUtility::payment_status_change(json_decode($order->shipping_address)->phone, $order);
            } catch (\Exception $e) {

            }
        }
        return 1;
    }

    public function assign_delivery_boy(Request $request)
    {
        if (addon_is_activated('delivery_boy')) {

            $order = Order::findOrFail($request->order_id);
            $order->assign_delivery_boy = $request->delivery_boy;
            $order->delivery_history_date = date("Y-m-d H:i:s");
            $order->save();

            $delivery_history = \App\Models\DeliveryHistory::where('order_id', $order->id)
                ->where('delivery_status', $order->delivery_status)
                ->first();

            if (empty($delivery_history)) {
                $delivery_history = new \App\Models\DeliveryHistory;

                $delivery_history->order_id = $order->id;
                $delivery_history->delivery_status = $order->delivery_status;
                $delivery_history->payment_type = $order->payment_type;
            }
            $delivery_history->delivery_boy_id = $request->delivery_boy;

            $delivery_history->save();

            if (env('MAIL_USERNAME') != null && get_setting('delivery_boy_mail_notification') == '1') {
                $array['view'] = 'emails.invoice';
                $array['subject'] = translate('You are assigned to delivery an order. Order code') . ' - ' . $order->code;
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['order'] = $order;

                try {
                    Mail::to($order->delivery_boy->email)->queue(new InvoiceEmailManager($array));
                } catch (\Exception $e) {

                }
            }

            if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'assign_delivery_boy')->first()->status == 1) {
                try {
                    SmsUtility::assign_delivery_boy($order->delivery_boy->phone, $order->code);
                } catch (\Exception $e) {

                }
            }
        }

        return 1;
    }

}
