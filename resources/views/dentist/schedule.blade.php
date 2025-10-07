@extends('layouts.app')

@section('title', 'Minha Agenda')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Minha Agenda</h1>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
                <i class="fas fa-plus me-1"></i>Novo Agendamento
            </button>
        </div>
    </div>

    <!-- Schedule Controls -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Controles da Agenda</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="datePicker" class="form-label">Data</label>
                    <input type="date" class="form-control" id="datePicker" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="viewType" class="form-label">Visualização</label>
                    <select class="form-select" id="viewType">
                        <option value="day">Dia</option>
                        <option value="week">Semana</option>
                        <option value="month">Mês</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">Todos</option>
                        <option value="scheduled">Agendado</option>
                        <option value="confirmed">Confirmado</option>
                        <option value="completed">Concluído</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary" onclick="loadSchedule()">
                        <i class="fas fa-search me-1"></i>Carregar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Calendar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Agenda</h6>
        </div>
        <div class="card-body">
            <div id="scheduleCalendar">
                <!-- Calendar will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Today's Appointments -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Agendamentos de Hoje</h6>
        </div>
        <div class="card-body">
            <div id="todayAppointments">
                <!-- Today's appointments will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- New Appointment Modal -->
<div class="modal fade" id="newAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Agendamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newAppointmentForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="patientSelect" class="form-label">Paciente *</label>
                                <select class="form-select" id="patientSelect" required>
                                    <option value="">Selecione um paciente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="appointmentDate" class="form-label">Data *</label>
                                <input type="date" class="form-control" id="appointmentDate" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="appointmentTime" class="form-label">Horário *</label>
                                <input type="time" class="form-control" id="appointmentTime" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duration" class="form-label">Duração (minutos)</label>
                                <select class="form-select" id="duration">
                                    <option value="30">30 minutos</option>
                                    <option value="60" selected>1 hora</option>
                                    <option value="90">1h30</option>
                                    <option value="120">2 horas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="proceduresSelect" class="form-label">Procedimentos</label>
                        <select class="form-select" id="proceduresSelect" multiple>
                            <!-- Procedures will be loaded here -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Observações</label>
                        <textarea class="form-control" id="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agendar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    loadSchedule();
    loadPatients();
    loadProcedures();
    
    // Event handlers
    $('#newAppointmentForm').on('submit', function(e) {
        e.preventDefault();
        saveAppointment();
    });
    
    $('#appointmentDate').on('change', function() {
        loadAvailableSlots();
    });
});

function loadSchedule() {
    const date = $('#datePicker').val();
    const viewType = $('#viewType').val();
    const status = $('#statusFilter').val();
    
    $.ajax({
        url: '/api/dentist/schedule',
        method: 'GET',
        data: {
            date: date,
            view_type: viewType,
            status: status
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displaySchedule(response.data);
                displayTodayAppointments(response.today_appointments);
            }
        },
        error: function() {
            displayScheduleError();
        }
    });
}

