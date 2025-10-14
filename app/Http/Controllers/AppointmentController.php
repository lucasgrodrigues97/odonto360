<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Patient;
use App\Models\Procedure;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Get appointments with filters
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['patient.user', 'dentist.user', 'procedures']);

        // Filter by patient (if user is patient)
        if ($request->user()->isPatient()) {
            $query->where('patient_id', $request->user()->patient->id);
        }

        // Filter by dentist (if user is dentist)
        if ($request->user()->isDentist()) {
            $query->where('dentist_id', $request->user()->dentist->id);
        }

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

        // Filter by dentist
        if ($request->has('dentist_id')) {
            $query->where('dentist_id', $request->dentist_id);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $appointments,
        ]);
    }

    /**
     * Get available time slots for a dentist on a specific date
     */
    public function getAvailableSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dentist_id' => 'required|exists:dentists,id',
            'date' => 'required|date|after_or_equal:today',
            'duration' => 'nullable|integer|min:30|max:240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $dentist = Dentist::findOrFail($request->dentist_id);
        $date = $request->date;
        $duration = $request->duration ?? 60;

        $availableSlots = $this->getAvailableTimeSlots($dentist, $date, $duration);

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date,
                'dentist' => $dentist->load('user'),
                'available_slots' => $availableSlots,
                'duration' => $duration,
            ],
        ]);
    }

    /**
     * Get AI-suggested time slots
     */
    public function getAISuggestions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dentist_id' => 'required|exists:dentists,id',
            'preferred_dates' => 'required|array|min:1',
            'preferred_dates.*' => 'date|after_or_equal:today',
            'duration' => 'nullable|integer|min:30|max:240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $dentist = Dentist::findOrFail($request->dentist_id);
        $preferredDates = $request->preferred_dates;
        $duration = $request->duration ?? 60;

        $suggestions = $this->aiService->suggestAppointmentTimes($dentist, $preferredDates, $duration);

        return response()->json([
            'success' => true,
            'data' => [
                'suggestions' => $suggestions,
                'dentist' => $dentist->load('user'),
                'duration' => $duration,
            ],
        ]);
    }

    /**
     * Create a new appointment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dentist_id' => 'required|exists:dentists,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'duration' => 'nullable|integer|min:30|max:240',
            'notes' => 'nullable|string|max:1000',
            'reason' => 'nullable|string|max:500',
            'procedures' => 'nullable|array',
            'procedures.*.id' => 'required_with:procedures|exists:procedures,id',
            'procedures.*.quantity' => 'required_with:procedures|integer|min:1',
            'procedures.*.notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $dentist = Dentist::findOrFail($request->dentist_id);
        $date = $request->appointment_date;
        $time = $request->appointment_time;
        $duration = $request->duration ?? 60;

        // Check if dentist is available
        if (! $dentist->isAvailable($date, $time)) {
            return response()->json([
                'success' => false,
                'message' => 'Horário não disponível para este dentista',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Get patient ID
            $patientId = $request->user()->isPatient()
                ? $request->user()->patient->id
                : $request->patient_id;

            if (! $patientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paciente não encontrado',
                ], 422);
            }

            // Calculate total cost
            $totalCost = 0;
            if ($request->has('procedures')) {
                foreach ($request->procedures as $procedureData) {
                    $procedure = Procedure::find($procedureData['id']);
                    $totalCost += $procedure->price * $procedureData['quantity'];
                }
            } else {
                $totalCost = $dentist->consultation_price;
            }

            // Create appointment
            $appointment = Appointment::create([
                'patient_id' => $patientId,
                'dentist_id' => $request->dentist_id,
                'appointment_date' => $date,
                'appointment_time' => $time,
                'duration' => $duration,
                'status' => Appointment::STATUS_SCHEDULED,
                'notes' => $request->notes,
                'reason' => $request->reason,
                'cost' => $totalCost,
                'payment_status' => Appointment::PAYMENT_PENDING,
            ]);

            // Attach procedures
            if ($request->has('procedures')) {
                foreach ($request->procedures as $procedureData) {
                    $procedure = Procedure::find($procedureData['id']);
                    $appointment->procedures()->attach($procedure->id, [
                        'quantity' => $procedureData['quantity'],
                        'price' => $procedure->price,
                        'notes' => $procedureData['notes'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Agendamento criado com sucesso',
                'data' => $appointment->load(['patient.user', 'dentist.user', 'procedures']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar agendamento: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled,no_show',
            'cancellation_reason' => 'required_if:status,cancelled|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $appointment->update([
            'status' => $request->status,
            'cancellation_reason' => $request->cancellation_reason,
            'notes' => $request->notes,
            'cancelled_at' => $request->status === 'cancelled' ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status do agendamento atualizado com sucesso',
            'data' => $appointment->load(['patient.user', 'dentist.user', 'procedures']),
        ]);
    }

    /**
     * Cancel appointment
     */
    public function cancel(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        // Check if user can cancel this appointment
        if ($request->user()->isPatient() && $appointment->patient_id !== $request->user()->patient->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não pode cancelar este agendamento',
            ], 403);
        }

        if (! $appointment->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Este agendamento não pode ser cancelado',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'cancellation_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $appointment->update([
            'status' => Appointment::STATUS_CANCELLED,
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agendamento cancelado com sucesso',
            'data' => $appointment->load(['patient.user', 'dentist.user', 'procedures']),
        ]);
    }

    /**
     * Get available time slots for a dentist
     */
    private function getAvailableTimeSlots($dentist, $date, $duration)
    {
        $dayOfWeek = date('N', strtotime($date));

        // Check if dentist works on this day
        if (! in_array($dayOfWeek, $dentist->available_days ?? [])) {
            return [];
        }

        $startTime = $dentist->available_hours_start;
        $endTime = $dentist->available_hours_end;

        $slots = [];
        $currentTime = strtotime($startTime);
        $endTimestamp = strtotime($endTime);

        while ($currentTime < $endTimestamp) {
            $timeSlot = date('H:i', $currentTime);

            // Check if this time slot is available
            $isAvailable = ! Appointment::where('dentist_id', $dentist->id)
                ->where('appointment_date', $date)
                ->where('appointment_time', $timeSlot)
                ->whereIn('status', [Appointment::STATUS_SCHEDULED, Appointment::STATUS_CONFIRMED])
                ->exists();

            if ($isAvailable) {
                $slots[] = [
                    'time' => $timeSlot,
                    'formatted_time' => date('H:i', $currentTime),
                    'available' => true,
                ];
            }

            $currentTime += $duration * 60; // Add duration in minutes
        }

        return $slots;
    }

    /**
     * Get AI analysis of appointment patterns
     */
    public function getAIAnalysis($dentistId, Request $request)
    {
        $request->validate([
            'days' => 'integer|min:7|max:365',
        ]);

        $aiService = new AIService;

        return $aiService->analyzeAppointmentPatterns(
            $dentistId,
            $request->days ?? 30
        );
    }

    /**
     * Get AI predictions for optimal scheduling
     */
    public function getAIPredictions($dentistId, Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'duration' => 'integer|min:30|max:240',
        ]);

        $aiService = new AIService;

        return $aiService->predictOptimalTimes(
            $dentistId,
            $request->date,
            $request->duration ?? 60
        );
    }
}
