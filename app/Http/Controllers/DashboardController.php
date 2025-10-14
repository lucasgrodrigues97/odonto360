<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStats(Request $request)
    {
        $user = auth()->user();

        \Log::info('Dashboard stats requested by user: '.($user ? $user->email : 'not authenticated'));

        // Simplificar temporariamente - sempre retornar stats de admin
        try {
            $stats = $this->getAdminStats();
            \Log::info('Dashboard stats generated successfully', $stats->getData(true));

            return $stats;
        } catch (\Exception $e) {
            \Log::error('Error generating dashboard stats: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar estatísticas: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get recent appointments
     */
    public function getRecentAppointments(Request $request)
    {
        $user = auth()->user();
        $limit = $request->get('limit', 10);

        // Simplificar temporariamente - sempre retornar agendamentos de admin
        try {
            $appointments = Appointment::with(['patient.user', 'dentist.user', 'procedures'])
                ->orderBy('appointment_date', 'desc')
                ->orderBy('appointment_time', 'desc')
                ->limit($limit)
                ->get();

            \Log::info('Agendamentos encontrados: '.$appointments->count());
            \Log::info('Primeiro agendamento: ', $appointments->first() ? $appointments->first()->toArray() : 'Nenhum');
        } catch (\Exception $e) {
            \Log::error('Erro ao carregar agendamentos: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar agendamentos: '.$e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $appointments->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'date' => $appointment->appointment_date,
                    'time' => $appointment->appointment_time ?
                        (is_string($appointment->appointment_time) ?
                            substr($appointment->appointment_time, 0, 5) :
                            $appointment->appointment_time->format('H:i')) :
                        '-',
                    'status' => $appointment->status,
                    'patient_name' => $appointment->patient->user->name ?? 'N/A',
                    'dentist_name' => $appointment->dentist->user->name ?? 'N/A',
                    'procedures' => $appointment->procedures->pluck('name')->toArray(),
                    'reason' => $appointment->reason,
                ];
            }),
        ]);
    }

    /**
     * Get admin statistics
     */
    private function getAdminStats()
    {
        $totalPatients = Patient::count();
        $activeDentists = Dentist::count();
        $todayAppointments = Appointment::whereDate('appointment_date', today())->count();

        // Calcular receita total de forma mais simples
        $totalRevenue = Appointment::where('status', 'completed')
            ->with('procedures')
            ->get()
            ->sum(function ($appointment) {
                return $appointment->procedures->sum('price');
            });

        return response()->json([
            'success' => true,
            'data' => [
                'total_patients' => $totalPatients,
                'active_dentists' => $activeDentists,
                'today_appointments' => $todayAppointments,
                'total_revenue' => $totalRevenue,
            ],
        ]);
    }

    /**
     * Get dentist statistics
     */
    private function getDentistStats($user)
    {
        $dentist = $user->dentist;

        $todayAppointments = Appointment::where('dentist_id', $dentist->id)
            ->whereDate('appointment_date', today())
            ->count();

        $totalAppointments = Appointment::where('dentist_id', $dentist->id)->count();

        $monthlyRevenue = Appointment::where('dentist_id', $dentist->id)
            ->where('status', 'completed')
            ->whereMonth('appointment_date', now()->month)
            ->sum(DB::raw('procedures.price'))
            ->join('appointment_procedures', 'appointments.id', '=', 'appointment_procedures.appointment_id')
            ->join('procedures', 'appointment_procedures.procedure_id', '=', 'procedures.id')
            ->sum('procedures.price');

        return response()->json([
            'success' => true,
            'data' => [
                'today_appointments' => $todayAppointments,
                'total_appointments' => $totalAppointments,
                'monthly_revenue' => $monthlyRevenue,
            ],
        ]);
    }

    /**
     * Get patient statistics
     */
    private function getPatientStats($user)
    {
        $patient = $user->patient;

        $totalAppointments = Appointment::where('patient_id', $patient->id)->count();
        $upcomingAppointments = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', '>=', today())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_appointments' => $totalAppointments,
                'upcoming_appointments' => $upcomingAppointments,
            ],
        ]);
    }
}
