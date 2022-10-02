<?php

use App\Http\Controllers\ClubPointController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\CommissionController;
use App\Models\Currency;
use App\Models\BusinessSetting;
use App\Models\ProductStock;
use App\Models\Address;
use App\Models\CustomerPackage;
use App\Models\Upload;
use App\Models\Translation;
use App\Models\City;
use App\Utility\CategoryUtility;
use App\Models\Wallet;
use App\Models\CombinedOrder;
use App\Models\User;
use App\Models\Addon;
use App\Models\AffiliateUser;
use App\Models\Product;
use App\Models\Shop;
use App\Utility\SendSMSUtility;
use App\Utility\NotificationUtility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Delegate\Entities\Delegate;
use Modules\Delegate\Entities\Stock;
use Modules\Delegate\Entities\WeekOrders;

//sensSMS function for OTP
if (!function_exists('sendSMS')) {
    function sendSMS($to, $from, $text, $template_id)
    {
        return SendSMSUtility::sendSMS($to, $from, $text, $template_id);
    }
}

//highlights the selected navigation on admin panel
if (!function_exists('areActiveRoutes')) {
    function areActiveRoutes(array $routes, $output = "active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
    }
}

//highlights the selected navigation on frontend
if (!function_exists('areActiveRoutesHome')) {
    function areActiveRoutesHome(array $routes, $output = "active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
    }
}

//highlights the selected navigation on frontend
if (!function_exists('default_language')) {
    function default_language()
    {
        return env("DEFAULT_LANGUAGE");
    }
}

/**
 * Save JSON File
 * @return Response
 */
if (!function_exists('convert_to_usd')) {
    function convert_to_usd($amount)
    {
        $currency = Currency::find(get_setting('system_default_currency'));
        return (floatval($amount) / floatval($currency->exchange_rate)) * Currency::where('code', 'USD')->first()->exchange_rate;
    }
}

if (!function_exists('convert_to_kes')) {
    function convert_to_kes($amount)
    {
        $currency = Currency::find(get_setting('system_default_currency'));
        return (floatval($amount) / floatval($currency->exchange_rate)) * Currency::where('code', 'KES')->first()->exchange_rate;
    }
}

//filter products based on vendor activation system
if (!function_exists('filter_products')) {
    function filter_products($products)
    {
        $verified_sellers = verified_sellers_id();
        if (get_setting('vendor_system_activation') == 1) {
            return $products->where('approved', '1')->where('published', '1')->where('auction_product', 0)->orderBy('created_at', 'desc')->where(function ($p) use ($verified_sellers) {
                $p->where('added_by', 'admin')->orWhere(function ($q) use ($verified_sellers) {
                    $q->whereIn('user_id', $verified_sellers);
                });
            });
        } else {
            return $products->where('published', '1')->where('auction_product', 0)->where('added_by', 'admin');
        }
    }
}

//cache products based on category
if (!function_exists('get_cached_products')) {
    function get_cached_products($category_id = null)
    {
        $products = \App\Models\Product::where('published', 1)->where('approved', '1')->where('auction_product', 0);
        $verified_sellers = verified_sellers_id();
        if (get_setting('vendor_system_activation') == 1) {
            $products = $products->where(function ($p) use ($verified_sellers) {
                $p->where('added_by', 'admin')->orWhere(function ($q) use ($verified_sellers) {
                    $q->whereIn('user_id', $verified_sellers);
                });
            });
        } else {
            $products = $products->where('added_by', 'admin');
        }

        if ($category_id != null) {
            return Cache::remember('products-category-' . $category_id, 86400, function () use ($category_id, $products) {
                $category_ids = CategoryUtility::children_ids($category_id);
                $category_ids[] = $category_id;
                return $products->whereIn('category_id', $category_ids)->latest()->take(12)->get();
            });
        } else {
            return Cache::remember('products', 86400, function () use ($products) {
                return $products->latest()->take(12)->get();
            });
        }
    }
}

if (!function_exists('verified_sellers_id')) {
    function verified_sellers_id()
    {
        return Cache::rememberForever('verified_sellers_id', function () {
            return App\Models\Seller::where('verification_status', 1)->pluck('user_id')->toArray();
        });
    }
}

