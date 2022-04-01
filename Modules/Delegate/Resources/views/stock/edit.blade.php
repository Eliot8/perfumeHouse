<div id="edit-modal{{$item->id}}" class="modal fade">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">@lang('delegate::delivery.edit_stock')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <form action="{{ route('stock.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    {{-- <input type="hidden" name="delegate" value="{{ $item->delegate_id }}"> --}}
                    <input type="number" class="form-control" name="quantity" id="quantity" value="{{ $item->stock }}">
                    <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-sm btn-primary mt-2">{{ translate('Update') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>