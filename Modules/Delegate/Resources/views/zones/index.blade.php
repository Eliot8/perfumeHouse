@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">@lang('delegate::delivery.zones')</h1>
        </div>
        @if(Auth::user()->user_type != 'Seller')
        <div class="col text-right">
            <button type="button" class="btn btn-circle btn-info" data-toggle="modal" data-target="#create-modal">
                <span>@lang('delegate::delivery.add_zone')</span>
            </button>
            <button type="button" class="btn btn-circle btn-success" data-toggle="modal" data-target="#create-neighborhood-modal">
                <span>@lang('delegate::delivery.add_neighborhood')</span>
            </button>
            @include('delegate::zones.create')
            @include('delegate::components.create_neighborhood')
        </div>
        @endif
    </div>
</div>
<br>

<div class="card">
    <form class="" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">@lang('delegate::delivery.zones')</h5>
            </div>
        </div>
    
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="sm">#</th>
                        <th>{{ translate('Name') }}</th>
                        <th data-breakpoints="sm">@lang('delegate::delivery.province')</th>
                        <th data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($zones as $key => $zone)
                    <tr>
                        <td>{{ ($key+1) + ($zones->currentPage() - 1)*$zones->perPage() }}</td>
                        <td>
                            <div class="row gutters-5 w-100px w-md-100px mw-100">
                                <div class="col">
                                    <span class="text-muted text-truncate-2">{{ $zone->name }}</span>
                                </div>
                            </div>
                        </td>
                        <td><strong class="btn-soft-info btn-circle btn-sm" style="transition: all 0.3s ease;">{{ $zone->province ? $zone->province->name : '' }}</strong></td>
                        <td class="text-right">
                            <button type="button" class="btn btn-soft-success btn-icon btn-circle btn-sm" data-toggle="modal" data-target="#view-neighborhoods-modal{{ $zone->id }}" title="@lang('delegate::delivery.view_neighborhood')">
                                <i class="las la-list"></i>
                            </button>
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('zones.edit', $zone->id) }}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm" data-toggle="modal" data-target="#delete-modal{{ $zone->id }}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </button>
                            @component('delegate::components.neighborhoods', ['zone' => $zone])@endcomponent
                            @component('delegate::components.delete', ['name' => 'zones', 'id' => $zone->id])@endcomponent
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $zones->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    setInterval(() => {
        $('.province-select').each(function(i, element) {
            $(element).children(':first').children().eq(1).remove();
            $(element).children(':first').children().eq(2).remove();
        })
        
    }, 2000);
</script>
@endsection






