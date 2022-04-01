@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">@lang('delegate::delivery.delegates')</h1>
        </div>
        @if(Auth::user()->user_type != 'Seller')
        <div class="col text-right">
            <a href="{{ route('delegates.create') }}" class="btn btn-circle btn-info">
                <span>@lang('delegate::delivery.add_new_delegate')</span>
            </a>
        </div>
        @endif
    </div>
</div>
<br>

<div class="card">
    <form class="" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">@lang('delegate::delivery.delegates')</h5>
            </div>
        </div>
    
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="lg">#</th>
                        <th>{{ translate('Name') }}</th>
                        <th data-breakpoints="sm">{{ translate('Phone') }}</th>
                        <th data-breakpoints="md">{{ translate('Email') }}</th>
                        <th data-breakpoints="md">@lang('delegate::delivery.province')</th>
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
                        <td>{{ $delegate->phone_number }}</td>
                        <td>{{ $delegate->email }}</td>
                        <td>
                            <strong class="btn-soft-info btn-circle btn-sm" style="transition: all 0.3s ease;">{{ \DB::table('provinces')->where('id', $delegate->province_id)->first()->name }}</strong>
                        </td>
                        <td class="text-right">
                            {{-- <a class="btn btn-soft-success btn-icon btn-circle btn-sm"  href="" target="_blank" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a> --}}
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('delegates.edit', $delegate->id) }}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm" data-toggle="modal" data-target="#delete-modal{{ $delegate->id }}" data-id="{{ $delegate->id }}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </button>
                            @component('delegate::components.delete', ['name' => 'delegates', 'id' => $delegate->id])@endcomponent
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






