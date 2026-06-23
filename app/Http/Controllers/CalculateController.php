<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\History;
use App\Models\Fuel;
use App\Models\PublicTransport;
use Carbon\Traits\Difference;
use App\Models\TravelLog;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class CalculateController extends Controller
{

    public function viewCalculator(){
        return view('calculate');
    }

    public function viewCompare(){
        return view('cost_compare');
    }

    public function getCoordinates($query){
        $response = Http::withHeaders([
            'User-Agent' => 'EmiTrackGeoCoder'
        ])->get('https://nominatim.openstreetmap.org/search', [
            'q' => $query,
            'format' => 'json',
            'limit' => 1,
            'countrycodes' => 'id' 
        ]);
        return $response->json()[0] ?? null;
    }

    public function getRoute($originLat, $originLng, $destLat, $destLng, $profile, $options){
        $apiKey = env('ORS_API_KEY');
        $baseUrl = "https://api.openrouteservice.org/v2/directions/";

        $requestBody = [
            'coordinates' => [
                [$originLng, $originLat],
                [$destLng, $destLat],
            ],
        ];

        if (!empty($options)) {
            $requestBody['options'] = $options;
        }

        $response = Http::withHeaders([
            'Authorization' => $apiKey,
            'Content-Type'  => 'application/json',
        ])->post($baseUrl . $profile, $requestBody);

        return $response->json();
    }

    public function calculateEmission($distance, $fuelname, $fuelefficiency, $oxidationfactor, $costFlag = false){
        $fuel = DB::table('fuels')->where('fuel_name', $fuelname)->first();

        $activity = $distance/$fuelefficiency;
        $volumetricEmissionFactor = $fuel->density * $fuel->ncv * $fuel->carbon_emission_factor * $oxidationfactor;

        $emission = round($activity * $volumetricEmissionFactor, 3);

        if($costFlag){

            $cost = $fuel->price * $activity;

            return [$emission, $cost];

        }

        return $emission;
    }

    public function calculate(Request $request){
        // dd($request);
        $profile = "driving-car";
        $options = [];
        
        $originName = $request->input('origin');
        $destinationName = $request->input('destination');
        $vehicle = $request->input('vehicle_model');

        $originData = $this->getCoordinates($originName);
        $destData = $this->getCoordinates($destinationName);

        if (!$originData || !$destData) {
            return back()->withErrors(['error' => 'Could not find one of the locations. Try adding the city name (e.g., "Binus Alam Sutera, Tangerang").']);
        }

        $originLat = (float)$originData['lat'];
        $originLng = (float)$originData['lon'];
        $destLat = (float)$destData['lat'];
        $destLng = (float)$destData['lon'];

        if (str_contains($vehicle, 'Mobil') || $vehicle == "Bus") {
            $profile = "driving-car";
        }
        elseif (str_contains($vehicle, 'Sepeda Motor')) {
            $profile = "driving-car";
            
            $options = [
                "avoid_features" => ["tollways", "highways"] 
            ];
        } 
        elseif (str_contains($vehicle, 'Sepeda')) {
            $profile = "cycling-regular"; 
        } 
        else {
            return back()->withErrors(['error' => 'Please select a valid vehicle type.']);
        }

        if (!empty($options)) {
            $requestBody['options'] = $options;
        }

        $routeData = $this->getRoute($originLat, $originLng, $destLat, $destLng, $profile, $options);

        if (isset($routeData['routes'][0]['summary'])) {
            // Geometry for routes
            $geometry = $routeData['routes'][0]['geometry'];
            $steps = $routeData['routes'][0]['segments'][0]['steps'];
            $summary = $routeData['routes'][0]['summary'];
            
            $distanceKm = round($summary['distance'] / 1000, 2);
            $durationSeconds = $summary['duration'];
            
            // Emission data
            $EmissionsList = DB::table('emission')->select('vehicle_type', 'average_emission')->get();
            $MainEmission = $EmissionsList->firstWhere('vehicle_type', $vehicle)->average_emission;

            $totalEmission = $this->calculateEmission($distanceKm, $request->fuel, $request->efficiency, 0.99);

            // Reccomendation data
            $Recommendation = [];
            $ReccomendationMsg = "";

            if($distanceKm < 3 && $vehicle !== "Sepeda"){

                $Recommendation[] = [
                    'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Jalan Kaki')->vehicle_type,
                    'emission' => $EmissionsList->firstWhere('vehicle_type', 'Jalan Kaki')->average_emission,
                    'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Jalan Kaki')->average_emission),
                ];

                $Recommendation[] = [
                    'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda')->vehicle_type,
                    'emission' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda')->average_emission,
                    'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Sepeda')->average_emission),
                ];

                $ReccomendationMsg = "Untuk perjalanan pendek disarankan untuk berjalan kaki atau menggunakan sepeda. berjalan kaki atau bersepeda dapat membantu meningktakan kesehatan tubuh dan jauh lebih sehat dibanding $vehicle.";

            }elseif($distanceKm < 3 && $vehicle === "Sepeda"){
                $Recommendation[] = [
                    'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Jalan Kaki')->vehicle_type,
                    'emission' => $EmissionsList->firstWhere('vehicle_type', 'Jalan Kaki')->average_emission,
                    'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Jalan Kaki')->average_emission),
                ];

                $ReccomendationMsg = "Jika waktu tempuh bukan masalah, berjalan kaki bisa menjadi alternatif untuk perjalanan ini.";

            }elseif($distanceKm >= 3 && $distanceKm < 8 && str_contains($vehicle, "Mobil")){
                $Recommendation[] = [
                    'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda')->vehicle_type,
                    'emission' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda')->average_emission,
                    'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Sepeda')->average_emission),
                ];

                $Recommendation[] = [
                    'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->vehicle_type,
                    'emission' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->average_emission,
                    'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->average_emission),
                ];

                $ReccomendationMsg = "Jika waktu tempuh tidak menjadi masalah, bersepeda merupakan alternatif paling bagus untuk perjalanan ini dibanding $vehicle. Tetapi jika waktu tempuh penting maka, sepeda motor listrik juga bisa menjadi alternatif untuk perjalanan ini.";

            }elseif($distanceKm >= 3 && $distanceKm < 8 && !str_contains($vehicle, "Mobil")){
                if ($vehicle === "Sepeda Motor (Listrik)"){
                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Sepeda')->average_emission),
                    ];

                    $ReccomendationMsg = "Jika waktu tempuh tidak menjadi masalah, sepeda dapat menjadi alternatif baik dibanding $vehicle. waktu perjalanan akan lebih lama tetapi bersepeda juga dapat meningkatkan kesehatan tubuh.";

                }elseif ($vehicle === "Sepeda Motor (Bensin)"){
                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Sepeda')->average_emission),
                    ];

                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->average_emission),
                    ];

                    $ReccomendationMsg = "Jika waktu tempuh tidak menjadi masalah, bersepeda merupakan alternatif paling bagus untuk perjalanan ini dibanding $vehicle. Tetapi jika waktu tempuh penting maka, sepeda motor listrik juga bisa menjadi alternatif untuk perjalanan ini.";

                }

            }elseif($distanceKm >= 8 && $distanceKm < 15 && str_contains($vehicle, "Mobil")){
                if ($vehicle === "Mobil (Listrik)"){
                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission),
                    ];

                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->average_emission),
                    ];

                    $ReccomendationMsg = "Jika ada kereta yang memiliki destinasi sama atau tidak membutuhkan fleksibilitas untuk membawa barang-barang yang berat, kereta listrik adalah alternatif terbaik untuk perjalanan panjang seperti ini. Sepeda motor listrik juga dapat menjadi alternatif untuk perjalanan ini.";

                }else{
                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission),
                    ];

                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Mobil (Listrik)')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Mobil (Listrik)')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Mobil (Listrik)')->average_emission),
                    ];

                    $ReccomendationMsg = "Jika ada kereta yang memiliki destinasi yang sama atau tidak membutuhkan fleksibilitas untuk membawa barang-barang yang berat, kereta listrik adalah alternatif terbaik untuk perjalanan panjang seperti ini. Mobil Listrik juga dapat menjadi alternatif untuk perjalanan ini dan memberikan fleksebilitas seperti yang dimiliki $vehicle";

                }

            }elseif($distanceKm >= 8 && $distanceKm < 15 && !str_contains($vehicle, "Mobil")){
                if ($vehicle === "Sepeda Motor (Listrik)"){
                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission),
                    ];

                    $ReccomendationMsg = "Jika ada kereta yang memiliki destinasi yang sama, kereta listrik adalah pilihan yang bagus untuk perjalanan jarak menengah.";

                }elseif ($vehicle === "Sepeda Motor (Bensin)"){
                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission),
                    ];

                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->average_emission),
                    ];

                    $ReccomendationMsg = "Jika ada kereta yang memiliki destinasi yang sama, kereta listrik adalah pilihan yang bagus untuk perjalanan jarak menengah. Sepeda motor listrik juga bisa menjadi alternatif $vehicle yang lebih ramah lingkungan.";
                }

            }elseif($distanceKm >= 15 && !str_contains($vehicle, "Mobil")){
                if ($vehicle === "Sepeda Motor (Listrik)"){
                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission),
                    ];

                    $ReccomendationMsg = "Jika ada kereta yang memiliki destinasi yang sama, kereta listrik adalah pilihan yang bagus untuk perjalanan jarak jauh seperti ini.";

                }elseif ($vehicle === "Sepeda Motor (Bensin)"){
                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission),
                    ];

                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->average_emission),
                    ];

                    $ReccomendationMsg = "Jika ada kereta yang memiliki destinasi yang sama, kereta listrik adalah pilihan yang bagus untuk perjalanan jarak jauh seperti ini. Sepeda Motor listrik juga bisa menjadi alternatif $vehicle yang lebih ramah lingkungan.";

                }

            }elseif($distanceKm >= 15 && str_contains($vehicle, "Mobil")){
                if ($vehicle === "Mobil (Listrik)"){
                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission),
                    ];

                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Sepeda Motor (Listrik)')->average_emission),
                    ];

                    $ReccomendationMsg = "Jika ada kereta yang memiliki destinasi yang sama, kereta listrik adalah pilihan yang bagus untuk perjalanan jarak jauh seperti ini. Sepeda Motor listrik juga bisa menjadi alternatif $vehicle yang lebih ramah lingkungan.";

                }else{
                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission),
                    ];
                    
                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Mobil (Listrik)')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Mobil (Listrik)')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Mobil (Listrik)')->average_emission),
                    ];

                    $ReccomendationMsg = "Jika ada kereta yang memiliki destinasi yang sama, kereta listrik adalah pilihan yang bagus untuk perjalanan jarak jauh seperti ini. Mobil listrik juga bisa menjadi alternatif $vehicle yang lebih ramah lingkungan.";

                }
            }

            if($Recommendation){
                $comparisonList = [];

                $comparisonList['main'] = round(($MainEmission*$distanceKm), 2);

                foreach ($Recommendation as $index => $rec) {
                    $comparisonList["rec_$index"] = $rec['emissionTotal'];
                }

                $minValue = min($comparisonList);
                $maxValue = max($comparisonList);

                // Assign Color for each emission
                $assignColor = function($value) use ($minValue, $maxValue) {

                    if ($value == $minValue) return '#34a853'; 

                    if ($value == $maxValue) return '#ea4335';

                    return '#f9ab00';
                };

                // User vehicle choice color
                $mainColor = $assignColor($comparisonList['main']);

                foreach ($Recommendation as $index => &$rec) {
                    $recTotal = $comparisonList["rec_$index"];
                    $rec['color'] = $assignColor($recTotal);
                }

                unset($rec);

                return view('result', [
                    'origin' => [
                        'name' => $originData['display_name'],
                        'lat' => $originLat,
                        'lng' => $originLng
                    ],
                    'destination' => [
                        'name' => $destData['display_name'],
                        'lat' => $destLat,
                        'lng' => $destLng
                    ],
                    'distance' => $distanceKm,
                    'duration' => gmdate("H \h i \m", $durationSeconds),
                    'emissionRate' => round($totalEmission/$distanceKm, 3),
                    'totalEmission' => $totalEmission,
                    'vehicleModel' => $request->input('vehicle_model'),
                    'fuel' => $request->input('fuel'),
                    'efficiency' => $request->input('efficiency'),
                    'mainColor' => $mainColor,
                    'recommendations' => $Recommendation,
                    'recommendationMsg' => $ReccomendationMsg,

                    'routeGeometry' => $geometry,
                    'routeSteps' => $steps
                ]);
            }

            return view('result', [
                'origin' => [
                    'name' => $originData['display_name'],
                    'lat' => $originLat,
                    'lng' => $originLng
                ],
                'destination' => [
                    'name' => $destData['display_name'],
                    'lat' => $destLat,
                    'lng' => $destLng
                ],
                'distance' => $distanceKm,
                'duration' => gmdate("H \h i \m", $durationSeconds),
                'emissionRate' => round($totalEmission/$distanceKm, 3),
                'totalEmission' => $totalEmission,
                'vehicleModel' => $request->input('vehicle_model'),
                'mainColor' => '#34a853',
                'recommendations' => $Recommendation,
                'recommendationMsg' => $ReccomendationMsg,

                'routeGeometry' => $geometry,
                'routeSteps' => $steps
            ]);

        } else {
            return back()->withErrors(['error' => 'Route could not be calculated between these points.']);
        }
    }

    public function calculatePublicTransport($type, $distance){
        $transport = DB::table('publictransports')->where('name', $type)->first();

        $publicEmission = round($transport->emission_factor_pkm * $distance, 3);
        $publicCost = 0;
        
        if($transport->name == "MRT"){
            $publicCost = $transport->base_price + ($transport->price_increase * floor($distance/12));

        }elseif($transport->name == "KRL"){

            if($distance > 25){
                $publicCost = $transport->base_price + ($transport->price_increase * floor(($distance-25)/10));

            }else{
                $publicCost = $transport->base_price;
            }
        }else{
            $publicCost = $transport->base_price;
        }

        return [$publicEmission, $publicCost];
    }

    public function calculateOnlineRide($name, $distance){
        $transport = DB::table('publictransports')->where('name', $name)->first();

        $publicEmission = $this->calculateEmission($distance, "Pertamax", 55, 0.99);

        $publicCost = $transport->base_price + ($transport->price_increase * floor($distance-3));

        return [$publicEmission, $publicCost];
    }

    public function compare(Request $request)
    {
        // 1. Calculate Private Vehicle Cost
        $privateDistance = $request->input('private_distance', 0);
        $fuelName = $request->input('fuel');
        $fuelEfficiency = $request->input('efficiency', 1);
        $parkingToll = $request->input('parking_toll', 0);
        
        [$emission, $cost] = $this->calculateEmission($privateDistance, $fuelName, $fuelEfficiency, 0.99, true);

        // 2. Calculate Public Transport Chain Cost
        $publicLegs = $request->input('public_legs', []);
        $publicEmission = 0;
        $publicCost = 0;
        $publicBreakdown = [];

        foreach ($publicLegs as $leg) {
            if ($leg['type'] === 'Grab Motor' || $leg['type'] === 'Grab Mobil' || $leg['type'] === 'Gojek Motor' || $leg['type'] === 'Gojek Mobil') {
                $tempCost = 0;
                $tempEmission = 0;

                [$tempEmission, $tempCost] = $this->calculateOnlineRide($leg['type'], $leg['distance']);

                $publicEmission += $tempEmission;
                $publicCost += $tempCost;

                $publicBreakdown[] = ucfirst($leg['type']) . ": Rp " . number_format($tempCost);
            } 
            else{

                $tempCost = 0;
                $tempEmission = 0;

                [$tempEmission, $tempCost] = $this->calculatePublicTransport($leg['type'], $leg['distance']);

                $publicEmission += $tempEmission;
                $publicCost += $tempCost;

                $publicBreakdown[] = ucfirst($leg['type']) . ": Rp " . number_format($tempCost);
            }
        }

        // 3. Calculate Difference
        $difference = abs($cost - $publicCost);
        $cheaperOption = $cost < $publicCost ? 'Private Vehicle' : 'Public Transport';

        // 4. Return JSON if it's an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'privateCost' => number_format($cost),
                'publicCost' => number_format($publicCost),
                'difference' => number_format($difference),
                'cheaperOption' => $cheaperOption,
                'publicBreakdown' => implode('  ➔  ', $publicBreakdown)
            ]);
        }

        // Fallback for normal reloads (just in case)
        return view('cost_compare', [
            'privateCost' => $cost,
            'publicCost' => $publicCost,
            'difference' => $difference,
            'cheaperOption' => $cheaperOption,
            'publicBreakdown' => $publicBreakdown
        ]);
    }

    public function addTravel(Request $request){
        $user = Auth::user();
        $transportation_type = $request->input('transport_type');
        $log_date = $request->input('log_date');
        $transport_type = $request->input('transport_type');


        if($transportation_type == "private"){
            $transport_mode = $request->input('vehicle_model');
            $distance = $request->input('private_distance');
            $fuelname = $request->input('fuel');
            $fuel = DB::table('fuels')->where('fuel_name', $fuelname)->first();
            $fuelefficiency = $request->input('efficiency');

            [$emission, $cost] = $this->calculateEmission($distance, $fuelname, $fuelefficiency, 0.99, true);

            $newtravel = new TravelLog();
            $newtravel->user_id = $user->id;
            $newtravel->fuel_id = $fuel->id;
            $newtravel->log_date = $log_date;
            $newtravel->transport_type = $transport_type;
            $newtravel->transport_mode = $transport_mode;
            $newtravel->origin = $request->input('origin') ?? null;
            $newtravel->destination = $request->input('destination') ?? null;
            $newtravel->distance_km = $distance;
            $newtravel->emissions_g = $emission;
            $newtravel->cost_rp = $cost;
            $newtravel->money_saved_rp = 0;
            $newtravel->save();

        }else{
            $publicLegs = $request->input('public_legs', []);
            $publicEmission = 0;
            $publicCost = 0;
            $totalDistance = 0;

            foreach ($publicLegs as $key => $leg) {
                if (in_array($leg['type'], ['Grab Motor', 'Grab Mobil', 'Gojek Motor', 'Gojek Mobil'])) {
                    [$tempEmission, $tempCost] = $this->calculateOnlineRide($leg['type'], $leg['distance']);
                }
                else{
                    [$tempEmission, $tempCost] = $this->calculatePublicTransport($leg['type'], $leg['distance']);
                }

                $publicEmission += $tempEmission;
                $publicCost += $tempCost;
                $totalDistance += $leg['distance'];
                $publicLegs[$key]['calculated_cost'] = $tempCost;
                $publicLegs[$key]['emission'] = $tempEmission;
            }

            [$emission, $cost] = $this->calculateEmission($totalDistance, 'Pertamax', 10, 0.99, true);

            $newtravel = new TravelLog();
            $newtravel->user_id = $user->id;
            $newtravel->log_date = $log_date;
            $newtravel->transport_type = $transport_type;
            $newtravel->distance_km = $totalDistance;
            $newtravel->emissions_g = $publicEmission;
            $newtravel->cost_rp = $publicCost;
            $newtravel->money_saved_rp = $cost;
            $newtravel->save();

            foreach ($publicLegs as $leg) {
                // Find the ID of the public transport/ride-hailing from your dictionary table
                $transitDbRecord = DB::table('publictransports')->where('name', $leg['type'])->first();

                // Using attach() to cleanly insert into the pivot table
                $newtravel->publicTransports()->attach($transitDbRecord->id, [
                    'leg_distance' => $leg['distance'],
                    'leg_cost' => $leg['calculated_cost'],
                    'emission' => $leg['emission']
                ]);
            }
        }

        return redirect('/dashboard');
    }
}
