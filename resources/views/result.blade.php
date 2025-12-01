@extends('layouts.app')

@section('content')
    <div class="container-result">
        <div class="result-box">
            <h1>Your Estimated Carbon Emission</h1>

            <div class="emission-value">
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

            {{-- Bottom row: Vehicle details --}}
            <div class="result-details result-bottom">
                <div>
                    <div class="label">Vehicle Model:</div>
                    <div class="value">{{ $vehicleModel ?? '[Car Model]' }}</div>
                </div>
                <div>
                    <div class="label">Avarage Emission Rate:</div>
                    <div class="value">{{ $emissionRate ?? '--' }} g CO2/km</div>
                </div>
                <div>
                    <div class="label">Estimated travel time:</div>
                    <div class="value">{{ $duration['hour'] . ' Hour    ' . $duration['minute'] . ' Minute' ?? '-- Hour -- Minute' }}</div>
                </div>
                <div>
                    <div class="label">Travel Length:</div>
                    <div class="value">{{ $distance ?? '--' }} km</div>
                </div>
            </div>
        </div>

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
@endsection