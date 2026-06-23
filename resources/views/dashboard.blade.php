@extends('layouts.app')

@section('content')
<style>
    /* Dashboard styles */
    .dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; }
    .stat-card { background: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center; border-top: 4px solid #34a853; }
    .stat-value { font-size: 2rem; font-weight: bold; color: #333; margin: 0.5rem 0; }
    
    /* Badges */
    .badge-grid { display: flex; gap: 1.5rem; flex-wrap: wrap; }
    .badge-card { text-align: center; width: 120px; opacity: 0.4; filter: grayscale(100%); transition: 0.3s; }
    .badge-card.earned { opacity: 1; filter: grayscale(0%); }
    .badge-icon { font-size: 3rem; background: #e6f9f0; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; border: 2px solid #34a853; }
    
    /* Modal Styles */
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
    .modal-content { background: white; padding: 2rem; border-radius: 15px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
</style>

<div class="container" style="max-width: 1000px; margin: 2rem auto;">
    
    <div class="dashboard-header">
        <h2>Welcome back, {{ auth()->user()->name }}!</h2>
        <button class="btn-green" onclick="openModal()">+ Log Today's Travel</button>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <p style="color: #666; font-weight: bold;">Total Emissions</p>
            <div class="stat-value">{{ $totalEmissionsKg }} kg</div>
            <p style="font-size: 0.85rem; color: #ea4335;">Equivalent to burning <strong>{{ $coalEquivalent }} kg</strong> of coal.</p>
        </div>
        
        <div class="stat-card">
            <p style="color: #666; font-weight: bold;">Money Saved (via Public Transport)</p>
            <div class="stat-value" style="color: #34a853;">Rp {{ number_format($totalMoneySaved) }}</div>
            <p style="font-size: 0.85rem; color: #888;">Compared to driving a private vehicle.</p>
        </div>
    </div>

    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
        
        <div style="flex: 2; min-width: 400px; background: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 1rem;">Emissions Last 7 Days (kg CO2)</h3>
            <canvas id="emissionChart" height="100"></canvas>
        </div>

        <div style="flex: 1; min-width: 400px; display: flex; flex-direction: column; gap: 1rem;">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div style="background: white; padding: 3rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center;">
                    <p style="color: #888; font-size: 0.8rem; margin: 0;">Lowest Record</p>
                    <strong style="color: #34a853;">{{ number_format(($stats->min_emission ?? 0) / 1000, 2) }} kg</strong>
                </div>
                
                <div style="background: white; padding: 3rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center;">
                    <p style="color: #888; font-size: 0.8rem; margin: 0;">Highest Record</p>
                    <strong style="color: #ea4335;">{{ number_format(($stats->max_emission ?? 0) / 1000, 2) }} kg</strong>
                </div>

                <div style="background: white; padding: 3rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center; grid-column: span 2;">
                    <p style="color: #888; font-size: 0.8rem; margin: 0;">Average Daily Emission</p>
                    <strong style="font-size: 1.2rem;">{{ number_format(($stats->avg_emission ?? 0) / 1000, 2) }} kg</strong>
                </div>
            </div>

        </div>

        <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
            <div style="background: #e6f9f0; padding: 2rem; border-radius: 15px; border-bottom: 5px solid #34a853; border-left: 5px solid #34a853; min-width: 415px;">
                <p style="color: #666; font-size: 0.9rem; font-weight: bold; margin: 0;">Most Used Mode</p>
                <div style="font-size: 1.5rem; font-weight: bold; color: #2d8a64;">
                    {{ $mostUsed ? ucfirst($mostUsed->transport_type) : 'None Yet' }}
                </div>
                @if($mostUsed)
                    <p style="font-size: 0.8rem; color: #555; margin: 0;">Used {{ $mostUsed->count }} times</p>
                @endif
            </div>

            <div style="background: #e6f9f0; padding: 2rem; border-radius: 15px; border-bottom: 5px solid #34a853; border-left: 5px solid #34a853; min-width: 415px;">
                <p style="color: #666; font-size: 0.9rem; font-weight: bold; margin: 0;">Total distance</p>
                <div style="font-size: 1.5rem; font-weight: bold; color: #2d8a64;">
                    {{ $totalDistanceKm ? : 0}} Km
                </div>
            </div>
        </div>


    </div>

    <h3>Your Achievements</h3>
    <div class="badge-grid">
        <div class="badge-card {{ $badges['first_trip'] ? 'earned' : '' }}">
            <div class="badge-icon">🌱</div>
            <strong>First Step</strong>
            <p style="font-size: 0.8rem; color:#666;">Log your first trip.</p>
        </div>
        
        <div class="badge-card {{ $badges['public_streak_7'] ? 'earned' : '' }}">
            <div class="badge-icon">🔥</div>
            <strong>7-Day Streak</strong>
            <p style="font-size: 0.8rem; color:#666;">Use public transport 7 days in a row.</p>
        </div>

        <div class="badge-card {{ $badges['eco_warrior'] ? 'earned' : '' }}">
            <div class="badge-icon">🌍</div>
            <strong>Eco Warrior</strong>
            <p style="font-size: 0.8rem; color:#666;">Keep weekly emissions under 5kg.</p>
        </div>
    </div>
</div>

<div class="modal-overlay" id="logModal">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 1rem; margin-bottom: 1rem;">
            <h3>Log Daily Transport</h3>
            <span style="cursor: pointer; font-size: 1.5rem;" onclick="closeModal()">&times;</span>
        </div>

        <form action="{{ url('/log-travel') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="log_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="form-group">
                <label>Transportation Type</label>
                <select name="transport_type" id="transport_type" class="form-control" onchange="toggleTransportForms()" required>
                    <option value="" disabled selected>Select...</option>
                    <option value="private">Private Vehicle</option>
                    <option value="public">Public Transport Chain</option>
                </select>
            </div>

            <div id="private-form" style="display: none; background: #f9f9f9; padding: 1rem; border-radius: 8px;">
                <div class="form-group">
                    <label for="vehicle_model">Transportation Mode</label>
                    <select id="vehicle_model" name="vehicle_model" required>
                        <option value="" disabled selected>Select your vehicle</option>
                        <option value="Mobil (Bensin)">Mobil (Bensin)</option>
                        <option value="Mobil (Diesel)">Mobil (Diesel)</option>
                        <option value="Sepeda Motor (Bensin)">Sepeda Motor (Bensin)</option>
                    </select>
                </div>
                
                <div class="form-group" style="margin-top: 1rem;">
                    <label>Total Distance (km)</label>
                    <input type="number" step="0.1" name="private_distance" class="form-control" required>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
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

                <div class="form-group">
                    <label>Parking & Tolls (Rp) <small>(Optional)</small></label>
                    <input type="number" name="parking_toll" class="form-control" value="0">
                </div>
            </div>

            <div id="public-form" style="display: none; background: #e6f9f0; padding: 1rem; border-radius: 8px;">
                <div id="transport-legs-container"></div>
                <button type="button" class="btn-outline" style="width: 100%; margin-top: 1rem;" onclick="addTransportLeg()">+ Add Leg (Train/Bus/Grab)</button>
            </div>

            <button type="submit" class="btn-green" style="width: 100%; margin-top: 2rem;">Save Log</button>
        </form>
    </div>
</div>

<script>
    function openModal() { document.getElementById('logModal').style.display = 'flex'; }
    function closeModal() { document.getElementById('logModal').style.display = 'none'; }

    function toggleTransportForms() {
        const type = document.getElementById('transport_type').value;
        const privateForm = document.getElementById('private-form');
        const publicForm = document.getElementById('public-form');

        const privateInputs = privateForm.querySelectorAll('input, select');
        const publicInputs = publicForm.querySelectorAll('input, select');

        if (type === 'private') {
            privateForm.style.display = 'block';
            privateInputs.forEach(input => input.disabled = false);
            
            publicForm.style.display = 'none';
            publicInputs.forEach(input => input.disabled = true);

        } else if (type === 'public') {
            publicForm.style.display = 'block';
            publicInputs.forEach(input => input.disabled = false);
            
            privateForm.style.display = 'none';
            privateInputs.forEach(input => input.disabled = true);

            if (legCount === 0) addTransportLeg();
            
        } else {
            privateForm.style.display = 'none';
            publicForm.style.display = 'none';
            privateInputs.forEach(input => input.disabled = true);
            publicInputs.forEach(input => input.disabled = true);
        }
    }

    let legCount = 0;
    function addTransportLeg() {
        const container = document.getElementById('transport-legs-container');
        
        const legDiv = document.createElement('div');
        legDiv.style.border = "1px solid #ddd";
        legDiv.style.padding = "1rem";
        legDiv.style.borderRadius = "8px";
        legDiv.style.marginTop = "1rem";
        legDiv.style.position = "relative";
        legDiv.id = `leg-${legCount}`;

        legDiv.innerHTML = `
            <span style="position: absolute; top: 10px; right: 10px; cursor: pointer; color: red;" onclick="removeLeg(${legCount})">✖</span>
            
            <div class="form-group">
                <label>Transport Type</label>
                <select name="public_legs[${legCount}][type]" class="form-control" required>
                    <option value="" disabled selected>Select mode...</option>
                    <option value="MRT">MRT</option>
                    <option value="KRL">KRL</option>
                    <option value="TransJakarta">TransJakarta</option>
                    <option value="Grab Motor">Grab Motor</option>
                    <option value="Grab Mobil">Grab Mobil</option>
                    <option value="Gojek Motor">Gojek Motor</option>
                    <option value="Gojek Mobil">Gojek Mobil</option>
                </select>
            </div>

            <div id="grab-inputs-${legCount}" style="display: flex;">
                <div class="form-group">
                    <label>Ride Distance (km)</label>
                    <input type="number" step="0.1" name="public_legs[${legCount}][distance]" class="form-control" placeholder="e.g., 5.5" required>
                </div>
            </div>
        `;

        container.appendChild(legDiv);
        legCount++;
    }

    function removeLeg(id) {
        const legDiv = document.getElementById(`leg-${id}`);
        legDiv.remove();
    }

    window.onload = function() {
        addTransportLeg();
    };
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('emissionChart').getContext('2d');
        
        const labels = @json($chartLabels);
        const data = @json($chartValues);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Emission (kg CO2)',
                    data: data,
                    backgroundColor: '#34a853',
                    borderRadius: 5,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5] }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection