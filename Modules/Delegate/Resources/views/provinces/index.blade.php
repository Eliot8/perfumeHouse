@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{ translate('All Provinces') }}</h1>
        </div>
        @if(Auth::user()->user_type != 'Seller')
        <div class="col text-right">
            <button type="button" class="btn btn-circle btn-info" data-toggle="modal" data-target="#create-modal">
                <span>{{ translate('Add New Province') }}</span>
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
                <h5 class="mb-md-0 h6">{{ translate('All Provinces') }}</h5>
            </div>
        </div>
    
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="sm">#</th>
                        <th>{{ translate('Name') }}</th>
                        <th data-breakpoints="sm">{{ translate('Delivery man') }}</th>
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
                            <span class="btn-soft-danger btn-circle btn-sm" style="transition: all 0.3s ease;">{{ translate('Empty') }}</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <button type="button" class="btn btn-soft-primary btn-icon btn-circle btn-sm" data-toggle="modal" data-target="#edit-modal{{ $province->id }}" title="{{ translate('Edit') }}">
                                  <i class="las la-edit"></i>
                            </button>
                            <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm" data-toggle="modal" data-target="#delete-modal{{ $province->id }}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </button>
                            @include('delegate::provinces.edit')
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






