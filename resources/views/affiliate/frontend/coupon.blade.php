@extends('frontend.layouts.app')

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-start">
                @include('frontend.inc.user_side_nav')
                <div class="aiz-user-panel">
                    <div class="aiz-titlebar mt-2 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h1 class="h3">{{ translate('Affiliate Coupon') }}</h1>
                            </div>
                        </div>
                    </div>
                    <div class="row gutters-10">
                        {{-- <div class="col-md-4 mx-auto mb-3" >
                          <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                            <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                                <i class="las la-dollar-sign la-2x text-white"></i>
                            </span>
                            <div class="px-3 pt-3 pb-3">
                                <div class="h4 fw-700 text-center">{{ single_price(Auth::user()->affiliate_user->balance) }}</div>
                                <div class="opacity-50 text-center">{{ translate('Affiliate Balance') }}</div>
                            </div>
                          </div>
                        </div> --}}
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Coupon Information')}}</h5>
                        </div>
                          <div class="card-body">
                            @if(has_coupon(Auth::user()))
                            @php
                            
                                $coupon = Auth::user()->affiliate_user->coupon;
                                $min_buy = json_decode($coupon->details)->min_buy;
                                $max_discount = json_decode($coupon->details)->max_discount;
                            @endphp
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{ translate('Code') }}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" style="text-transform: uppercase;" placeholder="{{ translate('code') }}" value="{{ $coupon->code }}" disabled>
                                </div>
                            </div>



                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{ translate('Minimum Shopping') }}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="{{ translate('Minimum Shopping')}}" value="{{ $min_buy }}" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{ translate('Discount') }}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="{{ translate('Discount')}}" value="{{ $coupon->discount }}@if($coupon->discount_type == 'percent')%@endif" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{ translate('Maximum Discount Amount') }}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="{{ translate('Maximum Discount Amount')}}" value="{{ $max_discount }}" disabled>
                                </div>
                            </div>

                            @php
                            $start_date = date('m/d/Y', $coupon->start_date);
                            $end_date = date('m/d/Y', $coupon->end_date);
                            @endphp
                            <div class="form-group row">
                                <label class="col-sm-3 control-label">{{translate('Date')}}</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" value="{{ $start_date .' - '. $end_date }}" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 control-label">{{ translate('Commission') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="@lang('delegate::delivery.enter_commission')" value="{{ $coupon->commission }}" disabled>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-info mt-2 mb-0" role="alert">
                                {{ translate('You don\'t have a coupon yet') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection




@section('script')

@endsection