function displaySchedule(scheduleData) {
    const container = $('#scheduleCalendar');
    
    if (scheduleData.length === 0) {
        container.html(`
            <div class="text-center py-4">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhum agendamento encontrado para esta data</p>
            </div>
        `);
        return;
    }
    
    let html = '<div class="table-responsive"><table class="table table-bordered">';
    html += '<thead><tr><th>Horário</th><th>Paciente</th><th>Procedimentos</th><th>Status</th><th>Ações</th></tr></thead>';
    html += '<tbody>';
    
    scheduleData.forEach(function(appointment) {
        const statusClass = getStatusClass(appointment.status);
        const statusText = getStatusText(appointment.status);
        
        html += `
            <tr>
                <td>${appointment.time}</td>
                <td>${appointment.patient_name}</td>
                <td>${appointment.procedures.join(', ')}</td>
                <td><span class="badge ${statusClass}">${statusText}</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewAppointment(${appointment.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="editAppointment(${appointment.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    container.html(html);
}

function displayTodayAppointments(appointments) {
    const container = $('#todayAppointments');
    
    if (appointments.length === 0) {
        container.html(`
            <div class="text-center py-4">
                <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                <p class="text-muted">Nenhum agendamento para hoje</p>
            </div>
        `);
        return;
    }
    
    let html = '<div class="row">';
    
    appointments.forEach(function(appointment) {
        const statusClass = getStatusClass(appointment.status);
        const statusText = getStatusText(appointment.status);
        
        html += `
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title">${appointment.patient_name}</h6>
                                <p class="card-text">
                                    <i class="fas fa-clock me-1"></i>${appointment.time}
                                    <br>
                                    <i class="fas fa-stethoscope me-1"></i>${appointment.procedures.join(', ')}
                                </p>
                            </div>
                            <div>
                                <span class="badge ${statusClass}">${statusText}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.html(html);
}

function displayScheduleError() {
    const container = $('#scheduleCalendar');
    container.html(`
        <div class="text-center py-4">
            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <p class="text-muted">Erro ao carregar agenda</p>
            <button class="btn btn-sm btn-outline-primary" onclick="loadSchedule()">
                <i class="fas fa-refresh me-1"></i>Tentar novamente
            </button>
        </div>
    `);
}

function loadPatients() {
    $.ajax({
        url: '/api/dentist/patients',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const select = $('#patientSelect');
                select.empty().append('<option value="">Selecione um paciente</option>');
                response.data.forEach(function(patient) {
                    select.append(`<option value="${patient.id}">${patient.name}</option>`);
                });
            }
        }
    });
}

function loadProcedures() {
    $.ajax({
        url: '/api/procedures',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const select = $('#proceduresSelect');
                select.empty();
                response.data.forEach(function(procedure) {
                    select.append(`<option value="${procedure.id}">${procedure.name}</option>`);
                });
            }
        }
    });
}

function loadAvailableSlots() {
    const date = $('#appointmentDate').val();
    if (!date) return;
    
    $.ajax({
        url: '/api/dentist/available-slots',
        method: 'GET',
        data: { date: date },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update time input with available slots
                console.log('Available slots:', response.data);
            }
        }
    });
}

function saveAppointment() {
    const formData = {
        patient_id: $('#patientSelect').val(),
        appointment_date: $('#appointmentDate').val(),
        appointment_time: $('#appointmentTime').val(),
        duration: $('#duration').val(),
        procedure_ids: $('#proceduresSelect').val(),
        notes: $('#notes').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };
    
    $.ajax({
        url: '/api/dentist/appointments',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#newAppointmentModal').modal('hide');
                loadSchedule();
                showToast('Agendamento criado com sucesso!', 'success');
            } else {
                showToast('Erro ao criar agendamento: ' + response.message, 'error');
            }
        },
        error: function() {
            showToast('Erro ao criar agendamento', 'error');
        }
    });
}

function getStatusClass(status) {
    const classes = {
        'scheduled': 'bg-primary',
        'confirmed': 'bg-success',
        'completed': 'bg-info',
        'cancelled': 'bg-danger',
        'no_show': 'bg-warning'
    };
    return classes[status] || 'bg-secondary';
}

function getStatusText(status) {
    const texts = {
        'scheduled': 'Agendado',
        'confirmed': 'Confirmado',
        'completed': 'Concluído',
        'cancelled': 'Cancelado',
        'no_show': 'Não compareceu'
    };
    return texts[status] || status;
}

function viewAppointment(appointmentId) {
    // Implementar visualização de agendamento
    showToast('Funcionalidade em desenvolvimento', 'info');
}

function editAppointment(appointmentId) {
    // Implementar edição de agendamento
    showToast('Funcionalidade em desenvolvimento', 'info');
}

function showToast(message, type) {
    // Implementar toast notification
    alert(message);
}
</script>
@endsection
