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
                            <input type="text" class="form-control" name="name" placeholder="@lang('delegate::delivery.province_name')" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">@lang('delegate::delivery.delegate_cost') <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input type="number" class="form-control" name="delegate_cost" placeholder="@lang('delegate::delivery.delegate_cost')" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">@lang('delegate::delivery.delegate_commission') <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input type="number" class="form-control" name="delegate_commission" max="100" min="0" step="5" placeholder="@lang('delegate::delivery.delegate_commission')" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{translate('Free Shipping')}}</label>
                        <div class="col-md-8">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="radio" name="shipping_type" value="free" checked>
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">@lang('delegate::delivery.shipping_cost')</label>
                        <div class="col-md-8">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="radio" name="shipping_type" value="cost">
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="flat_rate_shipping_div" style="display: none">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Shipping cost')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ translate('Shipping cost') }}" name="shipping_cost" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-info mt-2">{{ translate('Save') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>