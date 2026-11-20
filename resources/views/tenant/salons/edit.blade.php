@extends('layouts.tenant')

@section('title', 'Edit Store')
@section('page-title', 'Edit Store: ' . $salon->salon_name)

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('tenant.salons.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 14px;">
            ← Back to Stores
        </a>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger">
            <div>
                <strong>Please fix the following errors:</strong>
                <ul style="margin-top: 6px; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('tenant.salons.update', $salon->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 24px;">
            
            <!-- Left Card: Basic & Address Details -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">🏪 Store Details & Location</h3>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Store / Salon Name <span style="color:red;">*</span></label>
                    <input type="text" name="salon_name" id="salon_name" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;" value="{{ old('salon_name', $salon->salon_name) }}" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Contact Phone</label>
                        <input type="text" name="salon_phone" id="salon_phone" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;" value="{{ old('salon_phone', $salon->salon_phone) }}">
                    </div>
                    <div>
                        <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Contact Email</label>
                        <input type="email" name="salon_email" id="salon_email" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;" value="{{ old('salon_email', $salon->salon_email) }}">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Address Line 1</label>
                    <input type="text" name="address_1" id="address_1" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;" value="{{ old('address_1', $salon->address_1) }}">
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Address Line 2 (Optional)</label>
                    <input type="text" name="address_2" id="address_2" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;" value="{{ old('address_2', $salon->address_2) }}">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Country</label>
                        <select name="country_id" id="country_id" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;">
                            <option value="">-- Select Country --</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id', $salon->country_id) == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }} ({{ $country->short_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">State / Territory</label>
                        <select name="state_id" id="state_id" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;">
                            <option value="">-- Select State --</option>
                            @foreach($states as $st)
                                <option value="{{ $st->id }}" {{ old('state_id', $salon->state_id) == $st->id ? 'selected' : '' }}>
                                    {{ $st->name }} ({{ $st->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">City</label>
                        <input type="text" name="city" id="city" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;" value="{{ old('city', $salon->city) }}">
                    </div>
                    <div>
                        <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Zip / Postal Code</label>
                        <input type="text" name="zipcode" id="zipcode" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;" value="{{ old('zipcode', $salon->zipcode) }}">
                    </div>
                </div>

                <!-- Geocoding Button Section -->
                <div style="margin-bottom: 16px; padding: 12px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <span style="font-size: 13px; font-weight: 700; color: var(--text-main); display: block;">📍 GPS Coordinates</span>
                            <span style="font-size: 12px; color: var(--text-muted);">Automatically compute latitude & longitude from the address above.</span>
                        </div>
                        <button type="button" id="btnGeocode" onclick="geocodeAddress()" style="background: linear-gradient(135deg, #4f46e5, #06b6d4); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(79, 70, 229, 0.3);">
                            <span id="btnGeocodeIcon">🎯</span> <span id="btnGeocodeText">Get Latitude/Longitude</span>
                        </button>
                    </div>
                    <div id="geocodeMessage" style="display: none; margin-top: 8px; font-size: 12px; font-weight: 600;"></div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Latitude (GPS)</label>
                        <input type="text" name="salon_lat" id="salon_lat" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family: monospace;" value="{{ old('salon_lat', $salon->salon_lat) }}">
                    </div>
                    <div>
                        <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Longitude (GPS)</label>
                        <input type="text" name="salon_long" id="salon_long" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family: monospace;" value="{{ old('salon_long', $salon->salon_long) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Store Status</label>
                    <select name="status" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;">
                        <option value="1" {{ old('status', (string)$salon->status) === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', (string)$salon->status) === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Right Card: 7-Day Operating Hours -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">⏰ 7-Day Operating Hours</h3>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Open Time</th>
                                <th>Close Time</th>
                                <th style="text-align: center;">Closed?</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $existingTimings = $existingTimings ?? ($salon->timings ? $salon->timings->keyBy("day_of_week") : collect()); $daysList = $days ?? $daysOfWeek ?? ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"]; @endphp @foreach($daysList as $index => $day)
                                @php
                                    $timing = $existingTimings->get($day);
                                    $openVal = $timing && $timing->open_time ? substr($timing->open_time, 0, 5) : '09:00';
                                    $closeVal = $timing && $timing->close_time ? substr($timing->close_time, 0, 5) : '21:00';
                                    $isClosed = $timing ? $timing->is_closed : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $day }}</strong>
                                        <input type="hidden" name="timings[{{ $index }}][day_name]" value="{{ $day }}">
                                    </td>
                                    <td>
                                        <input type="time" name="timings[{{ $index }}][open_time]" id="open_{{ $index }}" value="{{ old("timings.$index.open_time", $openVal) }}" class="form-input" style="padding: 6px 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px;" {{ $isClosed ? 'disabled' : '' }}>
                                    </td>
                                    <td>
                                        <input type="time" name="timings[{{ $index }}][close_time]" id="close_{{ $index }}" value="{{ old("timings.$index.close_time", $closeVal) }}" class="form-input" style="padding: 6px 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px;" {{ $isClosed ? 'disabled' : '' }}>
                                    </td>
                                    <td style="text-align: center;">
                                        <input type="checkbox" name="timings[{{ $index }}][is_closed]" value="1" id="closed_{{ $index }}" {{ old("timings.$index.is_closed", $isClosed) ? 'checked' : '' }} onchange="toggleTiming({{ $index }})">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 28px; display: flex; justify-content: flex-end; gap: 12px;">
                    <a href="{{ route('tenant.salons.index') }}" style="padding: 12px 20px; border-radius: 8px; border: 1px solid var(--border); background: white; color: var(--text-muted); text-decoration: none; font-weight: 600;">Cancel</a>
                    <button type="submit" class="btn-primary" style="padding: 12px 24px; border-radius: 8px; background: var(--primary); color: white; border: none; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);">
                        Update Store Details
                    </button>
                </div>
            </div>

        </div>
    </form>
@endsection

@section('scripts')
<script>
    function toggleTiming(index) {
        const isClosed = document.getElementById('closed_' + index).checked;
        const openInput = document.getElementById('open_' + index);
        const closeInput = document.getElementById('close_' + index);
        openInput.disabled = isClosed;
        closeInput.disabled = isClosed;
    }

    document.getElementById('country_id').addEventListener('change', function() {
        const countryId = this.value;
        const stateSelect = document.getElementById('state_id');
        stateSelect.innerHTML = '<option value="">Loading states...</option>';

        if (!countryId) {
            stateSelect.innerHTML = '<option value="">-- Select State --</option>';
            return;
        }

        fetch(`/countries/${countryId}/states`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Select State --</option>';
                data.forEach(st => {
                    html += `<option value="${st.id}">${st.name} (${st.code || ''})</option>`;
                });
                stateSelect.innerHTML = html;
            })
            .catch(err => {
                console.error(err);
                stateSelect.innerHTML = '<option value="">-- Select State --</option>';
            });
    });

    // Geocoding Function
    function geocodeAddress() {
        const btn = document.getElementById('btnGeocode');
        const btnText = document.getElementById('btnGeocodeText');
        const msg = document.getElementById('geocodeMessage');

        const address1 = document.getElementById('address_1').value.trim();
        const city = document.getElementById('city').value.trim();
        const zipcode = document.getElementById('zipcode').value.trim();
        
        const stateElem = document.getElementById('state_id');
        const state = stateElem.selectedIndex > 0 ? stateElem.options[stateElem.selectedIndex].text.split('(')[0].trim() : '';

        const countryElem = document.getElementById('country_id');
        const country = countryElem.selectedIndex > 0 ? countryElem.options[countryElem.selectedIndex].text.split('(')[0].trim() : '';

        if (!address1 && !city) {
            msg.style.display = 'block';
            msg.style.color = '#dc2626';
            msg.innerText = '⚠️ Please enter Address Line 1 and City first.';
            return;
        }

        // Build query string
        const parts = [address1, city, state, zipcode, country].filter(p => p.length > 0);
        const query = parts.join(', ');

        btn.disabled = true;
        btnText.innerText = 'Locating...';
        msg.style.display = 'block';
        msg.style.color = '#4f46e5';
        msg.innerText = `🔍 Searching coordinates for "${query}"...`;

        // Free Nominatim OpenStreetMap Geocoding API
        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btnText.innerText = 'Get Latitude/Longitude';

                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat).toFixed(6);
                    const lon = parseFloat(data[0].lon).toFixed(6);

                    document.getElementById('salon_lat').value = lat;
                    document.getElementById('salon_long').value = lon;

                    msg.style.color = '#16a34a';
                    msg.innerHTML = `✅ Coordinates found: <strong>Lat: ${lat}, Long: ${lon}</strong>`;
                } else {
                    // Fallback to broader search (City + State + Country)
                    const fallbackQuery = [city, state, country].filter(p => p.length > 0).join(', ');
                    const fallbackUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fallbackQuery)}&limit=1`;

                    fetch(fallbackUrl)
                        .then(r => r.json())
                        .then(fallbackData => {
                            if (fallbackData && fallbackData.length > 0) {
                                const lat = parseFloat(fallbackData[0].lat).toFixed(6);
                                const lon = parseFloat(fallbackData[0].lon).toFixed(6);

                                document.getElementById('salon_lat').value = lat;
                                document.getElementById('salon_long').value = lon;

                                msg.style.color = '#d97706';
                                msg.innerHTML = `📍 Approximate coordinates found for city area: <strong>Lat: ${lat}, Long: ${lon}</strong>`;
                            } else {
                                msg.style.color = '#dc2626';
                                msg.innerText = '❌ Unable to find coordinates for this address. Please verify address details.';
                            }
                        })
                        .catch(() => {
                            msg.style.color = '#dc2626';
                            msg.innerText = '❌ Geocoding lookup failed. Please check your internet connection.';
                        });
                }
            })
            .catch(err => {
                btn.disabled = false;
                btnText.innerText = 'Get Latitude/Longitude';
                msg.style.color = '#dc2626';
                msg.innerText = '❌ Geocoding service error. Please enter coordinates manually if offline.';
            });
    }
</script>
@endsection
