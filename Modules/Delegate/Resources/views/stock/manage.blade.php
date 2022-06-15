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
                    <table class="table">
                        <thead>
                            <tr>
                                <th data-breakpoints="lg">#</th>
                                <th>{{ translate('Product Name') }}</th>
                                <th>{{ translate('Colors') }}</th>
                                <th>{{ translate('attributes') }}</th>
                                <th data-breakpoints="lg">{{ translate('Quantity') }}</th>
                                <th data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $key => $item)
                            <tr>
                                <td>{{ ($key+1) + ($products->currentPage() - 1)*$products->perPage() }}</td>
                                <td>
                                    <div class="row gutters-5 w-100px w-md-200px mw-100">
                                        <div class="col-auto">
                                            <img src="{{ uploaded_asset($item->product->thumbnail_img)}}" alt="Image" class="size-50px img-fit">
                                        </div>
                                        <div class="col">
                                            <span class="text-muted text-truncate-2">{{ $item->product->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span>
                                    {{-- {{ dd(count(json_decode($item->attributes))) }} --}}
                                        @if(!empty(json_decode($item->colors)))
                                            @foreach(json_decode($item->colors) as $color)
                                            <span class="size-15px d-inline-block mr-2 rounded border" style="background: {{ $color }}"></span>
                                            <span>{{ \App\Models\Color::where('code', $color)->first()->name }}</span>
                                            @endforeach
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <span>
                                        @if(!empty(json_decode($item->attributes)))
                                            @foreach(json_decode($item->attributes) as $attribute)
                                            {{ $attribute }}
                                            @endforeach
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <strong class="badge badge-primary btn-circle btn-sm" style="transition: all 0.3s ease;">{{ $item->stock }}</strong>
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
                            <label for="product">{{ translate('Product') }} <span class="text-danger">*</span></label>
                            <select class="form-control aiz-selectpicker" name="product" id="products" data-live-search="true">
                                <option value="" disabled selected>@lang('delegate::delivery.select_product')</option>
                                @foreach (\DB::table('products')->select('id', 'name', 'colors', 'attributes')->get() as $product)
                                <option value="{{ $product->id }}"
                                    @if(count(json_decode($product->colors)) > 0) data-color="true" @endif 
                                    @if(count(json_decode($product->attributes)) > 0) data-attribute="true" @endif
                                    >{{ $product->name }}</option>
                                @endforeach
                            </select>
                            @error('product')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div id="colors_box" class="form-group mb-3" style="display: none;">
                            <label for="colors">{{ translate('Colors') }} <span class="text-danger">*</span></label>
                            <select class="form-control aiz-selectpicker" data-live-search="true" data-selected-text-format="count" name="colors[]" id="colors" multiple>
                            </select>
                            @error('colors')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="attributes_box" class="form-group mb-3" style="display: none;">
                            <label for="attributes">{{ translate('Attributes') }} <span class="text-danger">*</span></label>
                            <select class="form-control aiz-selectpicker" data-live-search="true" data-selected-text-format="count" name="attributes[]" id="attributes" multiple>
                            </select>
                            @error('attributes')
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

    $('#products').on('change', function() {
        const data_color = $(this).find(':selected').attr('data-color');
        const data_attribute = $(this).find(':selected').attr('data-attribute');

        if(data_color){
            $('#colors_box').show();
            $('#colors').empty();
            $.ajax({
                url: `/admin/product/${$(this).val()}/colors`,
                type: "GET",
                dataType: "JSON",
                success: function(response) {
                    const data = JSON.parse(response);
                    $('#colors').append(data);
                    AIZ.plugins.bootstrapSelect('refresh');
                }
            });
        } else {
            $('#colors_box').hide();
        }

        if(data_attribute){
            $('#attributes_box').show();
            $('#attributes').empty();

            $.ajax({
                url: `/admin/product/${$(this).val()}/attributes`,
                type: "GET",
                dataType: "JSON",
                success: function(response) {
                    const data = JSON.parse(response);
                    $('#attributes').append(data);
                    AIZ.plugins.bootstrapSelect('refresh');
                }
            });
        } else {
            $('#attributes_box').hide();
        }

    });
</script>

@endsection
