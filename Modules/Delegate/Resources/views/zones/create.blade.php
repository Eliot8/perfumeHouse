<div id="create-modal" class="modal fade" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{ translate('New Zone') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center" style="overflow-y: unset !important;">
                <form action="{{ route('zones.store') }}" method="POST">
                    @csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Name') }} <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="name" value="" placeholder="{{ translate('Zone Name') }}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Province') }} <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="province_id" id="province_id" data-live-search="true">
                                <option value="" disabled selected>{{ translate('Select Province') }}</option>
                                @foreach (\DB::table('provinces')->select('id', 'name')->get() as $province)
                                <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-info mt-2">{{ translate('Save') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>