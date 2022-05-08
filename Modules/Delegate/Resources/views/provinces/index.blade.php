@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">@lang('delegate::delivery.provinces')</h1>
        </div>
        @if(Auth::user()->user_type != 'Seller')
        <div class="col text-right">
            <button type="button" class="btn btn-circle btn-info" data-toggle="modal" data-target="#create-modal">
                <span>@lang('delegate::delivery.add_province')</span>
            </button>
            @include('delegate::provinces.create')
        </div>
        @endif
    </div>
</div>
<br>

<div class="card">
    <form class="" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">@lang('delegate::delivery.provinces')</h5>
            </div>
        </div>
    
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="sm">#</th>
                        <th>{{ translate('Name') }}</th>
                        <th data-breakpoints="sm">@lang('delegate::delivery.delivery_man')</th>
                        <th data-breakpoints="sm">@lang('delegate::delivery.delegate_cost')</th>
                        <th data-breakpoints="sm">@lang('delegate::delivery.shipping_cost')</th>
                        <th data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($provinces as $key => $province)
                    <tr>
                        <td>{{ ($key+1) + ($provinces->currentPage() - 1)*$provinces->perPage() }}</td>
                        <td>
                            <div class="row gutters-5 w-100px w-md-100px mw-100">
                                <div class="col">
                                    <span class="text-muted text-truncate-2">{{ $province->name }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($province->delegates->count() > 0)
                            @foreach($province->delegates as $delegate)
                            <span class="btn-soft-success btn-circle btn-sm" style="transition: all 0.3s ease;">{{ $delegate->full_name }}</span>
                            @endforeach
                            @else
                            <span class="btn-soft-danger btn-circle btn-sm" style="transition: all 0.3s ease;">@lang('delegate::delivery.province_empty')</span>
                            @endif
                        </td>
                        <td>
                            {{ $province->delegate_cost ?? '0'}} $
                        </td>
                        <td>
                            @if($province->free_shipping)
                            <span class="badge badge-info badge-inline px-2">@lang('delegate::delivery.free_shipping')</span>
                            @else 
                             {{ $province->shipping_cost ?? '0' }} $
                            @endif
                        </td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('provinces.edit', $province->id) }}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm" data-toggle="modal" data-target="#delete-modal{{ $province->id }}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </button>
                             @component('delegate::components.delete', ['name' => 'provinces', 'id' => $province->id])@endcomponent
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $provinces->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
    <script type="text/javascript">
        $("[name=shipping_type]").on("change", function (){
        $(".flat_rate_shipping_div").hide();

        if($(this).val() == 'cost'){
            $(".flat_rate_shipping_div").show();
        }

    });
    </script>
@endsection





