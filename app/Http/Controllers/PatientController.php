<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    /**
     * Get all patients (admin only)
     */
    public function index(Request $request)
    {
        $query = Patient::with(['user', 'appointments.dentist.user']);

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $patients
        ]);
    }

    /**
     * Get patient profile
     */
    public function show(Request $request, $id = null)
    {
        // If no ID provided, get current user's patient profile
        if (!$id) {
            if (!$request->user()->isPatient()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não é um paciente'
                ], 403);
            }
            $patient = $request->user()->patient;
        } else {
            $patient = Patient::with(['user', 'appointments.dentist.user', 'medicalHistory.dentist.user'])
                ->findOrFail($id);
        }

        return response()->json([
            'success' => true,
            'data' => $patient
        ]);
    }

    /**
     * Update patient profile
     */
    public function update(Request $request, $id = null)
    {
        // If no ID provided, update current user's patient profile
        if (!$id) {
            if (!$request->user()->isPatient()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não é um paciente'
                ], 403);
            }
            $patient = $request->user()->patient;
        } else {
            $patient = Patient::findOrFail($id);
        }

        $validator = Validator::make($request->all(), [
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'medical_conditions' => 'nullable|array',
            'medical_conditions.*' => 'string|max:255',
            'allergies' => 'nullable|array',
            'allergies.*' => 'string|max:255',
            'medications' => 'nullable|array',
            'medications.*' => 'string|max:255',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $patient->update($request->only([
            'emergency_contact_name',
            'emergency_contact_phone',
            'medical_conditions',
            'allergies',
            'medications',
            'insurance_provider',
            'insurance_number',
            'notes',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Perfil do paciente atualizado com sucesso',
            'data' => $patient->load('user')
        ]);
    }

    /**
     * Get patient's medical history
     */
    public function medicalHistory(Request $request, $id = null)
    {
        // If no ID provided, get current user's medical history
        if (!$id) {
            if (!$request->user()->isPatient()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não é um paciente'
                ], 403);
            }
            $patientId = $request->user()->patient->id;
        } else {
            $patient = Patient::findOrFail($id);
            $patientId = $patient->id;
        }

        $medicalHistory = \App\Models\MedicalHistory::where('patient_id', $patientId)
            ->with(['dentist.user', 'appointment'])
            ->orderBy('date', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $medicalHistory
        ]);
    }

    /**
     * Get patient's appointments
     */
    public function appointments(Request $request, $id = null)
    {
        // If no ID provided, get current user's appointments
        if (!$id) {
            if (!$request->user()->isPatient()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não é um paciente'
                ], 403);
            }
            $patientId = $request->user()->patient->id;
        } else {
            $patient = Patient::findOrFail($id);
            $patientId = $patient->id;
        }

        $appointments = \App\Models\Appointment::where('patient_id', $patientId)
            ->with(['dentist.user', 'procedures'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    /**
     * Get patient statistics
     */
    public function statistics(Request $request, $id = null)
    {
        // If no ID provided, get current user's statistics
        if (!$id) {
            if (!$request->user()->isPatient()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não é um paciente'
                ], 403);
            }
            $patientId = $request->user()->patient->id;
        } else {
            $patient = Patient::findOrFail($id);
            $patientId = $patient->id;
        }

        $stats = [
            'total_appointments' => \App\Models\Appointment::where('patient_id', $patientId)->count(),
            'completed_appointments' => \App\Models\Appointment::where('patient_id', $patientId)
                ->where('status', 'completed')->count(),
            'cancelled_appointments' => \App\Models\Appointment::where('patient_id', $patientId)
                ->where('status', 'cancelled')->count(),
            'upcoming_appointments' => \App\Models\Appointment::where('patient_id', $patientId)
                ->where('appointment_date', '>=', now()->toDateString())
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->count(),
            'total_spent' => \App\Models\Appointment::where('patient_id', $patientId)
                ->where('status', 'completed')
                ->sum('cost'),
            'last_appointment' => \App\Models\Appointment::where('patient_id', $patientId)
                ->where('status', 'completed')
                ->orderBy('appointment_date', 'desc')
                ->first(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Create medical history entry (dentist only)
     */
    public function createMedicalHistory(Request $request, $id)
    {
        if (!$request->user()->isDentist()) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas dentistas podem criar histórico médico'
            ], 403);
        }

        $patient = Patient::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'appointment_id' => 'nullable|exists:appointments,id',
            'date' => 'required|date',
            'description' => 'required|string|max:1000',
            'diagnosis' => 'nullable|string|max:500',
            'treatment' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'attachments' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $medicalHistory = \App\Models\MedicalHistory::create([
            'patient_id' => $patient->id,
            'dentist_id' => $request->user()->dentist->id,
            'appointment_id' => $request->appointment_id,
            'date' => $request->date,
            'description' => $request->description,
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'notes' => $request->notes,
            'attachments' => $request->attachments,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Histórico médico criado com sucesso',
            'data' => $medicalHistory->load(['dentist.user', 'appointment'])
        ], 201);
    }
}
