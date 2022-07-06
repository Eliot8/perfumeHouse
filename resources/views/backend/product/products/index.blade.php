@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('All products')}}</h1>
        </div>
        @if($type != 'Seller')
        <div class="col text-right">
            <a href="{{ route('products.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add New Product')}}</span>
            </a>
        </div>
        @endif
    </div>
</div>
<br>

<div class="card filter-card">
    <div class="card-header row gutters-5" >
        <div class="col" data-toggle="collapse" href="#filter" role="button" aria-expanded="false" aria-controls="filter">
            <h5 class="mb-0 h6">{{ translate('Filter') }}</h5>
        </div> 
        @if(request()->query())
        <div class="mb-2 mb-md-0">
            <a href="{{ route('products.all') }}" >{{ translate('Clear Filter') }}</a>
        </div>
        @endif
    </div>
    <div class="card-body collapse show" id="filter">
        <form class="form-horizontal" action="{{ route('products.all') }}" method="GET">
            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Added By') }}</label>
                    @if($type == 'Seller')
                    <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="user_id" name="user_id">
                        <option value="">{{ translate('All Sellers') }}</option>
                        @foreach (App\Models\Seller::all() as $key => $seller)
                            @if ($seller->user != null && $seller->user->shop != null)
                                <option value="{{ $seller->user->id }}" @if(request()->has('user_id') && request()->filled('user_id') && request()->get('user_id') == $seller->id) selected @endif>{{ $seller->user->shop->name }} ({{ $seller->user->name }})</option>
                            @endif
                        @endforeach
                    </select>
                    @endif
                    @if($type == 'All')
                    <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="user_id" name="user_id">
                        <option value="" selected disabled hidden>{{ translate('All Sellers') }}</option>
                            @foreach (App\Models\User::where('user_type', '=', 'admin')->orWhere('user_type', '=', 'seller')->get() as $key => $seller)
                                <option value="{{ $seller->id }}"@if(request()->has('user_id') && request()->filled('user_id') && request()->get('user_id') == $seller->id) selected @endif>{{ $seller->name }}</option>
                            @endforeach
                    </select>
                    @endif
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Sort By') }}</label>
                     <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" name="type" id="type">
                        <option value="" selected disabled hidden>{{ translate('Sort By') }}</option>
                        <option value="rating,desc" @isset($col_name , $query) @if($col_name == 'rating' && $query == 'desc') selected @endif @endisset>{{translate('Rating (High > Low)')}}</option>
                        <option value="rating,asc" @isset($col_name , $query) @if($col_name == 'rating' && $query == 'asc') selected @endif @endisset>{{translate('Rating (Low > High)')}}</option>
                        <option value="num_of_sale,desc"@isset($col_name , $query) @if($col_name == 'num_of_sale' && $query == 'desc') selected @endif @endisset>{{translate('Num of Sale (High > Low)')}}</option>
                        <option value="num_of_sale,asc"@isset($col_name , $query) @if($col_name == 'num_of_sale' && $query == 'asc') selected @endif @endisset>{{translate('Num of Sale (Low > High)')}}</option>
                        <option value="unit_price,desc"@isset($col_name , $query) @if($col_name == 'unit_price' && $query == 'desc') selected @endif @endisset>{{translate('Base Price (High > Low)')}}</option>
                        <option value="unit_price,asc"@isset($col_name , $query) @if($col_name == 'unit_price' && $query == 'asc') selected @endif @endisset>{{translate('Base Price (Low > High)')}}</option>
                    </select>
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Category') }}</label>
                    <select name="category" id="category" class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0">
                        <option value="" selected disabled hidden>{{ translate('All Categories') }}</option>
                        @foreach (App\Models\Category::select('id','name')->get() as $category)
                            <option value="{{ $category->id }}" @if(request()->has('category') && request()->filled('category') && request()->get('category') == $category->id) selected @endif>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Brand') }}</label>
                    <select name="brand" id="brand" class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0">
                        <option value="" selected disabled hidden>{{ translate('All Brands') }}</option>
                        @foreach (App\Models\Brand::select('id','name')->get() as $brand)
                            <option value="{{ $brand->id }}" @if(request()->has('brand') && request()->filled('brand') && request()->get('brand') == $brand->id) selected @endif>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Product Name') }}</label>
                    <input type="text" class="form-control form-control-sm" id="search" name="search" value="{{ request()->query('search') }}" placeholder="{{ translate('Type & Enter') }}">
                </div>
                 <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Published') }}</label>
                    <div>
                        <label class="aiz-switch aiz-switch-success mb-0">
                        <input value="true" name="published" type="checkbox" @if(request()->query('published')) checked @endif>
                        <span class="slider round"></span></label>
                    </div>
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Featured') }}</label>
                    <div>
                        <label class="aiz-switch aiz-switch-success mb-0">
                        <input value="true" name="featured" type="checkbox" @if(request()->query('featured')) checked @endif>
                        <span class="slider round"></span></label>
                    </div>
                </div>
                <div class="col-lg-3 form-group">
                    <label class="col-from-label">{{ translate('Todays Deal') }}</label>
                    <div>
                        <label class="aiz-switch aiz-switch-success mb-0">
                        <input value="true" name="todays_deal" type="checkbox" @if(request()->query('todays_deal')) checked @endif>
                        <span class="slider round"></span></label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group mb-0 float-right">
                        <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <form class="" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col-3">
                <h5 class="mb-md-0 h6">{{ translate('All Product') }}</h5>
            </div>

            <div class="col table-links">
                <a href="" class="export-to-csv btn btn-primary btn-sm mb-1"><i class="las la-file-csv fs-18 mr-2"></i>@lang('delegate::delivery.export_to_csv')</a>
                <a href="" class="export-to-excel btn btn-primary btn-sm mb-1"><i class="las la-file-excel fs-18 mr-2"></i>@lang('delegate::delivery.export_to_excel')</a>
                <a href="" class="print btn btn-primary btn-sm mb-1"><i class="las la-print fs-18 mr-2"></i>@lang('delegate::delivery.print')</a>
                <div class="btn-group mb-1">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                        <i class="las la-columns fs-18 mr-2"></i>
                        @lang('delegate::delivery.column_visibility')
                    </button>
                    <ul class="dropdown-menu">
                        <li class="dropdown-item column_visibility" id="name" style="cursor: pointer;">{{ translate('Name') }}</li>
                        <li class="dropdown-item column_visibility" id="added_by" style="cursor: pointer;">{{ translate('Added_by') }}</li>
                        <li class="dropdown-item column_visibility" id="info" style="cursor: pointer;">{{ translate('Info') }}</li>
                        <li class="dropdown-item column_visibility" id="total_stock" style="cursor: pointer;">{{ translate('Total Stock') }}</li>
                        <li class="dropdown-item column_visibility" id="todays_deal" style="cursor: pointer;">{{ translate('Todays Deal') }}</li>
                        <li class="dropdown-item column_visibility" id="published" style="cursor: pointer;">{{ translate('Published') }}</li>
                        <li class="dropdown-item column_visibility" id="featured" style="cursor: pointer;">{{ translate('Featured') }}</li>
                        <li class="dropdown-item column_visibility" id="options" style="cursor: pointer;">{{ translate('Options') }}</li>
                    </ul>
                </div>
                <a href="" class="export-to-pdf btn btn-primary btn-sm mb-1"><i class="las la-file-pdf fs-18 mr-2"></i>@lang('delegate::delivery.export_to_pdf')</a>
            </div>
            
            <div class="dropdown mb-2 mb-md-0">
                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                    {{translate('Bulk Action')}}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" onclick="bulk_delete()"> {{translate('Delete selection')}}</a>
                </div>
            </div>
            
            {{-- @if($type == 'Seller')
            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="user_id" name="user_id" onchange="sort_products()">
                    <option value="">{{ translate('All Sellers') }}</option>
                    @foreach (App\Models\Seller::all() as $key => $seller)
                        @if ($seller->user != null && $seller->user->shop != null)
                            <option value="{{ $seller->user->id }}" @if ($seller->user->id == $seller_id) selected @endif>{{ $seller->user->shop->name }} ({{ $seller->user->name }})</option>
                        @endif
                    @endforeach
                </select>
            </div>
            @endif
            @if($type == 'All')
            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="user_id" name="user_id" onchange="sort_products()">
                    <option value="">{{ translate('All Sellers') }}</option>
                        @foreach (App\Models\User::where('user_type', '=', 'admin')->orWhere('user_type', '=', 'seller')->get() as $key => $seller)
                            <option value="{{ $seller->id }}" @if ($seller->id == $seller_id) selected @endif>{{ $seller->name }}</option>
                        @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" name="type" id="type" onchange="sort_products()">
                    <option value="">{{ translate('Sort By') }}</option>
                    <option value="rating,desc" @isset($col_name , $query) @if($col_name == 'rating' && $query == 'desc') selected @endif @endisset>{{translate('Rating (High > Low)')}}</option>
                    <option value="rating,asc" @isset($col_name , $query) @if($col_name == 'rating' && $query == 'asc') selected @endif @endisset>{{translate('Rating (Low > High)')}}</option>
                    <option value="num_of_sale,desc"@isset($col_name , $query) @if($col_name == 'num_of_sale' && $query == 'desc') selected @endif @endisset>{{translate('Num of Sale (High > Low)')}}</option>
                    <option value="num_of_sale,asc"@isset($col_name , $query) @if($col_name == 'num_of_sale' && $query == 'asc') selected @endif @endisset>{{translate('Num of Sale (Low > High)')}}</option>
                    <option value="unit_price,desc"@isset($col_name , $query) @if($col_name == 'unit_price' && $query == 'desc') selected @endif @endisset>{{translate('Base Price (High > Low)')}}</option>
                    <option value="unit_price,asc"@isset($col_name , $query) @if($col_name == 'unit_price' && $query == 'asc') selected @endif @endisset>{{translate('Base Price (Low > High)')}}</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type & Enter') }}">
                </div>
            </div> --}}
        </div>
    
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-all">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </th>
                        <th class="name">{{translate('Name')}}</th>
                        @if($type == 'Seller' || $type == 'All')
                        <th class="added_by" data-breakpoints="lg">{{translate('Added By')}}</th>
                        @endif
                        <th class="info" data-breakpoints="sm">{{translate('Info')}}</th>
                        <th class="total_stock" data-breakpoints="md">{{translate('Total Stock')}}</th>
                        <th class="todays_deal" data-breakpoints="lg">{{translate('Todays Deal')}}</th>
                        <th class="published" data-breakpoints="lg">{{translate('Published')}}</th>
                        @if(get_setting('product_approve_by_admin') == 1 && $type == 'Seller')
                        <th data-breakpoints="lg">{{translate('Approved')}}</th>
                        @endif
                        <th class="featured" data-breakpoints="lg">{{translate('Featured')}}</th>
                        <th class="options" data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $key => $product)
                    <tr>
                        <td>
                            <div class="form-group d-inline-block">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" class="check-one" name="id[]" value="{{$product->id}}">
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        </td>
                        <td class="name">
                            <div class="row gutters-5 w-200px w-md-300px mw-100">
                                <div class="col-auto">
                                    <img src="{{ uploaded_asset($product->thumbnail_img)}}" alt="Image" class="size-50px img-fit">
                                </div>
                                <div class="col">
                                    <span class="text-muted text-truncate-2">{{ $product->getTranslation('name') }}</span>
                                </div>
                            </div>
                        </td>
                        @if($type == 'Seller' || $type == 'All')
                        <td class="added_by">{{ $product->user->name ?? ''}}</td>
                        @endif
                        <td class="info">
                            <strong>{{translate('Num of Sale')}}:</strong> {{ $product->num_of_sale }} {{translate('times')}} </br>
                            <strong>{{translate('Base Price')}}:</strong> {{ single_price($product->unit_price) }} </br>
                            <strong>{{translate('Rating')}}:</strong> {{ $product->rating }} </br>
                        </td>
                        <td class="total_stock">
                            @php
                                $qty = 0;
                                if($product->variant_product) {
                                    foreach ($product->stocks as $key => $stock) {
                                        $qty += $stock->qty;
                                        echo $stock->variant.' - '.$stock->qty.'<br>';
                                    }
                                }
                                else {
                                    //$qty = $product->current_stock;
                                    $qty = optional($product->stocks->first())->qty;
                                    echo $qty;
                                }
                            @endphp
                            @if($qty <= $product->low_stock_quantity)
                                <span class="badge badge-inline badge-danger">Low</span>
                            @endif
                        </td>
                        <td class="todays_deal">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_todays_deal(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->todays_deal == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                            @if($product->todays_deal == 1)
                            <span class="print-icons d-none">
                                <i class="las la-check"></i>
                            </span>
                            @endif
                        </td>
                        <td class="published">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_published(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->published == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                            @if($product->published == 1)
                            <span class="print-icons d-none">
                                <i class="las la-check"></i>
                            </span>
                            @endif
                        </td>
                        @if(get_setting('product_approve_by_admin') == 1 && $type == 'Seller')
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_approved(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->approved == 1) echo "checked"; ?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                        @endif
                        <td class="featured">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_featured(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->featured == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                            @if($product->featured == 1)
                            <span class="print-icons d-none">
                                <i class="las la-check"></i>
                            </span>
                            @endif
                        </td>
                        <td class="text-right options">
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm"  href="{{ route('product', $product->slug) }}" target="_blank" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            @if ($type == 'Seller')
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('products.seller.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            @else
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('products.admin.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            @endif
                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{route('products.duplicate', ['id'=>$product->id, 'type'=>$type]  )}}" title="{{ translate('Duplicate') }}">
                                <i class="las la-copy"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('products.destroy', $product->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $products->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">
        
        $(document).on("change", ".check-all", function() {
            if(this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;                        
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;                       
                });
            }
          
        });

        $(document).ready(function(){
            //$('#container').removeClass('mainnav-lg').addClass('mainnav-sm');
        });

        function update_todays_deal(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.todays_deal') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Todays Deal updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_published(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.published') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Published products updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
        
        function update_approved(el){
            if(el.checked){
                var approved = 1;
            }
            else{
                var approved = 0;
            }
            $.post('{{ route('products.approved') }}', {
                _token      :   '{{ csrf_token() }}', 
                id          :   el.value, 
                approved    :   approved
            }, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Product approval update successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_featured(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.featured') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Featured products updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function sort_products(el){
            $('#sort_products').submit();
        }
        
        function bulk_delete() {
            var data = new FormData($('#sort_products')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-product-delete')}}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if(response == 1) {
                        location.reload();
                    }
                }
            });
        }

       

        $(".export-to-excel").click(function(e) {
            e.preventDefault();
            $(".aiz-table").table2excel({
                exclude: ".excludeThisClass",
                name: "All Products",
                filename: "products.xls", 
                preserveColors: false 
            });
        });
        $(".export-to-pdf").click(function(e) {
            e.preventDefault();
             html2canvas($('.aiz-table')[0], {
                onrendered: function (canvas) {
                    var data = canvas.toDataURL();
                    var docDefinition = {
                        content: [{
                            image: data,
                            width: 500
                        }]
                    };
                    pdfMake.createPdf(docDefinition).download("products.pdf");
                }
            });
        });

        $(".export-to-csv").click(function(e) {
            e.preventDefault();
            $(".aiz-table").tableHTMLExport({
                type:'csv',
                filename: 'products.csv',
                separator: ',',
                newline: '\r\n',
                trimContent: true,
                quoteFields: true,
                // CSS selector(s)
                ignoreColumns: '',
                ignoreRows: '',      
                // your html table has html content?
                htmlContent: true,
                // debug
                consoleLog: false,      
            });
        });

    </script>
@endsection
