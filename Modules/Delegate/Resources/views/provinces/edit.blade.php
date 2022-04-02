{{-- <div id="edit-modal{{$province->id}}" class="modal fade" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">@lang('delegate::delivery.edit_province')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <form action="{{ route('provinces.update', $province->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Name') }} <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="name" value="{{ $province->name }}" placeholder="@lang('delegate::delivery.province_name')" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary mt-2">{{ translate('Update') }}</button>
                </form>
            </div>
        </div>
    </div>
</div> --}}

@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">@lang('delegate::delivery.edit_province')</h5>
</div>
<div class="">
    <form class="form form-horizontal mar-top" action="{{route('provinces.update', $province->id)}}" method="POST" id="choice_form">
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
                        <div class="btn-toolbar float-right mb-3" role="toolbar" aria-label="Toolbar with button groups">
                            <div class="btn-group" role="group" aria-label="Second group">
                                <button type="submit" name="button" value="create" class="btn btn-primary action-btn">{{ translate('Update') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{-- <form action="{{ route('provinces.update', $province->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group row">
            <label class="col-md-3 col-from-label">{{ translate('Name') }} <span class="text-danger">*</span></label>
            <div class="col-md-8">
                <input type="text" class="form-control" name="name" value="{{ $province->name }}" placeholder="@lang('delegate::delivery.province_name')" required>
            </div>
        </div>
        <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{ translate('Cancel') }}</button>
        <button type="submit" class="btn btn-primary mt-2">{{ translate('Update') }}</button>
    </form> --}}
</div>

@endsection

