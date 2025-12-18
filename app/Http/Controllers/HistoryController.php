<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\History;

class HistoryController extends Controller
{
    public function view(){
        $user = Auth::User()->load('histories');

        return view('history')->with([
            'histories' => $user->histories
        ]);
    }

    public function Delete(Request $request){
        $history = History::where('id', $request->id)->firstOrFail();

        $history->delete();

        return back()->with('Success', 'History Removed');
    }
}
