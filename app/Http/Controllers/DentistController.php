<?php

namespace App\Http\Controllers;

use App\Models\Dentist;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DentistController extends Controller
{
    /**
     * Get all dentists
     */
    public function index(Request $request)
    {
        $query = Dentist::with(['user', 'specializations']);

        // Search by name or specialization
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })->orWhere('specialization', 'like', "%{$search}%");
            });
        }

        // Filter by specialization
        if ($request->has('specialization_id')) {
            $query->whereHas('specializations', function ($q) use ($request) {
                $q->where('specialization_id', $request->specialization_id);
            });
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $dentists = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $dentists
        ]);
    }

    /**
     * Get dentist profile
     */
    public function show(Request $request, $id = null)
    {
        // If no ID provided, get current user's dentist profile
        if (!$id) {
            if (!$request->user()->isDentist()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não é um dentista'
                ], 403);
            }
            $dentist = $request->user()->dentist;
        } else {
            $dentist = Dentist::with(['user', 'specializations', 'schedules'])
                ->findOrFail($id);
        }

        return response()->json([
            'success' => true,
            'data' => $dentist
        ]);
    }

    /**
     * Update dentist profile
     */
    public function update(Request $request, $id = null)
    {
        // If no ID provided, update current user's dentist profile
        if (!$id) {
            if (!$request->user()->isDentist()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não é um dentista'
                ], 403);
            }
            $dentist = $request->user()->dentist;
        } else {
            $dentist = Dentist::findOrFail($id);
        }

        $validator = Validator::make($request->all(), [
            'crm' => 'sometimes|string|max:20|unique:dentists,crm,' . $dentist->id,
            'specialization' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'consultation_duration' => 'nullable|integer|min:15|max:240',
            'consultation_price' => 'nullable|numeric|min:0|max:9999.99',
            'bio' => 'nullable|string|max:1000',
            'available_days' => 'nullable|array',
            'available_days.*' => 'integer|min:1|max:7',
            'available_hours_start' => 'nullable|date_format:H:i',
            'available_hours_end' => 'nullable|date_format:H:i',
            'specialization_ids' => 'nullable|array',
            'specialization_ids.*' => 'exists:specializations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $dentist->update($request->only([
                'crm',
                'specialization',
                'experience_years',
                'consultation_duration',
                'consultation_price',
                'bio',
                'available_days',
                'available_hours_start',
                'available_hours_end',
            ]));

            // Update specializations
            if ($request->has('specialization_ids')) {
                $dentist->specializations()->sync($request->specialization_ids);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Perfil do dentista atualizado com sucesso',
                'data' => $dentist->load(['user', 'specializations'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dentist's appointments
     */
    public function appointments(Request $request, $id = null)
    {
        // If no ID provided, get current user's appointments
        if (!$id) {
            if (!$request->user()->isDentist()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não é um dentista'
                ], 403);
            }
            $dentistId = $request->user()->dentist->id;
        } else {
            $dentist = Dentist::findOrFail($id);
            $dentistId = $dentist->id;
        }

        $query = \App\Models\Appointment::where('dentist_id', $dentistId)
            ->with(['patient.user', 'procedures']);

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('appointment_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('appointment_date', '<=', $request->end_date);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    /**
     * Get dentist's patients
     */
    public function patients(Request $request, $id = null)
    {
        // If no ID provided, get current user's patients
        if (!$id) {
            if (!$request->user()->isDentist()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não é um dentista'
                ], 403);
            }
            $dentistId = $request->user()->dentist->id;
        } else {
            $dentist = Dentist::findOrFail($id);
            $dentistId = $dentist->id;
        }

        $patients = \App\Models\Patient::whereHas('appointments', function ($query) use ($dentistId) {
            $query->where('dentist_id', $dentistId);
        })
        ->with(['user', 'appointments' => function ($query) use ($dentistId) {
            $query->where('dentist_id', $dentistId);
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $patients
        ]);
    }

    /**
     * Get dentist's schedule
     */
    public function schedule(Request $request, $id = null)
    {
        // If no ID provided, get current user's schedule
        if (!$id) {
            if (!$request->user()->isDentist()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não é um dentista'
                ], 403);
            }
            $dentistId = $request->user()->dentist->id;
        } else {
            $dentist = Dentist::findOrFail($id);
            $dentistId = $dentist->id;
        }

        $schedule = \App\Models\DentistSchedule::where('dentist_id', $dentistId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $schedule
        ]);
    }

    /**
     * Update dentist's schedule
     */
    public function updateSchedule(Request $request, $id = null)
    {
        // If no ID provided, update current user's schedule
        if (!$id) {
            if (!$request->user()->isDentist()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não é um dentista'
                ], 403);
            }
            $dentistId = $request->user()->dentist->id;
        } else {
            $dentist = Dentist::findOrFail($id);
            $dentistId = $dentist->id;
        }

        $validator = Validator::make($request->all(), [
            'schedule' => 'required|array',
            'schedule.*.day_of_week' => 'required|integer|min:1|max:7',
            'schedule.*.start_time' => 'required|date_format:H:i',
            'schedule.*.end_time' => 'required|date_format:H:i|after:schedule.*.start_time',
            'schedule.*.is_available' => 'boolean',
            'schedule.*.notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Delete existing schedule
            \App\Models\DentistSchedule::where('dentist_id', $dentistId)->delete();

            // Create new schedule
            foreach ($request->schedule as $scheduleItem) {
                \App\Models\DentistSchedule::create([
                    'dentist_id' => $dentistId,
                    'day_of_week' => $scheduleItem['day_of_week'],
                    'start_time' => $scheduleItem['start_time'],
                    'end_time' => $scheduleItem['end_time'],
                    'is_available' => $scheduleItem['is_available'] ?? true,
                    'notes' => $scheduleItem['notes'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Agenda atualizada com sucesso',
                'data' => \App\Models\DentistSchedule::where('dentist_id', $dentistId)->get()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar agenda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dentist statistics
     */
    public function statistics(Request $request, $id = null)
    {
        // If no ID provided, get current user's statistics
        if (!$id) {
            if (!$request->user()->isDentist()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não é um dentista'
                ], 403);
            }
            $dentistId = $request->user()->dentist->id;
        } else {
            $dentist = Dentist::findOrFail($id);
            $dentistId = $dentist->id;
        }

        $stats = [
            'total_appointments' => \App\Models\Appointment::where('dentist_id', $dentistId)->count(),
            'completed_appointments' => \App\Models\Appointment::where('dentist_id', $dentistId)
                ->where('status', 'completed')->count(),
            'cancelled_appointments' => \App\Models\Appointment::where('dentist_id', $dentistId)
                ->where('status', 'cancelled')->count(),
            'upcoming_appointments' => \App\Models\Appointment::where('dentist_id', $dentistId)
                ->where('appointment_date', '>=', now()->toDateString())
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->count(),
            'total_revenue' => \App\Models\Appointment::where('dentist_id', $dentistId)
                ->where('status', 'completed')
                ->sum('cost'),
            'unique_patients' => \App\Models\Appointment::where('dentist_id', $dentistId)
                ->distinct('patient_id')
                ->count('patient_id'),
            'average_appointment_duration' => \App\Models\Appointment::where('dentist_id', $dentistId)
                ->avg('duration'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
