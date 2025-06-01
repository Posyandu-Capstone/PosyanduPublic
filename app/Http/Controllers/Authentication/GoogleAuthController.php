<?php

namespace App\Http\Controllers\Authentication;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request)
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle()
    {
        try {
            Log::info('Masuk callbackGoogle');

            $google_user = Socialite::driver('google')->user();
            Log::info('Data user dari Google', [
                'google_id' => $google_user->getId(),
                'email' => $google_user->getEmail(),
                'name' => $google_user->getName()
            ]);

            $user = User::where('google_id', $google_user->getId())->first();

            if ($user) {
                $frontendUrl = env('FRONTEND_URL');
                if ($user->status === 'pending') {
                    return redirect()->away("{$frontendUrl}/pending");
                } elseif ($user->status === 'denied') {
                    return redirect()->away("{$frontendUrl}/denied");
                }

                $token = JWTAuth::fromUser($user);
                return redirect()->away("{$frontendUrl}/loginTransition?token=$token&email=" . $user->email);
            } else {
                $user = User::create([
                    'nama_lengkap' => $google_user->getName(),
                    'email' => $google_user->getEmail(),
                    'google_id' => $google_user->getId(),
                    'status' => 'pending'
                ]);

                $token = JWTAuth::fromUser($user);
                $frontendUrl = env('FRONTEND_URL');
                return redirect()->away("{$frontendUrl}/register?token=$token&email=" . $user->email);
            }
        } catch (\Throwable $th) {
            Log::error('Error call-backGoogle', [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);

            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