if (!function_exists('get_system_default_currency')) {
    function get_system_default_currency()
    {
        return Cache::remember('system_default_currency', 86400, function () {
            return Currency::findOrFail(get_setting('system_default_currency'));
        });
    }
}

//converts currency to home default currency
if (!function_exists('convert_price')) {
    function convert_price($price)
    {
        if (Session::has('currency_code') && (Session::get('currency_code') != get_system_default_currency()->code)) {
            $price = floatval($price) / floatval(get_system_default_currency()->exchange_rate);
            $price = floatval($price) * floatval(Session::get('currency_exchange_rate'));
        }
        return $price;
    }
}

//gets currency symbol
if (!function_exists('currency_symbol')) {
    function currency_symbol()
    {
        if (Session::has('currency_symbol')) {
            return Session::get('currency_symbol');
        }
        return get_system_default_currency()->symbol;
    }
}

//formats currency
if (!function_exists('format_price')) {
    function format_price($price)
    {
        if (get_setting('decimal_separator') == 1) {
            $fomated_price = number_format($price, get_setting('no_of_decimals'));
        } else {
            $fomated_price = number_format($price, get_setting('no_of_decimals'), ',', ' ');
        }

        if (get_setting('symbol_format') == 1) {
            return currency_symbol() . $fomated_price;
        } else if (get_setting('symbol_format') == 3) {
            return currency_symbol() . ' ' . $fomated_price;
        } else if (get_setting('symbol_format') == 4) {
            return $fomated_price . ' ' . currency_symbol();
        }
        return $fomated_price . currency_symbol();
    }
}

//formats price to home default price with convertion
if (!function_exists('single_price')) {
    function single_price($price)
    {
        return format_price(convert_price($price));
    }
}

if (!function_exists('discount_in_percentage')) {
    function discount_in_percentage($product)
    {
        try {
            $base = home_base_price($product, false);
            $reduced = home_discounted_base_price($product, false);
            $discount = $base - $reduced;
            $dp = ($discount * 100) / $base;
            return round($dp);
        } catch (Exception $e) {
        }
        return 0;
    }
}

//Shows Price on page based on low to high
if (!function_exists('home_price')) {
    function home_price($product, $formatted = true)
    {
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;

        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }

        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $lowest_price += ($lowest_price * $product_tax->tax) / 100;
                $highest_price += ($highest_price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $lowest_price += $product_tax->tax;
                $highest_price += $product_tax->tax;
            }
        }

        if ($formatted) {
            if ($lowest_price == $highest_price) {
                return format_price(convert_price($lowest_price));
            } else {
                return format_price(convert_price($lowest_price)) . ' - ' . format_price(convert_price($highest_price));
            }
        } else {
            return $lowest_price . ' - ' . $highest_price;
        }
    }
}

//Shows Price on page based on low to high with discount
if (!function_exists('home_discounted_price')) {
    function home_discounted_price($product, $formatted = true)
    {
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;

        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }

        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $lowest_price -= ($lowest_price * $product->discount) / 100;
                $highest_price -= ($highest_price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $lowest_price -= $product->discount;
                $highest_price -= $product->discount;
            }
        }

        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $lowest_price += ($lowest_price * $product_tax->tax) / 100;
                $highest_price += ($highest_price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $lowest_price += $product_tax->tax;
                $highest_price += $product_tax->tax;
            }
        }

        if ($formatted) {
            if ($lowest_price == $highest_price) {
                return format_price(convert_price($lowest_price));
            } else {
                return format_price(convert_price($lowest_price)) . ' - ' . format_price(convert_price($highest_price));
            }
        } else {
            return $lowest_price . ' - ' . $highest_price;
        }
    }
}

//Shows Base Price
if (!function_exists('home_base_price_by_stock_id')) {
    function home_base_price_by_stock_id($id)
    {
        $product_stock = ProductStock::findOrFail($id);
        $price = $product_stock->price;
        $tax = 0;

        foreach ($product_stock->product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }
        $price += $tax;
        return format_price(convert_price($price));
    }
}
if (!function_exists('home_base_price')) {
    function home_base_price($product, $formatted = true)
    {
        $price = $product->unit_price;
        $tax = 0;

        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }
        $price += $tax;
        return $formatted ? format_price(convert_price($price)) : $price;
    }
}

