<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redirect ke halaman autentikasi Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Menangani data kembalian dari Google.
     */
    public function handleGoogleCallback()
    {
        try {
            // Mengambil user dari Google (guzzle config sudah otomatis menangani SSL di services.php)
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Cek apakah user sudah terdaftar berdasarkan email
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Jika belum terdaftar, buat data user baru
                // profile_completed otomatis bernilai false (atau 0) untuk user baru
                $user = User::create([
                    'google_id' => $googleUser->getId(),
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'avatar_url' => $googleUser->getAvatar(),
                    'gender' => 'Both', 
                    'country' => 'Indonesia', 
                    'profile_completed' => false, 
                    'password' => bcrypt(Str::random(16)), 
                ]);
            } else {
                // Jika sudah ada, update data Google ID & Avatar terbarunya
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar_url' => $googleUser->getAvatar(),
                ]);
            }

            // Login-kan user ke dalam aplikasi
            Auth::login($user, true);

            // Jika profil belum lengkap, arahkan ke form set profil dulu
            if (!$user->profile_completed) {
                return redirect()->to('/profile/setup');
            }

            // Jika sudah lengkap, langsung ke chat
            return redirect()->to('/chat');

        } catch (\Exception $e) {
            // Jika masih ada error lain, tampilkan di layar untuk dicek
            dd([
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }
}