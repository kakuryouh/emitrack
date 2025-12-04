<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class EmissionController extends Controller
{

    public function show(){
        return view('calculate');
    }

    public function calculate(Request $request){
        $apiKey = env('ORS_API_KEY');
        $baseUrl = "https://api.openrouteservice.org/v2/directions/";
        $profile = "driving-car";
        $options = [];
        
        $originName = $request->input('origin');
        $destinationName = $request->input('destination');
        $vehicle = $request->input('vehicle_model');

        function getCoordinates($query) {
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

        $originData = getCoordinates($originName);
        $destData = getCoordinates($destinationName);

        if (!$originData || !$destData) {
            return back()->with('error', 'Could not find one of the locations. Try adding the city name (e.g., "Binus Alam Sutera, Tangerang").');
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
            return back()->with('error', 'Please select a valid vehicle type.');
        }

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

        $routeData = $response->json();

        if (isset($routeData['routes'][0]['summary'])) {
            // Geometry for routes
            $geometry = $routeData['routes'][0]['geometry'];
            $steps = $routeData['routes'][0]['segments'][0]['steps'];
            $summary = $routeData['routes'][0]['summary'];
            $distanceKm = round($summary['distance'] / 1000, 2);
            $durationSeconds = $summary['duration'];

            $EmissionsList = DB::table('emission')->select('vehicle_type', 'average_emission')->get();                      
            $MainEmission = $EmissionsList->firstWhere('vehicle_type', $vehicle)->average_emission;

            $Recommendation = [];
            $ReccomendationMsg = "";

            if($distanceKm < 3 && !strcmp($vehicle, "Sepeda")){

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

            }elseif($distanceKm < 3 && strcmp($vehicle, "Sepeda")){
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
                if (strcmp($vehicle, "Sepeda Motor (Listrik)")){
                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Sepeda')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Sepeda')->average_emission),
                    ];

                    $ReccomendationMsg = "Jika waktu tempuh tidak menjadi masalah, sepeda dapat menjadi alternatif baik dibanding $vehicle. waktu perjalanan akan lebih lama tetapi bersepeda juga dapat meningkatkan kesehatan tubuh.";

                }elseif (strcmp($vehicle, "Sepeda Motor (Bensin)")){
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
                if (strcmp($vehicle, "Mobil (Listrik)")){
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
                if (strcmp($vehicle, "Sepeda Motor (Listrik)")){
                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission),
                    ];

                    $ReccomendationMsg = "Jika ada kereta yang memiliki destinasi yang sama, kereta listrik adalah pilihan yang bagus untuk perjalanan jarak menengah.";

                }elseif (strcmp($vehicle, "Sepeda Motor (Bensin)")){
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
                if (strcmp($vehicle, "Sepeda Motor (Listrik)")){
                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission),
                    ];

                    $ReccomendationMsg = "Jika ada kereta yang memiliki destinasi yang sama, kereta listrik adalah pilihan yang bagus untuk perjalanan jarak jauh seperti ini.";

                }elseif (strcmp($vehicle, "Sepeda Motor (Bensin)")){
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

            }elseif($distanceKm >= 15 && str_contains($vehicle, "Mobil")){
                if (strcmp($vehicle, "Mobil (Listrik)") === 0){
                    $Recommendation[] = [
                        'vehicle_type' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->vehicle_type,
                        'emission' => $EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission,
                        'emissionTotal' => $distanceKm*($EmissionsList->firstWhere('vehicle_type', 'Kereta Listrik')->average_emission),
                    ];

                    $ReccomendationMsg = "Jika ada kereta yang memiliki destinasi yang sama, kereta listrik adalah pilihan yang bagus untuk perjalanan jarak menengah.";

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

            $comparisonList = [];

            // Add the User's Main Choice
            $comparisonList['main'] = round(($MainEmission*$distanceKm), 2);

            // Add the Recommendations
            // Note: We need to calculate the TOTAL for recommendations to compare fairly.
            // Total = Rate * Distance
            foreach ($Recommendation as $index => $rec) {
                $comparisonList["rec_$index"] = $rec['emissionTotal'];
            }

            // 2. Find the Min and Max values in this specific trip
            $minValue = min($comparisonList);
            $maxValue = max($comparisonList);

            // 3. Define a closure (helper) to pick the color based on rank
            $assignColor = function($value) use ($minValue, $maxValue) {
                // If it's the absolute lowest, it's Green
                if ($value == $minValue) return '#34a853'; 
                // If it's the absolute highest, it's Red
                if ($value == $maxValue) return '#ea4335';
                // Otherwise, it's in the middle (Yellow)
                return '#f9ab00';
            };

            // 4. Assign the colors back to the variables
            
            // Set Main Result Color
            $mainColor = $assignColor($comparisonList['main']);

            // Set Colors for each Recommendation
            foreach ($Recommendation as $index => &$rec) {
                $recTotal = $comparisonList["rec_$index"];
                $rec['color'] = $assignColor($recTotal);
            }
            unset($rec); // Break the reference

            

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
                'emissionRate' => $MainEmission,
                'totalEmission' => round(($MainEmission*$distanceKm), 2),
                'vehicleModel' => $request->input('vehicle_model'),
                'mainColor' => $mainColor,
                'recommendations' => $Recommendation,
                'recommendationMsg' => $ReccomendationMsg,

                'routeGeometry' => $geometry,
                'routeSteps' => $steps
            ]);

        } else {
            return back()->with('error', 'Route could not be calculated between these points.');
        }
    }
}
