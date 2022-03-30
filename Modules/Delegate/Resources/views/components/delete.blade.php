<div id="delete-modal{{$id}}" class="modal fade" >
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{translate('Delete Confirmation')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mt-1">{{translate('Are you sure to delete this?')}}</p>
                <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{translate('Cancel')}}</button>
                {{-- <form action="{{ route($name . '.destroy', $id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger mt-2">{{translate('Delete')}}</button>
                </form> --}}
                <a href="{{ route($name . '.destroy', $id) }}" class="btn btn-danger mt-2">{{translate('Delete')}}</a>
            </div>
        </div>
    </div>
</div>
