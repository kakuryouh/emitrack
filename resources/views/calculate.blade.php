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
                    <option value="Sepeda Motor (Bensin)">Sepeda Motor (Bensin)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="fuel">Fuel Type</label>
                <select id="fuel" name="fuel" required>
                    <option value="" disabled selected>Select your vehicle fuel type</option>
                    <option value="Pertalite">Pertalite (Ron 90)</option>
                    <option value="Pertamax">Pertamax (Ron 92)</option>
                    <option value="Pertamax Turbo">Pertamax Turbo (Ron 98)</option>
                    <option value="Pertamax Dex">Pertamax Dex (CN 53)</option>
                    <option value="Dexlite">Dexlite (CN 51)</option>
                    <option value="Solar">Solar/Bio Solar (CN 48)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="efficiency">Vehicle fuel efficiency</label>
                <select id="efficiency" name="efficiency" required>
                    <option value="" disabled selected>Select your vehicle fuel efficiency</option>
                    <option value="6">6KM/L</option>
                    <option value="8.7">8.7KM/L</option>
                    <option value="9.5">9.5KM/L</option>
                    <option value="10.6">10.6KM/L</option>
                    <option value="11.8">11.8KM/L</option>
                    <option value="13">13KM/L</option>
                    <option value="48.8">48.8KM/L (Motorcycle)</option>
                    <option value="52">52KM/L (Motorcycle)</option>
                    <option value="59">59KM/L (Motorcycle)</option>
                    <option value="63">63KM/L (Motorcycle)</option>
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