<div id="view-neighborhoods-modal{{$zone->id}}" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">@lang('delegate::delivery.view_neighborhood')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <div class="col-md-12">
                    <table style="width: 100%;">
                    <thead>
                        <th>#</th>
                        <th>{{ translate('Name') }}</th>
                        <th>{{ translate('Delete') }}</th>
                    </thead>
                    <tbody>
                        @foreach($zone->neighborhoods as $key => $item)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{$item->name }}</td>
                            <td>
                                <a href="{{ route('neighborhood.destroy', $item->id) }}" class="btn-sm btn btn-icon btn-circle btn-soft-danger">
                                    <i class="las la-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>