//Shows Base Price with discount
if (!function_exists('home_discounted_base_price_by_stock_id')) {
    function home_discounted_base_price_by_stock_id($id)
    {
        $product_stock = ProductStock::findOrFail($id);
        $product = $product_stock->product;
        $price = $product_stock->price;
        $tax = 0;

        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }
        $price += $tax;

        return format_price(convert_price($price));
    }
}

//Shows Base Price with discount
if (!function_exists('home_discounted_base_price')) {
    function home_discounted_base_price($product, $formatted = true)
    {
        $price = $product->unit_price;
        $tax = 0;

        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }
        $price += $tax;

        return $formatted ? format_price(convert_price($price)) : $price;
    }
}

if (!function_exists('renderStarRating')) {
    function renderStarRating($rating, $maxRating = 5)
    {
        $fullStar = "<i class = 'las la-star active'></i>";
        $halfStar = "<i class = 'las la-star half'></i>";
        $emptyStar = "<i class = 'las la-star'></i>";
        $rating = $rating <= $maxRating ? $rating : $maxRating;

        $fullStarCount = (int)$rating;
        $halfStarCount = ceil($rating) - $fullStarCount;
        $emptyStarCount = $maxRating - $fullStarCount - $halfStarCount;

        $html = str_repeat($fullStar, $fullStarCount);
        $html .= str_repeat($halfStar, $halfStarCount);
        $html .= str_repeat($emptyStar, $emptyStarCount);
        echo $html;
    }
}

function translate($key, $lang = null, $addslashes = false)
{
    if ($lang == null) {
        $lang = App::getLocale();
    }

    $lang_key = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', strtolower($key)));

    $translations_en = Cache::rememberForever('translations-en', function () {
        return Translation::where('lang', 'en')->pluck('lang_value', 'lang_key')->toArray();
    });

    if (!isset($translations_en[$lang_key])) {
        $translation_def = new Translation;
        $translation_def->lang = 'en';
        $translation_def->lang_key = $lang_key;
        $translation_def->lang_value = str_replace(array("\r", "\n", "\r\n"), "", $key);
        $translation_def->save();
        Cache::forget('translations-en');
    }

    // return user session lang
    $translation_locale = Cache::rememberForever("translations-{$lang}", function () use ($lang) {
        return Translation::where('lang', $lang)->pluck('lang_value', 'lang_key')->toArray();
    });
    if (isset($translation_locale[$lang_key])) {
        return trim($translation_locale[$lang_key]);
    }

    // return default lang if session lang not found
    $translations_default = Cache::rememberForever('translations-' . env('DEFAULT_LANGUAGE', 'en'), function () {
        return Translation::where('lang', env('DEFAULT_LANGUAGE', 'en'))->pluck('lang_value', 'lang_key')->toArray();
    });
    if (isset($translations_default[$lang_key])) {
        return trim($translations_default[$lang_key]);
    }

    // fallback to en lang
    if (!isset($translations_en[$lang_key])) {
        return trim($key);
    }
    return trim($translations_en[$lang_key]);
}

function remove_invalid_charcaters($str)
{
    $str = str_ireplace(array("\\"), '', $str);
    return str_ireplace(array('"'), '\"', $str);
}

