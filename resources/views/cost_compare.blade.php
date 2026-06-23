@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-card wide-card">
        <h2 style="text-align: center; margin-bottom: 2rem;">Cost Comparison</h2>

        <div id="result-container" style="display: none; background: #e6f9f0; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; text-align: center; border: 2px solid #34a853;">
            <h3>Comparison Result</h3>
            <div style="display: flex; justify-content: space-around; margin-top: 1rem;">
                <div>
                    <p class="label">Private Vehicle Cost</p>
                    <h4 style="color: #ea4335;">Rp <span id="res-private">0</span></h4>
                </div>
                <div>
                    <p class="label">Public Transport Cost</p>
                    <h4 style="color: #34a853;">Rp <span id="res-public">0</span></h4>
                </div>
            </div>
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #cce8dc;">
                <strong id="res-cheaper"></strong> is cheaper by Rp <span id="res-diff">0</span>!
            </div>
            <div style="margin-top: 1rem; font-size: 0.9rem; color: #555;">
                <strong>Route Breakdown:</strong><br>
                <span id="res-breakdown"></span>
            </div>
        </div>

        <form id="costForm" action="{{ url('/compare-cost') }}" method="POST">
            @csrf

            <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                
                <div style="flex: 1; min-width: 300px; padding-right: 1rem; border-right: 1px solid #eee;">
                    <h3 style="border-bottom: 2px solid #eee; padding-bottom: 0.5rem;">🚗 Private Vehicle</h3>

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
                            <option value="7.5">7.5L/100KM</option>
                            <option value="8.5">8.5L/100KM</option>
                            <option value="9.4">9.4L/100KM</option>
                            <option value="10.5">10.5/100KM</option>
                            <option value="11.5">11.5L/100KM</option>
                            <option value="15.5">15.5L/100KM</option>
                            <option value="2.049">48.8KM/L (Motorcycle)</option>
                            <option value="1.923">52KM/L (Motorcycle)</option>
                            <option value="1.694">59KM/L (Motorcycle)</option>
                            <option value="1.587">63KM/L (Motorcycle)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Parking & Tolls (Rp) <small>(Optional)</small></label>
                        <input type="number" name="parking_toll" class="form-control" value="0">
                    </div>
                </div>

                <div style="flex: 1; min-width: 300px;">
                    <h3 style="border-bottom: 2px solid #eee; padding-bottom: 0.5rem;">🚆 Public Transport Chain</h3>
                    
                    <p style="font-size: 0.9rem; color: #666; margin-top: 1rem;">Add your transport steps in order.</p>

                    <div id="transport-legs-container">
                        </div>

                    <button type="button" class="btn-outline" style="width: 100%; margin-top: 1rem;" onclick="addTransportLeg()">
                        + Add Transport
                    </button>
                </div>

            </div>

            <div class="button-group" style="margin-top: 3rem;">
                <button type="submit" class="btn-green">Compare Costs</button>
            </div>
        </form>
    </div>
</div>

<script>
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
    document.getElementById('costForm').addEventListener('submit', function(e) {
        e.preventDefault(); 
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerText;
        submitBtn.innerText = "Calculating...";
        submitBtn.disabled = true;

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('res-private').innerText = data.privateCost;
            document.getElementById('res-public').innerText = data.publicCost;
            document.getElementById('res-cheaper').innerText = data.cheaperOption;
            document.getElementById('res-diff').innerText = data.difference;
            document.getElementById('res-breakdown').innerText = data.publicBreakdown;

            document.getElementById('result-container').style.display = 'block';
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        })
        .catch(error => {
            console.error('Error:', error);
            alert("An error occurred while calculating.");
        })
        .finally(() => {
            submitBtn.innerText = originalText;
            submitBtn.disabled = false;
        });
    });

</script>
@endsection