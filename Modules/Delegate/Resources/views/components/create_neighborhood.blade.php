<div id="create-neighborhood-modal" class="modal fade" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">@lang('delegate::delivery.new_neighborhood')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center" style="overflow-y: unset !important;">
                <form action="{{ route('neighborhood.store') }}" method="POST">
                    @csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Name') }} <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="name" value="" placeholder="@lang('delegate::delivery.neighborhood_name')" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">@lang('delegate::delivery.zone') <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="zone" id="zone" data-live-search="true">
                                <option value="" disabled selected>@lang('delegate::delivery.select_zone')</option>
                                @foreach (\DB::table('zones')->select('id', 'name')->get() as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-link mt-2 text-success" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-success mt-2">{{ translate('Save') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>