function getShippingCost($carts, $index)
{
    $admin_products = array();
    $seller_products = array();

    $cartItem = $carts[$index];
    $product = Product::find($cartItem['product_id']);

    if ($product->digital == 1) {
        return 0;
    }

    foreach ($carts as $key => $cart_item) {
        $item_product = Product::find($cart_item['product_id']);
        if ($item_product->added_by == 'admin') {
            array_push($admin_products, $cart_item['product_id']);
        } else {
            $product_ids = array();
            if (isset($seller_products[$item_product->user_id])) {
                $product_ids = $seller_products[$item_product->user_id];
            }
            array_push($product_ids, $cart_item['product_id']);
            $seller_products[$item_product->user_id] = $product_ids;
        }
    }

    if (get_setting('shipping_type') == 'flat_rate') {
        return get_setting('flat_rate_shipping_cost') / count($carts);
    } elseif (get_setting('shipping_type') == 'seller_wise_shipping') {
        if ($product->added_by == 'admin') {
            return get_setting('shipping_cost_admin') / count($admin_products);
        } else {
            return Shop::where('user_id', $product->user_id)->first()->shipping_cost / count($seller_products[$product->user_id]);
        }
    } elseif (get_setting('shipping_type') == 'area_wise_shipping') {
        $shipping_info = Address::where('id', $carts[0]['address_id'])->first();
        $city = City::where('id', $shipping_info->city_id)->first();
        if ($city != null) {
            if ($product->added_by == 'admin') {
                return $city->cost / count($admin_products);
            } else {
                return $city->cost / count($seller_products[$product->user_id]);
            }
        }
        return 0;
    } else {
        if ($product->is_quantity_multiplied && get_setting('shipping_type') == 'product_wise_shipping') {
            return  $product->shipping_cost * $cartItem['quantity'];
        }
        return $product->shipping_cost;
    }
}

function timezones()
{
    return Timezones::timezonesToArray();
}

if (!function_exists('app_timezone')) {
    function app_timezone()
    {
        return config('app.timezone');
    }
}

if (!function_exists('api_asset')) {
    function api_asset($id)
    {
        if (($asset = \App\Models\Upload::find($id)) != null) {
            return $asset->file_name;
        }
        return "";
    }
}

//return file uploaded via uploader
if (!function_exists('uploaded_asset')) {
    function uploaded_asset($id)
    {
        if (($asset = \App\Models\Upload::find($id)) != null) {
            return $asset->external_link == null ? my_asset($asset->file_name) : $asset->external_link;
        }
        return null;
    }
}

