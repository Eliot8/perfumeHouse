<div id="create-modal" class="modal fade" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">@lang('delegate::delivery.new_province')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <form action="{{ route('provinces.store') }}" method="POST">
                    @csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Name') }} <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="name" value="" placeholder="@lang('delegate::delivery.province_name')" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">@lang('delegate::delivery.profit_ratio') <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input type="number" class="form-control" name="profit_ratio" value="" placeholder="@lang('delegate::delivery.profit_ratio')" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-info mt-2">{{ translate('Save') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>