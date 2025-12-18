<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\rules\Password;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function update(Request $request){
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
        ]);

        $user->name = $request->input('name');

        if($request->filled('password')){

            $request->validate([
                'password' => ['required', 'confirmed', Password::default()],
            ]);

            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect('/profile')->with('Success', 'Profile Updated');
    }

    public function delete(Request $request){
        $user = Auth::user();

        Auth::logout();

        if($user->delete()){
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('Success', 'Your Account Has Been Deleted');
        }else{
            Auth::login($user);
            return redirect('/')->with('Failed', 'Something went wrong when deleting your account. Please try again later.');
        }
    }
}