if (!function_exists('my_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function my_asset($path, $secure = null)
    {
        if (env('FILESYSTEM_DRIVER') == 's3') {
            return Storage::disk('s3')->url($path);
        } else {
            // return app('url')->asset('public/' . $path, $secure);
            return app('url')->asset('/' . $path, $secure);
        }
    }
}

if (!function_exists('static_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function static_asset($path, $secure = null)
    {
        // return app('url')->asset('public/' . $path, $secure);
        return app('url')->asset('/' . $path, $secure);
    }
}


// if (!function_exists('isHttps')) {
//     function isHttps()
//     {
//         return !empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS']);
//     }
// }

if (!function_exists('getBaseURL')) {
    function getBaseURL()
    {
        $root = '//' . $_SERVER['HTTP_HOST'];
        $root .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

        return $root;
    }
}


if (!function_exists('getFileBaseURL')) {
    function getFileBaseURL()
    {
        if (env('FILESYSTEM_DRIVER') == 's3') {
            return env('AWS_URL') . '/';
        } else {
            // return getBaseURL() . 'public/';
            return getBaseURL() . '/';
        }
    }
}


if (!function_exists('isUnique')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function isUnique($email)
    {
        $user = \App\Models\User::where('email', $email)->first();

        if ($user == null) {
            return '1'; // $user = null means we did not get any match with the email provided by the user inside the database
        } else {
            return '0';
        }
    }
}

if (!function_exists('get_setting')) {
    function get_setting($key, $default = null, $lang = false)
    {
        $settings = Cache::remember('business_settings', 86400, function () {
            return BusinessSetting::all();
        }); 

        if ($lang == false) {
            $setting = $settings->where('type', $key)->first();
        } else {
            $setting = $settings->where('type', $key)->where('lang', $lang)->first();
            $setting = !$setting ? $settings->where('type', $key)->first() : $setting;
        }
        return $setting == null ? $default : $setting->value;
    }
}

function hex2rgba($color, $opacity = false)
{
    return Colorcodeconverter::convertHexToRgba($color, $opacity);
}

if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        if (Auth::check() && (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff')) {
            return true;
        }
        return false;
    }
}

if (!function_exists('isSeller')) {
    function isSeller()
    {
        if (Auth::check() && Auth::user()->user_type == 'seller') {
            return true;
        }
        return false;
    }
}

if (!function_exists('isCustomer')) {
    function isCustomer()
    {
        if (Auth::check() && Auth::user()->user_type == 'customer') {
            return true;
        }
        return false;
    }
}

if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

// duplicates m$ excel's ceiling function
if (!function_exists('ceiling')) {
    function ceiling($number, $significance = 1)
    {
        return (is_numeric($number) && is_numeric($significance)) ? (ceil($number / $significance) * $significance) : false;
    }
}

if (!function_exists('get_images')) {
    function get_images($given_ids, $with_trashed = false)
    {
        if (is_array($given_ids)) {
            $ids = $given_ids;
        } elseif ($given_ids == null) {
            $ids = [];
        } else {
            $ids = explode(",", $given_ids);
        }


        return $with_trashed
            ? Upload::withTrashed()->whereIn('id', $ids)->get()
            : Upload::whereIn('id', $ids)->get();
    }
}

//for api
if (!function_exists('get_images_path')) {
    function get_images_path($given_ids, $with_trashed = false)
    {
        $paths = [];
        $images = get_images($given_ids, $with_trashed);
        if (!$images->isEmpty()) {
            foreach ($images as $image) {
                $paths[] = !is_null($image) ? $image->file_name : "";
            }
        }

        return $paths;
    }
}

//for api
if (!function_exists('checkout_done')) {
    function checkout_done($combined_order_id, $payment)
    {
        $combined_order = CombinedOrder::find($combined_order_id);

        foreach ($combined_order->orders as $key => $order) {
            $order->payment_status = 'paid';
            $order->payment_details = $payment;
            $order->save();

            try {
                NotificationUtility::sendOrderPlacedNotification($order);
                calculateCommissionAffilationClubPoint($order);
            } catch (\Exception $e) {
            }
        }
    }
}

//for api
if (!function_exists('wallet_payment_done')) {
    function wallet_payment_done($user_id, $amount, $payment_method, $payment_details)
    {
        $user = \App\Models\User::find($user_id);
        $user->balance = $user->balance + $amount;
        $user->save();

        $wallet = new Wallet;
        $wallet->user_id = $user->id;
        $wallet->amount = $amount;
        $wallet->payment_method = $payment_method;
        $wallet->payment_details = $payment_details;
        $wallet->save();
    }
}

if (!function_exists('purchase_payment_done')) {
    function purchase_payment_done($user_id, $package_id)
    {
        $user = User::findOrFail($user_id);
        $user->customer_package_id = $package_id;
        $customer_package = CustomerPackage::findOrFail($package_id);
        $user->remaining_uploads += $customer_package->product_upload;
        $user->save();

        return 'success';
    }
}

//Commission Calculation
if (!function_exists('calculateCommissionAffilationClubPoint')) {
    function calculateCommissionAffilationClubPoint($order)
    {
        (new CommissionController)->calculateCommission($order);

        if (addon_is_activated('affiliate_system')) {
            (new AffiliateController)->processAffiliatePoints($order);
        }

        if (addon_is_activated('club_point')) {
            if ($order->user != null) {
                (new ClubPointController)->processClubPoints($order);
            }
        }

        $order->commission_calculated = 1;
        $order->save();
    }
}

// Addon Activation Check
if (!function_exists('addon_is_activated')) {
    function addon_is_activated($identifier, $default = null)
    {
        $addons = Cache::remember('addons', 86400, function () {
            return Addon::all();
        });

        $activation = $addons->where('unique_identifier', $identifier)->where('activated', 1)->first();
        return $activation == null ? false : true;
    }
}

if(!function_exists('has_coupon')){
    function has_coupon($user) {
        if($user->affiliate_user && $user->affiliate_user->coupon){
            return true;
        }
        return false;
    }
}

if(!function_exists('coupon_has_expired')){
    function coupon_has_expired($end_date) {
        if($end_date <= strtotime(date('m/d/Y'))){
            return true;
        }
        return false;
    }
}

if(!function_exists('get_discounted_price')){
    function get_discounted_price($price) {
        $coupon = get_valid_coupon();

        if(!$coupon) return false;

        $discount_type = $coupon->discount_type;
        $discount = $coupon->discount;
        $price = str_replace(',', '', $price);
        
        if($discount_type == 'percent'){
            $discounted_price = $price - ($price * ($discount / 100));
        } else {
            $discounted_price = $price - $discount;
        }
        return $discounted_price;
    }
}

if(!function_exists('get_valid_coupon')){
    function get_valid_coupon(){
        $coupons = Auth::user()->affiliate_user->coupon;
        $coupons = $coupons->where('end_date', '>=', strtotime(date('m/d/Y')));

        if($coupons->count() == 0){
            return false;
        }
        
        return getHighestCommission($coupons);
    }
}

if (!function_exists('get_coupons')) {
    function get_coupons()
    {
        $coupons = Auth::user()->affiliate_user->coupon;
        $coupons = $coupons->where('end_date', '>=', strtotime(date('m/d/Y')));

        if ($coupons->count() == 0) {
            return false;
        }

        return array_values(reset($coupons));
    }
}


if(!function_exists('getHighestCommission')) {
    function getHighestCommission($coupons) {
        $coupon = [
            'id' => null,
            'commission' => 0,
        ];

        foreach ($coupons as $coupon) {
            if ($coupon->commission > $coupon['commission']) {
                $coupon = [
                    'id' => $coupon->id,
                    'commission' => $coupon->commission,
                ];
            }
        }
        $coupon = $coupons->where('id', $coupon['id']);
        return array_values(reset($coupon))[0];
    }
}

if(!function_exists('getDeliveryStatus')){
    function getDeliveryStatus(){
        return collect([
            'pending'       => 'Pending',
            'confirmed'     => 'Confirmed',
            'picked_up'     => 'Picked Up',
            'on_the_way'    => 'On The Way',
            'delivered'     => 'Delivered',
            'cancelled'     => 'Cancel',
        ]);
    }
}

if(!function_exists('getPaymentStatus')){
    function getPaymentStatus(){
        return collect([
            'paid'       => 'Paid',
            'unpaid'     => 'Un-Paid',
        ]);
    }
}

if(!function_exists('filterOrders')){
    function fitlerOrders(Request $request, $orders){
        if ($request->has('customer')) {
            $orders = $orders->where('user_id', $request->get('customer'));
        }
        if ($request->has('delivery_status')) {
            $orders = $orders->where('delivery_status', $request->get('delivery_status'));
        }
        if ($request->has('payment_status')) {
            $orders = $orders->where('payment_status', $request->get('payment_status'));
        }
        if ($request->has('delivery_man')) {
            $delivery_man = Delegate::find($request->get('delivery_man'));
            $orders = $orders->where('assign_delivery_boy', $delivery_man->user_id);
        }
        if ($request->get('date')) {
            $date = $request->get('date');

            $start_date = date('Y-m-d', strtotime(explode(" to ", $date)[0]));
            $end_date = date('Y-m-d', strtotime(explode(" to ", $date)[1]));

            $orders = $orders->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date);
        }
        if ($request->has('order_code')) {
            $orders = $orders->where('code', 'like', '%' . $request->get('order_code') . '%');
        }
        if ($request->has('affiliate_user')) {
            $orders = $orders->where('user_id', $request->get('affiliate_user'));
        }
        if ($request->has('cancel_request')) {
            $orders = $orders->where('cancel_request', 1);
        }
        return $orders;
    }
}

