<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\TravelLog;
use App\Models\History;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function viewDashboard()
    {
        $user = Auth::user();

        $totalEmissionsG = TravelLog::where('user_id', $user->id)->sum('emissions_g');
        $totalMoneySaved = TravelLog::where('user_id', $user->id)->sum('money_saved_rp');
        $totalEmissionsKg = $totalEmissionsG / 1000;
        $coalEquivalentKg = $totalEmissionsKg / 2.5;
        $totalDistanceKm = TravelLog::where('user_id', $user->id)->sum('distance_km');
        $badges = $this->calculateBadges($user->id);

        $emissionStats = TravelLog::where('user_id', $user->id)
            ->selectRaw('
                MIN(emissions_g) as min_emission, 
                MAX(emissions_g) as max_emission, 
                AVG(emissions_g) as avg_emission
            ')->first();

        $mostUsedMode = TravelLog::where('user_id', $user->id)
            ->selectRaw('transport_type, COUNT(*) as count')
            ->groupBy('transport_type')
            ->orderBy('count', 'DESC')
            ->first();

        $last7DaysData = TravelLog::where('user_id', $user->id)
            ->where('log_date', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(log_date) as date, SUM(emissions_g) as total')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->pluck('total', 'date')
            ->toArray();

        $chartLabels = [];
        $chartValues = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $displayDate = Carbon::now()->subDays($i)->format('M d');
            
            $chartLabels[] = $displayDate;
            $chartValues[] = isset($last7DaysData[$date]) ? round($last7DaysData[$date] / 1000, 2) : 0; 
        }

        return view('dashboard', [
            'totalEmissionsKg' => round($totalEmissionsKg, 2),
            'totalMoneySaved' => $totalMoneySaved,
            'coalEquivalent' => round($coalEquivalentKg, 2),
            'totalDistanceKm' => $totalDistanceKm,
            'badges' => $badges,
            
            // New Variables
            'stats' => $emissionStats,
            'mostUsed' => $mostUsedMode,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
        ]);
    }

    private function calculateBadges(int $userid)
    {
        $badges = [
            'first_trip' => false,
            'public_streak_7' => false,
            'eco_warrior' => false,
        ];

        // Check First Trip
        if (TravelLog::where('user_id', $userid)->exists()) {
            $badges['first_trip'] = true;
        }

        // Check 7-Day Public Transport
        $streakCount = TravelLog::where('user_id', $userid)
            ->where('transport_type', 'public')
            ->where('log_date', '>=', Carbon::now()->subDays(7))
            ->count();
        
        if ($streakCount >= 7) {
            $badges['public_streak_7'] = true;
        }

        // Check Eco Warrior
        $weekEmissions = TravelLog::where('user_id', $userid)
            ->whereBetween('log_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('emissions_g');
            
        if ($weekEmissions > 0 && $weekEmissions <= 5000) {
            $badges['eco_warrior'] = true;
        }

        return $badges;
    }

    public function viewLogs(){
        $user = Auth::User()->load('logs');

        return view('history')->with([
            'logs' => $user->logs
        ]);
    }

    public function DeleteLog(Request $request){
        $history = History::where('id', $request->id)->firstOrFail();

        $history->delete();

        return back()->with('Success', 'History Removed');
    }
}
