@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h1 class="mb-0 h6"><a href="{{ route('delegates.index') }}" class="text-dark"><i class="las la-arrow-left"></i> @lang('delegate::delivery.back')</a></h1>
</div>
<div class="">
    <form class="form form-horizontal mar-top" action="{{route('delegates.store')}}" method="POST" id="choice_form">
        <div class="row gutters-5">
            <div class="col-lg-8">
                @csrf
                <input type="hidden" name="added_by" value="admin">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">@lang('delegate::delivery.delegate_info')</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">@lang('delegate::delivery.delegate_name') <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="@lang('delegate::delivery.delegate_name')" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">@lang('delegate::delivery.phone_number')</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="phone_number" value="{{ old('phone_number') }}" placeholder="@lang('delegate::delivery.phone_number')">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Address') }}</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="address" value="{{ old('address') }}" placeholder="{{ translate('Address') }}">
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
                            <label class="col-md-3 col-from-label">@lang('delegate::delivery.provinces') <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control aiz-selectpicker" name="province_id" id="province_id" data-live-search="true">
                                    <option value="">@lang('delegate::delivery.select_province')</option>
                                    @foreach (\DB::table('provinces')->select('id', 'name')->get() as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
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
                                <select class="form-control aiz-selectpicker" name="zones[]" id="zone_id" data-live-search="true" multiple>
                                    
                                </select>
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
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
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
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="btn-toolbar float-right mb-3" role="toolbar" aria-label="Toolbar with button groups">
                    <div class="btn-group" role="group" aria-label="Second group">
                        <button type="submit" name="button" value="create" class="btn btn-info action-btn">{{ translate('Save') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('script')

<script type="text/javascript">

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
    
    
    $('#province_id').on('change', function() {
        $.ajax({
            url: `/province/${$(this).val()}/zone`,
            type: "GET",
            success: function(response) {
                $('#zone_id').empty().append(response.options).selectpicker('refresh');
            }
        });
    });
</script>

@endsection