if(!function_exists('filterProducts')){
    function filterProducts(Request $request, $products){
        if ($request->has('user_id')) {
            $products = $products->where('user_id', $request->get('user_id'));
        }
        if ($request->search != null) {
            $sort_search = $request->search;
            $products = $products
                ->where('name', 'like', '%' . $sort_search . '%')
                ->orWhereHas('stocks', function ($q) use ($sort_search) {
                    $q->where('sku', 'like', '%' . $sort_search . '%');
                });
        }
        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
        }
  
        if ($request->has('category')) {
            $products = $products->where('category_id', $request->get('category'));
        }

        if ($request->has('brand')) {
            $products = $products->where('brand_id', $request->get('brand'));
        }

        if ($request->has('published')) {
            $products = $products->where('published', 1);
        }
        if ($request->has('featured')) {
            $products = $products->where('featured', 1);
        }
        if ($request->has('todays_deal')) {
            $products = $products->where('todays_deal', 1);
        }

        return $products;
    }
}

if(!function_exists('filterDelivery_man')){
    function filterDelivery_man(Request $request, $delegates){
        if ($request->has('province')) {
            $delegates = $delegates->where('province_id', $request->get('province'));
        }
        if ($request->search != null) {
            $sort_search = $request->search;
            $delegates = $delegates
                ->where('full_name', 'like', '%' . $sort_search . '%')
                ->orWhere('phone_number', 'like', '%' . $sort_search . '%')
                ->orWhere('email', 'like', '%' . $sort_search . '%');
        }
       
        return $delegates;
    }
}

