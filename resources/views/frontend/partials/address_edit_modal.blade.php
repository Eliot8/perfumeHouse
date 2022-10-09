<form class="form-default" role="form" action="{{ route('addresses.update', $address_data->id) }}" method="POST">
    @csrf
    <div class="p-3">
        <div class="row">
            <div class="col-md-2">
                <label>{{ translate('Name')}}</label>
            </div>
            <div class="col-md-10">
                <textarea class="form-control mb-3" placeholder="{{ translate('Your Name')}}" rows="2" name="name" required>{{ $address_data->name }}</textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <label>{{ translate('Address')}}</label>
            </div>
            <div class="col-md-10">
                <textarea class="form-control mb-3" placeholder="{{ translate('Your Address')}}" rows="2" name="address" required>{{ $address_data->address }}</textarea>
            </div>
        </div>
      
        <div class="row">
            <div class="col-md-2">
                <label>@lang('delegate::delivery.province')</label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" data-placeholder="@lang('delegate::delivery.select_province')" name="province" id="province_id" onchange="get_zones(this);" required>
                    <option value="">@lang('delegate::delivery.select_province')</option>
                    @foreach (\Modules\Delegate\Entities\Province::all() as $key => $province)
                        <option value="{{ $province->id }}" @if($address_data->province_id == $province->id) selected @endif>{{ $province->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>@lang('delegate::delivery.zone')</label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="zone" id="zone_id">
                    @foreach (Modules\Delegate\Entities\Zone::where('province_id', $address_data->province_id)->get() as $zone)
                    <optgroup label="{{ $zone->name }}">
                        @forelse($zone->neighborhoods as $item)
                        <option value="{{ $item->id }}" @if($address_data->zone_id == $item->id) selected @endif>{{ $item->name }}</option>
                        @empty
                        <option value="{{ $zone->id }}" @if($address_data->zone_id == $zone->id) selected @endif>{{ $zone->name }}</option>
                        @endforelse
                    </optgroup>
                    @endforeach
                </select>
            </div>
        </div>
        
        @if (get_setting('google_map') == 1)
            <div class="row">
                <input id="edit_searchInput" class="controls" type="text" placeholder="Enter a location">
                <div id="edit_map"></div>
                <ul id="geoData">
                    <li style="display: none;">Full Address: <span id="location"></span></li>
                    <li style="display: none;">Postal Code: <span id="postal_code"></span></li>
                    <li style="display: none;">Country: <span id="country"></span></li>
                    <li style="display: none;">Latitude: <span id="lat"></span></li>
                    <li style="display: none;">Longitude: <span id="lon"></span></li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-2" id="">
                    <label for="exampleInputuname">Longitude</label>
                </div>
                <div class="col-md-10" id="">
                    <input type="text" class="form-control mb-3" id="edit_longitude" name="longitude" value="{{ $address_data->longitude }}" readonly="">
                </div>
            </div>
            <div class="row">
                <div class="col-md-2" id="">
                    <label for="exampleInputuname">Latitude</label>
                </div>
                <div class="col-md-10" id="">
                    <input type="text" class="form-control mb-3" id="edit_latitude" name="latitude" value="{{ $address_data->latitude }}" readonly="">
                </div>
            </div>
        @endif
        
        <div class="row">
            <div class="col-md-2">
                <label>{{ translate('Phone')}}</label>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control mb-3" placeholder="{{ translate('880')}}" value="{{ $address_data->phone }}" name="phone" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>@lang('delegate::delivery.optional_phone')</label>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control mb-3" placeholder="" value="{{ $address_data->optional_phone }}" name="optional_phone" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required>
            </div>
        </div>

        <div class="form-group text-right">
            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
        </div>
    </div>
</form>
