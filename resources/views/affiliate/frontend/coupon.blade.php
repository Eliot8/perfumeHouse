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
                                <table class="table aiz-table mb-0">
                                    <thead>
                                    <tr>
                                        <th>{{ translate('Code')}}</th>
                                        <th>{{ translate('Minimum Shopping')}}</th>
                                        <th>{{ translate('Discount')}}</th>
                                        <th>{{ translate('Maximum Discount Amount') }}</th>
                                        <th>{{ translate('Date') }}</th>
                                        <th>{{ translate('Commission') }}</th>
                                        <th>{{ translate('Validity') }}</th>
                                    </thead>
                                    <tbody>
                                    @foreach($coupons as $coupon)
                                        <tr>
                                            <td>{{ $coupon->code }}</td>
                                            <td>{{ json_decode($coupon->details)->min_buy }}</td>
                                            <td>{{ $coupon->discount }}@if($coupon->discount_type == 'percent')%@endif</td>
                                            <td>{{ json_decode($coupon->details)->max_discount }}</td>
                                            <td>{{ date('d/m/Y', $coupon->start_date) }} - {{ date('d/m/Y', $coupon->end_date) }}</td>
                                            <td>{{ $coupon->commission }}@if($coupon->commission_type == 'percent')%@endif</td>
                                            <td>
                                                @if(coupon_has_expired($coupon->end_date))
                                                <span class="badge badge-inline badge-danger">@lang('delegate::delivery.expired')</span>
                                                @else
                                                <span class="badge badge-inline badge-success">@lang('delegate::delivery.valid')</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="aiz-pagination">
                                    {{ $coupons->links() }}
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