if(!function_exists('filterStock')){
    function filterStock(Request $request, $delegates){
        if ($request->has('delegate')) {
            $delegates = $delegates->where('id', $request->get('delegate'));
        }
        if ($request->has('province')) {
            $delegates = $delegates->where('province_id', $request->get('province'));
        }
        if ($request->has('stock')) {

            $arr = [];
            foreach($delegates->get() as $delegate){
                $stock_status = getStockLevel($delegate->id);
                if($request->get('stock') == $stock_status){
                   array_push($arr, $delegate->id);
                }
            }
            $delegates = $delegates->whereIn('id', $arr);
        }
       
        return $delegates;
    }
}

if(!function_exists('filterProvinces')){
    function filterProvinces(Request $request, $provinces){
        if ($request->has('delivery_man')) {
            $arr = [];
            foreach($provinces->get() as $province){
                $delegates = $province->delegates->where('id', $request->get('delivery_man'));
                if($delegates->count() > 0){
                    array_push($arr, $province->id);
                }
            }
            $provinces = $provinces->whereIn('id', $arr);
        }
        if ($request->has('province')) {
            $provinces = $provinces->where('id', $request->get('province'));
        }
        if ($request->has('shipping_cost')) {
            if($request->get('shipping_cost') == 'free'){
                $provinces = $provinces->where('shipping_cost', null);
            } else {
                $provinces = $provinces->where('shipping_cost','!=', null);
            }
        }
       
        return $provinces;
    }
}

if(!function_exists('filterCoupon')){
    function filterCoupon(Request $request, $coupons){
        
        if ($request->has('affiliate_user')) {
            $coupons = $coupons->where('affiliate_user_id', $request->get('affiliate_user'));
        }

        if ($request->has('commission_type')) {
            $coupons = $coupons->where('commission_type', $request->get('commission_type'));
        }

        if ($request->has('coupon_validity')) {
            if($request->input('coupon_validity') == 'expired'){
                $coupons = $coupons->where('end_date', '<=', strtotime(date('m/d/Y')));
            }

            if($request->input('coupon_validity') == 'valid') {
                $coupons = $coupons->where('end_date', '>=', strtotime(date('m/d/Y')));
            }
        }

        if ($request->get('start_date')) {
            $start_date = $request->get('start_date');
            $coupons = $coupons->whereDate('start_date', '>=', date('Y-m-d', strtotime(explode(" to ", $start_date)[0])))->whereDate('start_date', '<=', date('Y-m-d', strtotime(explode(" to ", $start_date)[1])));
        }

        // if ($request->get('end_date')) {
        //     $end_date = $request->get('end_date');
        //     $coupons = $coupons->where('end_date', '>=', date('Y-m-d', strtotime(explode(" to ", $end_date)[0])))->where('end_date', '<=', date('Y-m-d', strtotime(explode(" to ", $end_date)[1])));
        // }
        if ($request->get('search')) {
            $coupons = $coupons->where('code', 'like', '%'.$request->get('search').'%')->orWhere('type', 'like', '%' . $request->get('search') . '%');
           
        }
       
        return $coupons;
    }
}

