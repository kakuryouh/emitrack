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
                    <option value="Mobil (Listrik)">Mobil (Listrik)</option>
                    <option value="Mobil (Hybrid)">Mobil (Hybrid)</option>
                    <option value="Sepeda Motor (Bensin)">Sepeda Motor (Bensin)</option>
                    <option value="Sepeda Motor (Listrik)">Sepeda Motor (Listrik)</option>
                    <option value="Sepeda">Sepeda</option>
                </select>
            </div>

            <input type="hidden" name="createFlag" value="true">

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <span style="color: red">{{ $error }}</span>
                @endforeach
            @endif

            <div class="form-group" style="margin-top: 10px">
                <button type="submit" class="btn">Calculate</button>
            </div>
        </form>
        <div class="disclaimer">The calculation uses total average emission, real emission value may vary. These results may not be completely accurate and shouldn't be taken as the final answer.</div>
    </div>
</div>
@endsection