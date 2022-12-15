@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">@lang('delegate::delivery.edit_province')</h5>
</div>
<div class="">
    <form class="form form-horizontal mar-top" action="{{ route('provinces.update', $province->id) }}" method="POST" id="province_form">
        <div class="row gutters-5">
            <div class="col-lg-8 mx-auto">
                @csrf
                @method('PUT')
                <input type="hidden" name="added_by" value="admin">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Name') }} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="name" value="{{ $province->name }}" placeholder="@lang('delegate::delivery.province_name')" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">@lang('delegate::delivery.delegate_cost') <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="number" class="form-control" name="delegate_cost" value="{{ $province->delegate_cost }}" placeholder="@lang('delegate::delivery.delegate_cost')" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">@lang('delegate::delivery.delegate_commission') <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="number" class="form-control" name="delegate_commission" max="100" min="0" step="5" value="{{ $province->delegate_commission }}" placeholder="@lang('delegate::delivery.delegate_commission')" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Free Shipping')}}</label>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="radio" name="shipping_type" value="free" @if($province->free_shipping) checked @endif >
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">@lang('delegate::delivery.shipping_cost')</label>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="radio" name="shipping_type" value="cost" @if(!$province->free_shipping) checked @endif> 
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="flat_rate_shipping_div" style=" display: @if(!$province->free_shipping) block @else none @endif;">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Shipping cost')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="number" lang="en" min="0" value="{{ $province->shipping_cost }}" step="0.01" placeholder="{{ translate('Shipping cost') }}" name="shipping_cost" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="btn-toolbar float-right mb-3" role="toolbar" aria-label="Toolbar with button groups">
                            <div class="btn-group" role="group" aria-label="Second group">
                                <button type="submit" name="button" class="btn btn-primary" onclick="submitForm()">{{ translate('Update') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
@section('script')
    <script type="text/javascript">
    $("[name=shipping_type]").on("change", function (){
        $(".flat_rate_shipping_div").hide();

        if($(this).val() == 'cost'){
            $(".flat_rate_shipping_div").show();
        }
    });

    function submitForm() {
        console.log('hi');
        $('#province_form').submit();
    }
    </script>
@endsection