if(!function_exists('filterAffiliateUsers')){
    function filterAffiliateUsers(Request $request, $affiliate_users){
        
        if ($request->has('affiliate_user')) {
            $affiliate_users = $affiliate_users->where('id', $request->get('affiliate_user'));
        }
        if ($request->has('approval')) {
            $affiliate_users = $affiliate_users->where('status', $request->get('approval') == true ? 1 : 0);
        }
        return $affiliate_users;
    }
}

if(!function_exists('filterRefferalUsers')){
    function filterRefferalUsers(Request $request, $refferal_users){
        
        if ($request->has('affiliate_user')) {
            $user = AffiliateUser::find($request->get('affiliate_user'))->user;
            // dd($user->id);
            $refferal_users = $refferal_users->where('referred_by', $user->id);
        }
        // if ($request->has('search') && $request->get('search') != null) {
        //     // dd($request->get('search'));
        //     $refferal_users = $refferal_users->where('name', 'like', '%'.$request->get('search').'%')
        //     ->orWhere('email', 'like', '%' . $request->get('search') . '%')
        //     ->orWhere('phone', 'like', '%' . $request->get('search') . '%')->where('referred_by', '!=' , null);

        //     // $refferal_users = $refferal_users->where('referred_by', '!=' , null);
        // }
        return $refferal_users;
    }
}

if(!function_exists('filterAffiliateWithdrawRequests')){
    function filterAffiliateWithdrawRequests(Request $request, $affiliate_withdraw_requests){
        
        if ($request->has('affiliate_user')) {
            $user = AffiliateUser::find($request->get('affiliate_user'))->user;
            $affiliate_withdraw_requests = $affiliate_withdraw_requests->where('user_id', $user->id);
        }
        if ($request->has('status')) {
            $affiliate_withdraw_requests = $affiliate_withdraw_requests->where('status', $request->get('status') == 'approved' ? 1 : 0);
        }
        return $affiliate_withdraw_requests;
    }
}


if (!function_exists('checkNewPriceProduct')) {
    function checkNewPriceProduct(Request $request, $id, $coupon)
    {

        $product_price = Product::find($id)->unit_price;

        if ($coupon->commission_type == 'percent') {
            $max_price = $product_price - ($product_price * ($coupon->commission / 100));
        } else {
            $max_price = $product_price - ($coupon->commission / 100);
        }

        if ($max_price > $request->get('unit_price')) {
            return false;
        }

        return true;
    }
}

if(!function_exists('insertIntoWeekOrders')){
    function insertIntoWeekOrders($delivery_man_id, $system_earnings, $personal_earnings){
        
        $day = date('w');
        $week_start = date('d-m-Y', strtotime('-' . $day . ' days'));
        $week_end = date('d-m-Y', strtotime('+' . (6 - $day) . ' days'));
        $today = date('d-m-Y');

        // dd(Carbon::createFromFormat('d-m-Y', $week_start), $week_end);
        $week = WeekOrders::where('delivery_man_id', $delivery_man_id)->where('week_end', '>', $today)->first();
        if($week){
            $week->system_earnings += $system_earnings;
            $week->personal_earnings += $personal_earnings;
        } else {
            $week = new WeekOrders();
            $week->delivery_man_id = $delivery_man_id;
            $week->week_start = Carbon::createFromFormat('d-m-Y', $week_start);
            $week->week_end = Carbon::createFromFormat('d-m-Y', $week_end);
            $week->system_earnings = $system_earnings;
            $week->personal_earnings = $personal_earnings;
        }
        $week->save();

        return true;
    }

}

if (!function_exists('updateOfficialProductStock')) {
    function updateOfficialProductStock($product_id, $variant) {
        if($variant) {
            $product_stock = ProductStock::where('product_id', $product_id)->where('variant', $variant)->get();
            $total_delegates_stock = Stock::where('product_id', $product_id)->where('variation', $variant)->sum('stock');
        } else {
            $product_stock = ProductStock::where('product_id', $product_id)->get();
            $total_delegates_stock = Stock::where('product_id', $product_id)->sum('stock');
        }
    
        $product_stock->each(function($p) use ($total_delegates_stock) {
            $p->qty = $total_delegates_stock;
            $p->save();
        });
    }
}
