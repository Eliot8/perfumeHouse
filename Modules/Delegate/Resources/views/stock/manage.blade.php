@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h1 class="mb-0 h6"><a href="{{ route('stock.index') }}" class="text-dark"><i class="las la-arrow-left"></i> @lang('delegate::delivery.back')</a></h1>
</div>
<div class="">
    <div class="row gutters-5">
        <div class="col-lg-8">
            @csrf
            <input type="hidden" name="added_by" value="admin">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">@lang('delegate::delivery.product_stock')</h5>
                </div>
                <div class="card-body">
                        <table class="table aiz-table mb-0">
                        <thead>
                            <tr>
                                <th data-breakpoints="lg">#</th>
                                <th>{{ translate('Product Name') }}</th>
                                <th data-breakpoints="lg">{{ translate('Quantity') }}</th>
                                <th data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $key => $item)
                            <tr>
                                <td>{{ ($key+1) + ($products->currentPage() - 1)*$products->perPage() }}</td>
                                <td>
                                    <div class="row gutters-5 w-100px w-md-100px mw-100">
                                        <div class="col">
                                            <span class="text-muted text-truncate-2">{{ $item->product->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong class="btn-info btn-icon btn-circle btn-sm" style="transition: all 0.3s ease;">{{ $item->stock }}</strong>
                                </td>
                                <td class="text-right">
                                    <button type="button" class="btn btn-soft-primary btn-icon btn-circle btn-sm" data-toggle="modal" data-target="#edit-modal{{ $item->id }}" title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </button>
                                    @include('delegate::stock.edit')
                                    <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm" data-toggle="modal" data-target="#delete-modal{{ $item->id }}" title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </button>
                                    @component('delegate::components.delete', ['name' => 'stock', 'id' => $item->id])@endcomponent
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="aiz-pagination">
                        {{ $products->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
                <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">@lang('delegate::delivery.add_stock')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('stock.store') }}" method="POST">
                        <input type="hidden" name="delegate" value="{{ $delegate_id }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="email">{{ translate('Product') }} <span class="text-danger">*</span></label>
                            <select class="form-control aiz-selectpicker" name="product" id="products" data-live-search="true">
                                <option value="" disabled selected>@lang('delegate::delivery.select_product')</option>
                                @foreach (\DB::table('products')->select('id', 'name')->get() as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            @error('product')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">{{ translate('Quantity') }} <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" name="quantity" value="{{ old('quantity') }}" placeholder="{{ translate('Quantity') }}" required>
                            @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <div class="btn-toolbar float-right" role="toolbar" aria-label="Toolbar with button groups">
                                <div class="btn-group" role="group" aria-label="Second group">
                                    <button type="submit" name="button" value="create" class="btn btn-info action-btn">@lang('delegate::delivery.add')</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script type="text/javascript">

    $('input').on('keypress', function() {
        $(this).removeClass('is-invalid');
    });
    
    if($('#delegate_password').val()){
        $('.la-eye-slash').show();
    }       
    $('#delegate_password').on('keyup', function() {    
        if(!$(this).val()) $('.eye').hide();
    }); 
    $('#delegate_password').on('keypress', function() {
        $('.la-eye-slash').show();
    }); 
    $('.la-eye').on('click', function() {
        $(this).hide();
        $('.la-eye-slash').show();
        $('#delegate_password').attr('type', 'password');
    });
    $('.la-eye-slash').on('click', function() {
        $(this).hide();
        $('.la-eye').show();
        $('#delegate_password').attr('type', 'text');
    });
    
    /********** GET ZONES USING AJAX *******************/
    $('#province_id').on('change', function() {
        $.ajax({
            url: `/admin/province/${$(this).val()}/zone`,
            type: "GET",
            // dataType: "HTML",
            success: function(response) {
                $('#zone_id').empty().append(response.options).selectpicker('refresh');
            }
        });
    });
</script>

@endsection
