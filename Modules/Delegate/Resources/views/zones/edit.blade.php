{{-- <div id="edit-modal{{$zone->id}}" class="modal fade" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{ translate('Edit Zone') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center" style="overflow-y: unset !important;">
                <form action="{{ route('zones.update', $zone->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Name') }} <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="name" value="{{ $zone->name }}" placeholder="{{ translate('Zone Name') }}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Province') }} <span class="text-danger">*</span></label>
                        <div class="col-md-8 province-select">
                            <select class="form-control aiz-selectpicker" name="province_id" id="province_id" data-live-search="true">
                                <option value="">{{ translate('Select Province') }}</option>
                                @foreach (\DB::table('provinces')->select('id', 'name')->get() as $province)
                                <option value="{{ $province->id }}" @if($province->id == $zone->province_id) selected @endif>{{ $province->name }}</option>
                                @endforeach
                            </select>
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
    <h5 class="mb-0 h6">{{translate('Edit Zone')}}</h5>
</div>
<div class="">
    <form class="form form-horizontal mar-top" action="{{route('zones.update', $zone->id)}}" method="POST" id="choice_form">
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
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $zone->name }}" placeholder="{{ translate('Zone Name') }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Province') }} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control aiz-selectpicker" name="province_id" id="province_id" data-live-search="true">
                                    <option value="">{{ translate('Select Province') }}</option>
                                    @foreach (\DB::table('provinces')->select('id', 'name')->get() as $province)
                                    <option value="{{ $province->id }}" @if($zone->province_id == $province->id) selected @endif>{{ $province->name }}</option>
                                    @endforeach
                                </select>
                                  @error('province_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
            {{-- <div class="col-12">
               
            </div> --}}
        </div>
    </form>
</div>

@endsection

