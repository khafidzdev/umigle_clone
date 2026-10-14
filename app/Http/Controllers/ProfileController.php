<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Menampilkan halaman form set profil
    public function showProfileForm()
    {
        // Jika user sudah terlanjur lengkap profilnya, jangan biarkan di sini, langsung lempar ke /chat
        if (Auth::check() && Auth::user()->profile_completed) {
            return redirect()->to('/chat');
        }

        return view('profile-setup');
    }

    // Menyimpan data gender dan country pilihan user
    public function updateProfile(Request $request)
    {
        $request->validate([
            'gender' => 'required|in:Male,Female,Both',
            'country' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        
        // Update data user dan tandai profil sudah lengkap (true)
        $user->update([
            'gender' => $request->gender,
            'country' => $request->country,
            'profile_completed' => true, 
        ]);

        // Arahkan langsung ke halaman chat setelah tersimpan
        return redirect()->to('/chat');
    }
}