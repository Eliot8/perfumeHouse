@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Total Stock') }}</h5>
        </div>

        @if (count($total_stock) > 0)
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ translate('Product Name')}}</th>
                            <th>{{ translate('Colors') }}</th>
                            <th>{{ translate('attributes') }}</th>
                            <th>{{ translate('Quantity')}}</th>
                            <th>{{ translate('Price')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($total_stock as $key => $item)
                            <tr>
                                <td>
                                    <div class="row">
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
                                       @if(!empty($item->color))
                                            <span class="d-block">
                                                <span class="size-15px d-inline-block mr-2 rounded border" style="background: {{ $item->color }}"></span>
                                                <span>{{ \App\Models\Color::where('code', $item->color)->first()->name }}</span>
                                            </span>
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <span>
                                        @if(!empty(json_decode($item->attributes)))
                                            @foreach(json_decode($item->attributes) as $attribute)
                                            <span class="d-block"> {{ $attribute }} </span> 
                                            @endforeach
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-inline badge-primary btn-circle">{{ $item->stock }}</span>
                                </td>
                                <td>
                                    {{ single_price($item->product->unit_price) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $total_stock->appends(request()->input())->links() }}
              	</div>
            </div>
        @endif
    </div>
@endsection

