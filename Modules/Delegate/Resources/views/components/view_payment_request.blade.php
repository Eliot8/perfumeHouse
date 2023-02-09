{{-- <div iclass="row"> --}}
<div class="modal-body">
    <table class="table table-sm table-bordered">
        <thead>
            <tr class="gry-color" style="background: #eceff4;">
                <th width="25%" class="text-left">{{ Translate('paypal') }}</th>
                <th width="25%" class="text-left">{{ Translate('bank_information') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $payment->delegate->paypal_email }}</td>
                <td>{{ $payment->delegate->bank_information }}</td>
            </tr>
        </tbody>
      </table>
    <div class="form-group mb-3">
        <label for="amount">{{ translate('Amount') }}:</label>
        <input type="text" name="amount" class="form-control" value="{{ number_format($payment->amount, 0) }}" disabled>
    </div>

    <div class="form-group mb-3">
        <label for="attached_pieces">@lang('delegate::delivery.attached_pieces'):</label>
        <div>
            @foreach($payment->attached_pieces as $file)
            <img class="img-thumbnail rounded img-modal-target" src="{{ asset('uploads/payment_requests/' . $file) }}" width="350" height="150">
            @endforeach
        </div>
    </div>

    <div class="form-group mb-3">
        <label for="comment">{{ translate('Comment') }}:</label>
        <textarea name="comment" id="comment" cols="30" rows="2" class="form-control" disabled>{{ $payment->comment }}</textarea>
    </div>
</div>

<div class="modal-footer" style="justify-content: space-between;">
    @if($payment->status === 'pending')
    <div>
        <a href="{{ route('delegates.update_payment_request_status', ['id' => $payment->id, 'status' => 'approved']) }}" class="btn btn-success btn-sm">
            <i class="las la-check"></i>
            {{ Translate('Approval') }}
        </a>
        <a href="{{ route('delegates.update_payment_request_status', ['id' => $payment->id, 'status' => 'rejected']) }}" class="btn btn-danger btn-sm">
            <i class="las la-times"></i>
            {{ Translate('Reject') }}
        </a>
    </div>
    @endif
    <div>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ translate('Close') }}</button>
    </div>
</div>
