<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{ translate('Order id')}}: {{ $order->code }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>

@php
    $status = $order->orderDetails->first()->delivery_status;
    $user_type = Auth::user()->user_type == 'customer' ? 'Customer' : (Auth::user()->user_type == 'delivery_boy' ? 'Delivery Man' : '');
@endphp

<div class="modal-body gry-bg px-3 pt-3" id="modal_body">
    <div class="py-4">
        <div class="row gutters-5 text-center aiz-steps">
            <div class="col @if($status == 'pending') active @else done @endif">
                <div class="icon">
                    <i class="las la-file-invoice"></i>
                </div>
                <div class="title fs-12">{{ translate('Order placed')}}</div>
            </div>
            <div class="col @if($status == 'confirmed') active @elseif($status == 'on_delivery' || $status == 'delivered') done @endif">
                <div class="icon">
                    <i class="las la-newspaper"></i>
                </div>
              <div class="title fs-12">{{ translate('Confirmed')}}</div>
            </div>
            <div class="col @if($status == 'on_delivery') active @elseif($status == 'delivered') done @endif">
                <div class="icon">
                    <i class="las la-truck"></i>
                </div>
                <div class="title fs-12">{{ translate('On delivery')}}</div>
            </div>
            <div class="col @if($status == 'delivered') done @endif">
                <div class="icon">
                    <i class="las la-clipboard-check"></i>
                </div>
                <div class="title fs-12">{{ translate('Delivered')}}</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-4">
                <div class="card-header">
                  <b class="fs-15">@lang('delegate::delivery.orders_comments')</b>
                </div>
                <div class="card-body pb-0 comments">
                    @if(Auth::user()->user_type != 'admin')
                        @if($order->delivery_status != 'delivered')
                        <div class="comment_form">
                            <label>{{ translate('Comment') }} <span class="text-danger">*</span></label>
                            <div class="form-group row">
                                <div class="col-md-10">
                                    <textarea class="form-control" name="comment" id="comment" value="" placeholder="{{ translate('Enter Your Comment') }}" required></textarea>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="comment_form" onclick="postComment({{ $order->id }})" class="btn btn-info btn-sm mt-2">{{ translate('Comment') }}</button>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif
                    @forelse($comments as $comment)
                    <div class="row bg-light py-4 px-2 my-4 mx-2 comment" style="border-radius: 5px; border: 1px solid #111; box-shadow: 5px 5px #111;">
                        <div class="col-md-10">
                            <strong style="width: 64px;">
                                @if($comment->user->user_type == 'customer')
                                ({{ translate('Customer') }}) {{ $comment->user->name }}: 
                                @elseif($comment->user->user_type == 'delivery_boy')
                                (@lang('delegate::delivery.delivery_man')) - {{ $comment->user->name }} : 
                                @endif
                            </strong>
                        </div>
                        <div class="col-md-2 text-right">
                            <span class="text-italic">{{ date('Y/m/d H:i', strtotime($comment->created_at)) }}</span>
                        </div>
                        <div class="col-md-12">{{ $comment->content }} </div>
                    </div>
                    @empty
                    <div class="row bg-info ">
                        <div class="col-md-10">
                            <strong style="width: 64px;">
                              
                            </strong>
                        </div>
                    </div>
                    <div id="order_alert" class="alert alert-info d-flex align-items-center">
                        @lang('delegate::delivery.no_order_comments').
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function postComment(order_id){
        const comment = $('#comment').val();
        $.post('{{ route('purchase_history.postComment') }}', { 
            _token: AIZ.data.csrf,
            order_id: order_id,
            comment: comment,
            }, function() {
            let data = `
            <div class="row bg-light py-4 px-2 my-4 mx-2 comment" style="border-radius: 5px; border: 1px solid #111;
            box-shadow: 5px 5px #111;">
                <div class="col-md-10">
                    <strong style="width: 64px;">{{ $user_type }}: </strong>
                </div>
                <div class="col-md-2 text-right">
                    <span class="text-italic">{{ date('Y/m/d H:i') }}</span>
                </div>
                <div class="col-md-12">${comment}</div>
            </div>`;
            $('.comments').append(data);
            $('#comment').val('');
            $('#order_alert').remove();
            $('.modal-body').animate({scrollTop: $('.modal-body').scrollHeight}, "fast");
        });
        $('.modal-body').animate({ scrollTop: $(this).height() }, "fast");
    }       
    
</script>
