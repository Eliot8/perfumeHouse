@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h1 class="mb-0 h6"><a href="{{ route('delegates.index') }}" class="text-dark"><i class="las la-arrow-left"></i> @lang('delegate::delivery.back')</a></h1>
</div>
<div class="">
    <form class="form form-horizontal mar-top" action="{{route('delegates.update', $delegate->id)}}" method="POST" id="choice_form">
        <div class="row gutters-5">
            <div class="col-lg-8">
                @csrf
                @method('PUT')
                <input type="hidden" name="added_by" value="admin">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">@lang('delegate::delivery.delegate_info')</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">@lang('delegate::delivery.delegate_name') <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $delegate->full_name }}" placeholder="@lang('delegate::delivery.delegate_name')" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Phone') }}</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="phone_number" value="{{ $delegate->phone_number }}" placeholder="{{ translate('Phone') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Address') }}</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="address" value="{{ $delegate->address }}" placeholder="{{ translate('Address') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">@lang('delegate::delivery.delivery_info')</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">@lang('delegate::delivery.province') <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control aiz-selectpicker" name="province_id" id="province_id" data-live-search="true">
                                    <option value="">@lang('delegate::delivery.select_province')</option>
                                    @foreach (\DB::table('provinces')->select('id', 'name')->get() as $province)
                                    <option value="{{ $province->id }}" @if($delegate->province_id == $province->id) selected @endif>{{ $province->name }}</option>
                                    @endforeach
                                </select>
                                @error('province_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">@lang('delegate::delivery.zones')</label>
                            <div class="col-md-8" id="zones_select">
                                <select class="form-control aiz-selectpicker" name="zones[]" id="zones" data-live-search="true" multiple>
                                    @foreach (Modules\Delegate\Entities\Zone::where('province_id', $delegate->province_id)->get() as $zone)
                                    <optgroup label="{{ $zone->name }}">
                                        @forelse($zone->neighborhoods as $item)
                                        <option value="{{ $item->id }}" @if(in_array($item->id, json_decode($delegate->zones) ?? [])) selected @endif>{{ $item->name }}</option>
                                        @empty
                                        <option value="{{ $zone->id }}" @if(in_array($zone->id, json_decode($delegate->zones) ?? [])) selected @endif>{{ $zone->name }}</option>
                                        @endforelse
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">@lang('delegate::delivery.payment_info')</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="paypal" class="col-md-3 col-from-label">{{ Translate('Paypal') }} - Paypal</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="paypal" value="{{ $delegate->paypal_email }}" placeholder="{{ Translate('Paypal') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bank_info" class="col-md-3 col-from-label">{{ Translate('Bank Information') }} - Bank</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="bank_info" value="{{ $delegate->bank_information }}" placeholder="{{ Translate('Bank Information') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">@lang('delegate::delivery.delegate_account')</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="email">{{ translate('Email') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ $delegate->email }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="password-fields" style="display: none;">
                            <div class="form-group mb-3">
                                <label for="password">{{ translate('Password') }} <span class="text-danger">*</span></label>
                                <div class="eye-box" style="position: relative;">
                                    <input type="password" name="password" id="delegate_password" class="form-control @error('password') is-invalid @enderror" style="padding-right: 40px;">
                                    <i class="las la-eye eye" style="font-size: 18px; cursor: pointer; position: absolute; top: 13px; right: 13px; display: none;"></i>
                                    <i class="las la-eye-slash eye" style="font-size: 18px; cursor: pointer; position: absolute; top: 13px; right: 13px; display: none;"></i>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="password_confirmation">@lang('delegate::delivery.password_confirmation') <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                            <a href="#" class="text-info cancel-reset" style="float:right;">{{ translate('Cancel') }}</a>
                        </div>
                        <input type="hidden" name="reset_password" id="password_state" value="false">
                        <a href="#" class="text-info reset-password">@lang('delegate::delivery.reset_password')</a>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="btn-toolbar float-right mb-3" role="toolbar" aria-label="Toolbar with button groups">
                    <div class="btn-group" role="group" aria-label="Second group">
                        <button type="submit" name="button" value="create" class="btn btn-primary action-btn">{{ translate('Update') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('script')

<script type="text/javascript">

    @error('password')
        $('.reset-password').hide();
        $('.password-fields').show();
        $('#password_state').val(true);
    @enderror
    $('input').on('keypress', function() {
        $(this).removeClass('is-invalid');
    });

    
    if($('#delegate_password').val()){
        $('.la-eye-slash').show();
    }       
    $('#delegate_password').on('keyup', function() {    
        if(!$(this).val()) $('.eye').hide();
    }); 
    $('#delegate_password').on('keypress', function() {
        $('.la-eye-slash').show();
    }); 
    $('.la-eye').on('click', function() {
        $(this).hide();
        $('.la-eye-slash').show();
        $('#delegate_password').attr('type', 'password');
    });
    $('.la-eye-slash').on('click', function() {
        $(this).hide();
        $('.la-eye').show();
        $('#delegate_password').attr('type', 'text');
    });

    $('.reset-password').on('click', function() {
        $(this).hide();
        $('.password-fields').show();
        $('#password_state').val(true);
    });
    $('.cancel-reset').on('click', function() {
        $('.password-fields').hide();
        $('.reset-password').show();
        $('#password_state').val(false);
    });

    /********** GET ZONES USING AJAX *******************/
    $('#province_id').on('change', function() {
        $.ajax({
            url: `/province/${$(this).val()}/zone`,
            type: "GET",
            // dataType: "HTML",
            success: function(response) {
                $('#zones').empty().append(response.options).selectpicker('refresh');
            }
        });
    });

</script>

@endsection
