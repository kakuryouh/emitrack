@extends('layouts.app')

@section('content')
<div class="container-calculate">

    <div class="form-box">
        <h2>Calculate Your Emission</h2>
        
        <form action="{{ route('calculate') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="origin">Origin</label>
                <input type="text" id="origin" name="origin" placeholder="e.g., Jakarta" required>
            </div>

            <div class="form-group">
                <label for="destination">Destination</label>
                <input type="text" id="destination" name="destination" placeholder="e.g., Bandung" required>
            </div>

            <div class="form-group">
                <label for="vehicle_model">Transportation Mode</label>
                <select id="vehicle_model" name="vehicle_model" required>
                    <option value="" disabled selected>Select your vehicle</option>
                    <option value="Mobil (Bensin)">Mobil (Bensin)</option>
                    <option value="Mobil (Diesel)">Mobil (Diesel)</option>
                    <option value="Mobil (Elektrik)">Mobil (Elektrik)</option>
                    <option value="Mobil (Hybrid)">Mobil (Hybrid)</option>
                    <option value="Sepeda Motor (Bensin)">Sepeda Motor (Bensin)</option>
                    <option value="Sepeda Motor (Listrik)">Sepeda Motor (Listrik)</option>
                    <option value="Sepeda">Sepeda</option>
                    <option value="Kereta Api">Kereta Api</option>
                    <option value="Kereta Listrik">Kereta Listrik</option>
                    <option value="Bus">Bus</option>
                </select>
            </div>

            @if (isset($error))
                <span>{{ $error }}</span>
            @endif

            <div class="form-group">
                <button type="submit" class="btn">Calculate</button>
            </div>
        </form>
    </div>    
</div>
@endsection