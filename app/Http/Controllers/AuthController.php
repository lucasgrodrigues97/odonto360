<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Register a new user (API)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'cpf' => 'nullable|string|max:14|unique:users',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'role' => 'required|in:patient,dentist',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'cpf' => $request->cpf,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
        ]);

        $user->assignRole($request->role);

        // Create patient or dentist record
        if ($request->role === 'patient') {
            $user->patient()->create([
                'patient_code' => 'PAT' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);
        } elseif ($request->role === 'dentist') {
            $user->dentist()->create([
                'crm' => $request->crm,
                'specialization' => $request->specialization,
                'experience_years' => $request->experience_years ?? 0,
                'consultation_duration' => $request->consultation_duration ?? 60,
                'consultation_price' => $request->consultation_price ?? 0,
                'is_active' => true,
                'available_days' => $request->available_days ?? [1, 2, 3, 4, 5],
                'available_hours_start' => $request->available_hours_start ?? '08:00',
                'available_hours_end' => $request->available_hours_end ?? '18:00',
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Usuário criado com sucesso',
            'data' => [
                'user' => $user->load(['patient', 'dentist']),
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }

    /**
     * Login user (API)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'data' => [
                'user' => $user->load(['patient', 'dentist', 'roles']),
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * Login user (Web)
     */
    public function loginWeb(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Credenciais inválidas.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();
        
        // Redirect based on user role
        if ($user->hasRole('admin')) {
            return redirect()->intended('/admin/dashboard');
        } elseif ($user->hasRole('dentist')) {
            return redirect()->intended('/dentist/appointments');
        } elseif ($user->hasRole('patient')) {
            return redirect()->intended('/patient/appointments');
        }

        return redirect()->intended('/dashboard');
    }

    /**
     * Register a new user (Web)
     */
    public function registerWeb(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'cpf' => 'nullable|string|max:14|unique:users',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'user_type' => 'required|in:patient,dentist',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'cpf' => $request->cpf,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
        ]);

        $user->assignRole($request->user_type);

        // Create patient or dentist record
        if ($request->user_type === 'patient') {
            $user->patient()->create([
                'cpf' => $request->cpf,
                'birth_date' => $request->birth_date,
                'address' => $request->address ?? '',
            ]);
        } elseif ($request->user_type === 'dentist') {
            $user->dentist()->create([
                'crm' => $request->crm ?? '',
                'specialization' => $request->specialization ?? '',
                'consultation_fee' => $request->consultation_fee ?? 0,
            ]);
        }

        Auth::login($user);

        return redirect()->intended('/dashboard')->with('success', 'Conta criada com sucesso!');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load(['patient', 'dentist', 'roles'])
        ]);
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->email)->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => Hash::make(uniqid()),
                ]);
                
                // Assign patient role by default for OAuth users
                $user->assignRole('patient');
                
                // Create patient record
                $user->patient()->create([
                    'patient_code' => 'PAT' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                    'is_active' => true,
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

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
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'birth_date' => 'sometimes|date',
            'gender' => 'sometimes|in:male,female,other',
            'address' => 'sometimes|string|max:500',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:2',
            'zip_code' => 'sometimes|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only([
            'name', 'phone', 'birth_date', 'gender', 'address', 'city', 'state', 'zip_code'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso',
            'data' => $user->load(['patient', 'dentist', 'roles'])
        ]);
    }
}
