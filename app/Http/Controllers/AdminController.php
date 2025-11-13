<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Get patients data for admin
     */
    public function getPatientsData(Request $request)
    {
        $query = Patient::with('user');

        // Apply filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('is_active', $request->status === 'active');
            });
        }

        $patients = $query->get()->map(function ($patient) {
            $lastAppointment = Appointment::where('patient_id', $patient->id)
                ->orderBy('appointment_date', 'desc')
                ->first();

            return [
                'id' => $patient->id,
                'name' => $patient->user->name,
                'email' => $patient->user->email,
                'cpf' => $patient->user->cpf,
                'phone' => $patient->user->phone,
                'birth_date' => $patient->user->birth_date ? date('d/m/Y', strtotime($patient->user->birth_date)) : '-',
                'status' => $patient->user->is_active ? 'Ativo' : 'Inativo',
                'last_appointment' => $lastAppointment ? date('d/m/Y', strtotime($lastAppointment->appointment_date)) : 'Nunca',
                'actions' => $this->getPatientActions($patient->id),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $patients,
        ]);
    }

    /**
     * Get dentists data for admin
     */
    public function getDentistsData(Request $request)
    {
        $query = Dentist::with('user');

        // Apply filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('crm', 'like', "%{$search}%");
        }

        if ($request->has('specialization') && $request->specialization) {
            $query->where('specialization', $request->specialization);
        }

        if ($request->has('status') && $request->status) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('is_active', $request->status === 'active');
            });
        }

        $dentists = $query->get()->map(function ($dentist) {
            $lastAppointment = Appointment::where('dentist_id', $dentist->id)
                ->orderBy('appointment_date', 'desc')
                ->first();

            return [
                'id' => $dentist->id,
                'name' => $dentist->user->name,
                'email' => $dentist->user->email,
                'crm' => $dentist->crm,
                'specialization' => ucfirst($dentist->specialization),
                'consultation_fee' => 'R$ '.number_format($dentist->consultation_price, 2, ',', '.'),
                'status' => $dentist->user->is_active ? 'Ativo' : 'Inativo',
                'last_appointment' => $lastAppointment ? date('d/m/Y', strtotime($lastAppointment->appointment_date)) : 'Nunca',
                'actions' => $this->getDentistActions($dentist->id),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $dentists,
        ]);
    }

    /**
     * Get procedures data for admin
     */
    public function getProceduresData(Request $request)
    {
        $query = Procedure::query();

        // Apply filters
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('price_min') && $request->price_min) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->has('price_max') && $request->price_max) {
            $query->where('price', '<=', $request->price_max);
        }

        $procedures = $query->get()->map(function ($procedure) {
            return [
                'id' => $procedure->id,
                'name' => $procedure->name,
                'description' => $procedure->description ? substr($procedure->description, 0, 50).'...' : '-',
                'category' => ucfirst($procedure->category),
                'duration' => $procedure->duration.' min',
                'price' => 'R$ '.number_format($procedure->price, 2, ',', '.'),
                'status' => 'Ativo',
                'actions' => $this->getProcedureActions($procedure->id),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $procedures,
        ]);
    }

    /**
     * Get reports data for admin
     */
    public function getReportsData(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $dentistId = $request->get('dentist_id');
        $status = $request->get('status');

        $query = Appointment::with(['patient.user', 'dentist.user', 'procedures'])
            ->whereBetween('appointment_date', [$startDate, $endDate]);

        if ($dentistId) {
            $query->where('dentist_id', $dentistId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $appointments = $query->get();

        \Log::info('Agendamentos para relatórios: '.$appointments->count());
        \Log::info('Período: '.$startDate.' até '.$endDate);

        // Calculate summary
        $totalAppointments = $appointments->count();
        $completedAppointments = $appointments->where('status', 'completed')->count();
        $totalRevenue = $appointments->where('status', 'completed')->sum(function ($appointment) {
            return $appointment->procedures->sum('price');
        });
        $attendanceRate = $totalAppointments > 0 ?
            round(($completedAppointments / $totalAppointments) * 100, 1) : 0;

        $summary = [
            'total_appointments' => $totalAppointments,
            'completed_appointments' => $completedAppointments,
            'total_revenue' => $totalRevenue,
            'attendance_rate' => $attendanceRate,
        ];

        // Prepare charts data
        $charts = [
            'status' => [
                'labels' => ['Agendado', 'Confirmado', 'Concluído', 'Cancelado'],
                'data' => [
                    $appointments->where('status', 'scheduled')->count(),
                    $appointments->where('status', 'confirmed')->count(),
                    $appointments->where('status', 'completed')->count(),
                    $appointments->where('status', 'cancelled')->count(),
                ],
            ],
            'dentists' => [
                'labels' => $appointments->groupBy('dentist.user.name')->keys()->toArray(),
                'data' => $appointments->groupBy('dentist.user.name')->map->count()->values()->toArray(),
            ],
            'revenue' => $this->getMonthlyRevenue($startDate, $endDate),
        ];

        // Prepare appointments table data
        $appointmentsData = $appointments->map(function ($appointment) {
            return [
                'date' => date('d/m/Y', strtotime($appointment->appointment_date)),
                'time' => substr($appointment->appointment_time, 0, 5), // Apenas HH:MM
                'patient_name' => $appointment->patient->user->name,
                'dentist_name' => $appointment->dentist->user->name,
                'procedures' => $appointment->procedures->pluck('name')->toArray(),
                'status' => $appointment->status,
                'total_value' => $appointment->procedures->sum('price'),
            ];
        });

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'charts' => $charts,
            'appointments' => $appointmentsData,
        ]);
    }

    private function getPatientActions($id)
    {
        return '
            <div class="btn-group" role="group">
                <button class="btn btn-sm btn-outline-primary" onclick="editPatient('.$id.')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="viewPatient('.$id.')">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deletePatient('.$id.')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }

    private function getDentistActions($id)
    {
        return '
            <div class="btn-group" role="group">
                <button class="btn btn-sm btn-outline-primary" onclick="editDentist('.$id.')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="viewDentist('.$id.')">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteDentist('.$id.')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }

    private function getProcedureActions($id)
    {
        return '
            <div class="btn-group" role="group">
                <button class="btn btn-sm btn-outline-primary" onclick="editProcedure('.$id.')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="viewProcedure('.$id.')">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteProcedure('.$id.')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }

    private function getMonthlyRevenue($startDate, $endDate)
    {
        $revenue = [];
        $labels = [];

        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);

        while ($start <= $end) {
            $month = $start->format('Y-m');
            $labels[] = $start->format('M/Y');

            $monthRevenue = Appointment::where('status', 'completed')
                ->whereRaw("DATE_FORMAT(appointment_date, '%Y-%m') = ?", [$month])
                ->with('procedures')
                ->get()
                ->sum(function ($appointment) {
                    return $appointment->procedures->sum('price');
                });

            $revenue[] = $monthRevenue;
            $start->modify('+1 month');
        }

        return [
            'labels' => $labels,
            'data' => $revenue,
        ];
    }

    /**
     * Get appointments data for admin dashboard
     */
    public function getAppointmentsData(Request $request)
    {
        $query = \App\Models\Appointment::with(['patient.user', 'dentist.user', 'procedures']);

        // Aplicar filtros
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('dentist') && $request->dentist) {
            $query->where('dentist_id', $request->dentist);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('appointment_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('appointment_date', '<=', $request->date_to);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        $data = $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'patient_name' => $appointment->patient->user->name ?? 'N/A',
                'dentist_name' => $appointment->dentist->user->name ?? 'N/A',
                'appointment_date' => $appointment->appointment_date ?
                    \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') : '-',
                'appointment_time' => $appointment->appointment_time ?
                    (is_string($appointment->appointment_time) ?
                        substr($appointment->appointment_time, 0, 5) :
                        $appointment->appointment_time->format('H:i')) :
                    '-',
                'status' => $this->getStatusBadge($appointment->status),
                'procedures' => $appointment->procedures->pluck('name')->join(', '),
                'actions' => $this->getAppointmentActions($appointment),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get status badge HTML
     */
    private function getStatusBadge($status)
    {
        $badges = [
            'scheduled' => '<span class="badge bg-primary">Agendado</span>',
            'confirmed' => '<span class="badge bg-success">Confirmado</span>',
            'completed' => '<span class="badge bg-info">Concluído</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelado</span>',
            'no_show' => '<span class="badge bg-warning">Não Compareceu</span>',
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">'.ucfirst($status).'</span>';
    }

    /**
     * Get appointment actions HTML
     */
    private function getAppointmentActions($appointment)
    {
        $actions = '';

        if ($appointment->status === 'scheduled') {
            $actions .= '<button class="btn btn-sm btn-success me-1" onclick="updateAppointmentStatus('.$appointment->id.', \'confirmed\')" title="Confirmar">
                            <i class="fas fa-check"></i>
                         </button>';
        }

        if (in_array($appointment->status, ['scheduled', 'confirmed'])) {
            $actions .= '<button class="btn btn-sm btn-info me-1" onclick="updateAppointmentStatus('.$appointment->id.', \'completed\')" title="Marcar como Concluído">
                            <i class="fas fa-check-circle"></i>
                         </button>';
        }

        if (in_array($appointment->status, ['scheduled', 'confirmed'])) {
            $actions .= '<button class="btn btn-sm btn-warning me-1" onclick="updateAppointmentStatus('.$appointment->id.', \'no_show\')" title="Não Compareceu">
                            <i class="fas fa-times"></i>
                         </button>';
        }

        if (in_array($appointment->status, ['scheduled', 'confirmed'])) {
            $actions .= '<button class="btn btn-sm btn-danger" onclick="cancelAppointment('.$appointment->id.')" title="Cancelar">
                            <i class="fas fa-ban"></i>
                         </button>';
        }

        return $actions;
    }

    /**
     * Store a new patient
     */
    public function storePatient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cpf' => 'required|string|max:14|unique:users',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'required|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'O nome completo é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Por favor, informe um email válido.',
            'email.unique' => 'Este email já está cadastrado no sistema.',
            'email.max' => 'O email não pode ter mais de 255 caracteres.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado no sistema.',
            'cpf.max' => 'O CPF não pode ter mais de 14 caracteres.',
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'birth_date.required' => 'A data de nascimento é obrigatória.',
            'birth_date.date' => 'Por favor, informe uma data válida.',
            'gender.in' => 'O gênero selecionado é inválido.',
            'address.max' => 'O endereço não pode ter mais de 500 caracteres.',
        ]);

        if ($validator->fails()) {
            // Criar mensagem descritiva baseada nos erros
            $errors = $validator->errors();
            $errorMessages = [];
            
            foreach ($errors->all() as $error) {
                $errorMessages[] = $error;
            }
            
            $message = count($errorMessages) === 1 
                ? $errorMessages[0] 
                : 'Por favor, corrija os seguintes erros: ' . implode(' ', $errorMessages);

            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], 422);
        }

        try {
            // Criar usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'cpf' => $request->cpf,
                'phone' => $request->phone,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'address' => $request->address,
                'password' => Hash::make('senha123'), // Senha padrão temporária
                'is_active' => true,
            ]);

            // Atribuir role de paciente
            $user->assignRole('patient');

            // Criar registro de paciente
            $patient = $user->patient()->create([
                'patient_code' => 'PAT'.str_pad($user->id, 6, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Paciente criado com sucesso!',
                'data' => $patient->load('user'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar paciente: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new dentist
     */
    public function storeDentist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'crm' => 'required|string|max:20|unique:dentists',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'required|string|max:255',
            'consultation_fee' => 'required|numeric|min:0',
            'bio' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'O nome completo é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Por favor, informe um email válido.',
            'email.unique' => 'Este email já está cadastrado no sistema.',
            'email.max' => 'O email não pode ter mais de 255 caracteres.',
            'crm.required' => 'O CRM é obrigatório.',
            'crm.unique' => 'Este CRM já está cadastrado no sistema.',
            'crm.max' => 'O CRM não pode ter mais de 20 caracteres.',
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'specialization.required' => 'A especialização é obrigatória.',
            'specialization.max' => 'A especialização não pode ter mais de 255 caracteres.',
            'consultation_fee.required' => 'O valor da consulta é obrigatório.',
            'consultation_fee.numeric' => 'O valor da consulta deve ser um número.',
            'consultation_fee.min' => 'O valor da consulta não pode ser negativo.',
            'bio.max' => 'A biografia não pode ter mais de 1000 caracteres.',
        ]);

        if ($validator->fails()) {
            // Criar mensagem descritiva baseada nos erros
            $errors = $validator->errors();
            $errorMessages = [];
            
            foreach ($errors->all() as $error) {
                $errorMessages[] = $error;
            }
            
            $message = count($errorMessages) === 1 
                ? $errorMessages[0] 
                : 'Por favor, corrija os seguintes erros: ' . implode(' ', $errorMessages);

            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], 422);
        }

        try {
            // Criar usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make('senha123'), // Senha padrão temporária
                'is_active' => true,
            ]);

            // Atribuir role de dentista
            $user->assignRole('dentist');

            // Criar registro de dentista
            $dentist = $user->dentist()->create([
                'crm' => $request->crm,
                'specialization' => $request->specialization,
                'consultation_price' => $request->consultation_fee, // O formulário envia consultation_fee
                'bio' => $request->bio,
                'is_active' => true,
                'available_days' => [1, 2, 3, 4, 5], // Segunda a sexta por padrão
                'available_hours_start' => '08:00',
                'available_hours_end' => '18:00',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dentista criado com sucesso!',
                'data' => $dentist->load('user'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar dentista: '.$e->getMessage(),
            ], 500);
        }
    }
}
