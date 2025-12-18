@extends('layouts.app')

@section('content')

{{-- Add Leaflet CSS and JS locally for this page --}}
    <link rel="stylesheet" href="Leaflet\dist\leaflet.css" />
    <link rel="stylesheet" href="leaflet-routing-machine\dist\leaflet-routing-machine.css" />

    <div class="container-fluid" style="padding: 0 2rem;">
        
        <div class="result-box">
                
            <div class="map-container">
                <div id="map"></div>

                {{-- <div class="directions-box">
                    <h3>Route Directions</h3>
                    @foreach($routeSteps as $index => $step)
                        <div class="step-item">
                            <div class="step-icon">{{ $index + 1 }}.</div>
                            <div class="step-text">
                                {{ $step['instruction'] }}
                                <div style="color: #888; font-size: 0.8em;">
                                    {{ round($step['distance']) }}m
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div> --}}
            </div>

            <div class="data-container">
                
                <h1>Your Estimated Carbon Emission</h1>

                <div class="emission-value" style="color: {{ $mainColor }}">
                    {{ $totalEmission ?? '--' }} g CO2
                </div>

                <div class="result-details">
                    <div>
                        <div class="label">Origin:</div>
                        <div class="value">{{ $origin['name'] ?? '--' }}</div>
                    </div>
                    <div>
                        <div class="label">Destination:</div>
                        <div class="value">{{ $destination['name'] ?? '--' }}</div>
                    </div>
                </div>

                <div class="result-details result-bottom">
                    <div>
                        <div class="label">Vehicle Model:</div>
                        <div class="value">{{ $vehicleModel ?? '[Car Model]' }}</div>
                    </div>
                    <div>
                        <div class="label">Average Emission Rate:</div>
                        <div class="value">{{ $emissionRate ?? '--' }} g CO2/km</div>
                    </div>
                    <div>
                        <div class="label">Estimated travel time:</div>
                        <div class="value">{{ $duration ?? '-- Hour -- Minute' }}</div>
                    </div>
                    <div>
                        <div class="label">Travel Length:</div>
                        <div class="value">{{ $distance ?? '--' }} km</div>
                    </div>
                </div>

                @if(!empty($recommendations))
                <div class="recommendation-section">
                    
                    <div class="rec-cards-container">
                        @foreach($recommendations as $rec)
                            <div class="rec-card">
                                <div style="font-weight: bold; color: #2d8a64; margin-bottom: 0.5rem;">
                                    Try: {{ $rec['vehicle_type'] }}
                                </div>
                                <div style="font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    Emission: {{ $rec['emission'] }} g/km
                                </div>
                                <div style="font-size: 0.9rem; color: #555;">
                                    Total Emissions: <br>
                                    <span style="font-weight: bold; color: {{ $rec['color'] }}; font-size: 1.5rem;">
                                        {{ number_format($rec['emissionTotal'], 2) }} g CO2
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="rec-message">
                        {{ $recommendationMsg }}
                    </div>

                </div>
                @endif

            </div>
        </div>

        <div class="disclaimer">The calculation uses total average emission, real emission value may vary. These results may not be completely accurate and shouldn't be taken as the final result.</div>

        <div class="container-info-button-result">
            <a href="{{ url('/calculate') }}" class="btn-info">Hitung Kembali</a>
        </div>

        {{-- Recommendations Section --}}
        <div class="recommendations" style="max-width: 800px; margin: 2rem auto;">
            <h2>Cara Menurunkan Emisi Anda</h2>
            <p>Berikut adalah beberapa cara untuk mengurangi jejak karbon Anda:</p>
            <ul>
                <li>
                    <strong>Gunakan Transportasi Umum</strong> Bus dan kereta api sering kali memiliki jejak karbon yang lebih rendah per penumpang.
                    <a href="{{ url('/guide') }}">[Aplikasi TransJakarta]</a>
                </li>
                <li>
                    <strong>Coba Melakukan <i>Carbon Offsetting</i>:</strong> Anda dapat mengimbangi emisi Anda dengan mendukung / mendonasi proyek yang mengurangi gas rumah kaca.
                    <a href="https://lindungihutan.com/blog/5-proyek-carbon-offsetting-di-indonesia">[Proyek Carbon Offsetting]</a>
                </li>
                <li>
                    <strong>Kurangi Penggunaan Kendaraan Bermotor:</strong> Untuk jarak pendek, berjalan kaki atau bersepeda tidak menghasilkan emisi.
                </li>
            </ul>
        </div>

    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="leaflet-routing-machine\dist\leaflet-routing-machine.js"></script>
    <script src="Leaflet-encoded\Polyline.encoded.js"></script>

    <script>

        // Initialize Map
        var map = L.map('map');

        var originLat = {{ $origin['lat'] }};
        var originLng = {{ $origin['lng'] }};
        var destLat = {{ $destination['lat'] }};
        var destLng = {{ $destination['lng'] }};

        var routeCoords = @json($routeGeometry);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var polyline = L.Polyline.fromEncoded(routeCoords, {
            color: '#34a853', // Green color
            weight: 5,
            opacity: 0.8
        }).addTo(map);

        L.marker([originLat, originLng]).addTo(map)
            .bindPopup("<b>Start:</b> {{ $origin['name'] }}");

        L.marker([destLat, destLng]).addTo(map)
            .bindPopup("<b>End:</b> {{ $destination['name'] }}");

        map.fitBounds(polyline.getBounds(), { padding: [50, 50] });

    </script>

@endsection