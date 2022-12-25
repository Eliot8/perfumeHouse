<div class="modal fade" id="new-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="create_address_form" class="form-default" role="form" action="{{ route('addresses.store') }}" method="POST" onsubmit="event.preventDefault();">
                @csrf
                <div class="modal-body">
                    <div class="p-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Name')}}</label>
                            </div>
                            <div class="col-md-10">
                                <textarea class="form-control mb-3" placeholder="{{ translate('Your Name')}}" rows="2" name="name" required></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Address')}}</label>
                            </div>
                            <div class="col-md-10">
                                <textarea class="form-control mb-3" placeholder="{{ translate('Your Address')}}" rows="2" name="address" required></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <label>@lang('delegate::delivery.province')</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" data-placeholder="@lang('delegate::delivery.select_province')" name="province" id="province" required>
                                    <option value="">@lang('delegate::delivery.select_province')</option>
                                    @foreach (\Modules\Delegate\Entities\Province::all() as $key => $province)
                                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>@lang('delegate::delivery.zone')</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="zone" id="zone">
                                    
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Phone')}}</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3" id="phone" name="phone" value="{{ old('phone') }}" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>@lang('delegate::delivery.optional_phone')</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3" id="optional_phone" name="optional_phone" value="{{ old('optional_phone') }}" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
                            </div>
                        </div>
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-sm btn-primary" onclick="submitAddressForm()">{{translate('Save')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body" id="edit_modal_body">

            </div>
        </div>
    </div>
</div>

@section('script')
    <script type="text/javascript">
        @error('phone')
        AIZ.plugins.notify('danger', '{{ $errors->first("phone") }}');
        $('#new-address-modal').modal('show');
        @enderror
        @error('optional_phone')
        AIZ.plugins.notify('danger', '{{ $errors->first("optional_phone") }}');
        $('#new-address-modal').modal('show');
        @enderror
        function add_new_address(){
            $('#new-address-modal').modal('show');
        }

        function edit_address(address) {
            var url = '{{ route("addresses.edit", ":id") }}';
            url = url.replace(':id', address);
            
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                success: function (response) {
                    $('#edit_modal_body').html(response.html);
                    $('#edit-address-modal').modal('show');
                    AIZ.plugins.bootstrapSelect('refresh');

                    @if (get_setting('google_map') == 1)
                        var lat     = -33.8688;
                        var long    = 151.2195;

                        if(response.data.address_data.latitude && response.data.address_data.longitude) {
                            lat     = response.data.address_data.latitude;
                            long    = response.data.address_data.longitude;
                        }

                        initialize(lat, long, 'edit_');
                    @endif
                }
            });
        }
        
        $(document).on('change', '[name=country_id]', function() {
            var country_id = $(this).val();
            get_states(country_id);
        });

        $(document).on('change', '[name=state_id]', function() {
            var state_id = $(this).val();
            get_city(state_id);
        });
        
        function get_states(country_id) {
            $('[name="state"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-state')}}",
                type: 'POST',
                data: {
                    country_id  : country_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="state_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

        function get_city(state_id) {
            $('[name="city"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-city')}}",
                type: 'POST',
                data: {
                    state_id: state_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="city_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

        function get_zones(select){
             $.ajax({
                url: `/province/${select.value}/zone`,
                type: "GET",
                success: function(response) {
                    $('#zone_id').empty().append(response.options).selectpicker('refresh');
                }
            });
        }

        //
        $('#province').on('change', function() {
            $.ajax({
                url: `/province/${$(this).val()}/zone`,
                type: "GET",
                success: function(response) {
                    $('#zone').empty().append(response.options).selectpicker('refresh');
                }
            });
        });

        function submitAddressForm() {

            $.ajax({
                type: $('#create_address_form').attr('method'),
                url: $('#create_address_form').attr('action'),
                data: $('#create_address_form').serialize(),
                async: false,
                success: function(response) {
                    $('#new-address-modal').modal('hide');
                    AIZ.plugins.notify('success', response.message);
                    location.reload();
                },
                error: function(response) {
                    $.each(response.responseJSON, function(r, value) {
                        AIZ.plugins.notify('danger', value);
                    });
                }
            });
        }

        function submitAddressEditForm() {

            $.ajax({
                type: $('#edit_address_form').attr('method'),
                url: $('#edit_address_form').attr('action'),
                data: $('#edit_address_form').serialize(),
                async: false,
                success: function(response) {
                    $('#edit-address-modal').modal('hide');
                    AIZ.plugins.notify('success', response.message);
                    location.reload();
                },
                error: function(response) {
                    $.each(response.responseJSON, function(r, value) {
                        AIZ.plugins.notify('danger', value);
                    });
                }
            });
        }
    </script>

    
    @if (get_setting('google_map') == 1)
        @include('frontend.partials.google_map')
    @endif
@endsection