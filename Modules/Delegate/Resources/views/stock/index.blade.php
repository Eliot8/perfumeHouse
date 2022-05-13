@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">@lang('delegate::delivery.stock_management')</h1>
        </div>
    </div>
</div>
<br>

<div class="card">
    <form class="" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">@lang('delegate::delivery.stock_management')</h5>
            </div>
        </div>
    
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="lg">#</th>
                        <th>{{ translate('Name') }}</th>
                        <th data-breakpoints="md">@lang('delegate::delivery.province')</th>
                        <th data-breakpoints="lg">{{ translate('Stock') }}</th>
                        <th data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($delegates as $key => $delegate)
                    <tr>
                        <td>{{ ($key+1) + ($delegates->currentPage() - 1)*$delegates->perPage() }}</td>
                        <td>
                            <div class="row gutters-5 w-100px w-md-100px mw-100">
                                <div class="col">
                                    <span class="text-muted text-truncate-2">{{ $delegate->full_name }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong class="btn-soft-info btn-circle btn-sm" style="transition: all 0.3s ease;">{{ \DB::table('provinces')->where('id', $delegate->province_id)->first()->name }}</strong>
                        </td>
                        <td>
                            @if(getStockLevel($delegate->id) == 'empty')
                            <span class="btn-soft-danger btn-circle btn-sm" style="transition: all 0.3s ease;">@lang('delegate::delivery.' . getStockLevel($delegate->id))</span>
                            @else 
                            <span class="btn-soft-{{ getStockLevel($delegate->id) == 'high' ? 'success' : 'primary' }} btn-circle btn-sm" style="transition: all 0.3s ease;">@lang('delegate::delivery.' . getStockLevel($delegate->id))</span>
                            @endif 
                        </td>
                        <td class="text-right">
                            <a class="btn btn-soft-dark btn-icon btn-circle btn-sm"  href="{{ route('stock.manage', $delegate->id) }}" target="_blank" title="@lang('delegate::delivery.manage')">
                                <i class="las la-tools"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $delegates->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>
@endsection






