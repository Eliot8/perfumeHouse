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
                            <th>{{ translate('Product')}}</th>
                            <th>{{ translate('Quantity')}}</th>
                            <th>{{ translate('Price')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($total_stock as $key => $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
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

