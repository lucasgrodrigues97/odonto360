<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['email', 'profile'])
            ->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Verificar se o usuário já existe
            $user = User::where('email', $googleUser->email)->first();
            
            if (!$user) {
                // Criar novo usuário
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => Hash::make(uniqid()), // Senha aleatória
                    'email_verified_at' => now(),
                ]);
                
                // Atribuir role de paciente por padrão
                $user->assignRole('patient');
                
                // Criar perfil de paciente
                $user->patient()->create([
                    'patient_code' => 'PAT' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                    'is_active' => true,
                ]);
            } else {
                // Atualizar dados do usuário existente
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                ]);
            }

            // Fazer login do usuário
            Auth::login($user);
            
            // Criar token de API
            $token = $user->createToken('google-auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login com Google realizado com sucesso',
                'data' => [
                    'user' => $user->load(['patient', 'dentist', 'roles']),
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer login com Google: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Google OAuth URL
     */
    public function getGoogleAuthUrl()
    {
        $url = Socialite::driver('google')
            ->scopes(['email', 'profile'])
            ->redirect()
            ->getTargetUrl();

        return response()->json([
            'success' => true,
            'data' => [
                'auth_url' => $url
            ]
        ]);
    }

    /**
     * Revoke Google OAuth token
     */
    public function revokeGoogleToken(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->google_id) {
                // Revogar token do Google (se necessário)
                // Implementar revogação do token do Google
                
                $user->update(['google_id' => null]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Token do Google revogado com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao revogar token do Google: ' . $e->getMessage()
            ], 500);
        }
    }
}
