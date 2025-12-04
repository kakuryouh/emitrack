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
        $originName = $request->input('origin');
        $destinationName = $request->input('destination');
        $vehicle = $request->input('vehicle_model');
        $apiKey = env('ORS_API_KEY');

        $responseOrigin = Http::get('https://api.openrouteservice.org/geocode/search', [
            'api_key' => $apiKey,
            'text' => $originName,
            'boundary.country' => 'ID',
            'size' => 1,
        ]);

        $responseDestination = Http::get('https://api.openrouteservice.org/geocode/search', [
            'api_key' => $apiKey,
            'text' => $destinationName,
            'boundary.country' => 'ID',
            'size' => 1,
        ]);

        $dataOrigin = $responseOrigin->json();
        $dataDestination = $responseDestination->json();

        if(isset($dataOrigin['features'][0]['geometry']['coordinates']) && isset($dataDestination['features'][0]['geometry']['coordinates'])){
            $origin = $dataOrigin['features'][0]['geometry']['coordinates'];
            $destination = $dataDestination['features'][0]['geometry']['coordinates'];

            $response = Http::withHeaders([
                'Authorization' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post("https://api.openrouteservice.org/v2/directions/driving-car", [
                'coordinates' => [
                    [$origin[0], $origin[1]],
                    [$destination[0], $destination[1]],
                ],
            ]);

            $data = $response->json();

            if(isset($data)){
                $coordinatesOrigin = $dataOrigin['features'][0]['geometry']['coordinates'];
                $coordinatesDestination = $dataDestination['features'][0]['geometry']['coordinates'];
                $emissionRateForVehicle = DB::table('emission')->where('vehicle_type', $vehicle)->value('avarage_emission');
                $totalDistance = round(($data['routes'][0]['summary']['distance'] / 1000), 2);

                return view('result', [
                    'origin' => [
                        'name' => $originName,
                        'lat' => $coordinatesOrigin[1],
                        'lng' => $coordinatesOrigin[0],
                    ],

                    'destination' => [
                        'name' => $destinationName,
                        'lat' => $coordinatesDestination[1],
                        'lng' => $coordinatesDestination[0],
                    ],

                    'duration' => [
                        'minute' => (round($data['routes'][0]['summary']['duration'] / 60)) % 60,
                        'hour' => floor($data['routes'][0]['summary']['duration'] / 3600),
                    ],

                    'emissionRate' => $emissionRateForVehicle,
                    'totalEmission' => round(($emissionRateForVehicle*$totalDistance), 2),
                    'vehicleModel' => $request->input('vehicle_model'),
                    'distance' => $totalDistance,
                    'geometry' => $data['routes'][0]['geometry'],
                ]);
            }else{
                return view('calculate', ['error' => "Something went wrong when calculating route"]);
            }
        }else{
            return view('calculate', ['error' => "Cannot find location"]);
        }
    }
}
