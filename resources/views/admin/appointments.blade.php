@extends('layouts.app')

@section('title', 'Gerenciar Agendamentos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-calendar-alt me-2"></i>Gerenciar Agendamentos</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
                    <i class="fas fa-plus me-1"></i>Novo Agendamento
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="statusFilter" class="form-label">Status</label>
                                <select class="form-select" id="statusFilter">
                                    <option value="">Todos</option>
                                    <option value="scheduled">Agendado</option>
                                    <option value="confirmed">Confirmado</option>
                                    <option value="completed">Concluído</option>
                                    <option value="cancelled">Cancelado</option>
                                    <option value="no_show">Não Compareceu</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="dentistFilter" class="form-label">Dentista</label>
                                <select class="form-select" id="dentistFilter">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="dateFrom" class="form-label">Data Inicial</label>
                                <input type="date" class="form-control" id="dateFrom">
                            </div>
                            <div class="col-md-3">
                                <label for="dateTo" class="form-label">Data Final</label>
                                <input type="date" class="form-control" id="dateTo">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="button" class="btn btn-primary" onclick="filterAppointments()">
                                    <i class="fas fa-search me-1"></i>Filtrar
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                                    <i class="fas fa-times me-1"></i>Limpar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Agendamentos -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="appointmentsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Data</th>
                                    <th>Horário</th>
                                    <th>Paciente</th>
                                    <th>Dentista</th>
                                    <th>Procedimentos</th>
                                    <th>Status</th>
                                    <th>Valor</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Novo Agendamento -->
<div class="modal fade" id="newAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Agendamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="newAppointmentForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="patientSelect" class="form-label">Paciente</label>
                                <select class="form-select" id="patientSelect" required>
                                    <option value="">Selecione um paciente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dentistSelect" class="form-label">Dentista</label>
                                <select class="form-select" id="dentistSelect" required>
                                    <option value="">Selecione um dentista</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="appointmentDate" class="form-label">Data</label>
                                <input type="date" class="form-control" id="appointmentDate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="appointmentTime" class="form-label">Horário</label>
                                <input type="time" class="form-control" id="appointmentTime" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="proceduresSelect" class="form-label">Procedimentos</label>
                        <select class="form-select" id="proceduresSelect" multiple>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Observações</label>
                        <textarea class="form-control" id="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveAppointment()">Salvar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    loadAppointments();
    loadDentists();
    loadPatients();
    loadProcedures();
});

function loadAppointments() {
    $.ajax({
        url: '/admin/appointments/data',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                displayAppointments(response.data);
            } else {
                showError('Erro ao carregar agendamentos: ' + response.message);
            }
        },
        error: function() {
            showError('Erro ao carregar agendamentos');
        }
    });
}

function displayAppointments(appointments) {
    const tbody = $('#appointmentsTable tbody');
    tbody.empty();
    
    if (appointments.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="9" class="text-center text-muted">
                    <i class="fas fa-calendar-times fa-2x mb-2"></i><br>
                    Nenhum agendamento encontrado
                </td>
            </tr>
        `);
        return;
    }
    
    appointments.forEach(function(appointment) {
        const row = `
            <tr>
                <td>${appointment.id}</td>
                <td>${formatDate(appointment.appointment_date)}</td>
                <td>${formatTime(appointment.appointment_time)}</td>
                <td>${appointment.patient_name}</td>
                <td>${appointment.dentist_name}</td>
                <td>${appointment.procedures || '-'}</td>
                <td><span class="badge ${getStatusClass(appointment.status)}">${getStatusText(appointment.status)}</span></td>
                <td>R$ ${appointment.total_value || '0,00'}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-primary" onclick="editAppointment(${appointment.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="confirmAppointment(${appointment.id})">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="cancelAppointment(${appointment.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function loadDentists() {
    $.ajax({
        url: '/admin/dentists/data',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#dentistFilter, #dentistSelect');
                select.empty().append('<option value="">Selecione um dentista</option>');
                response.data.forEach(function(dentist) {
                    select.append(`<option value="${dentist.id}">${dentist.name}</option>`);
                });
            }
        }
    });
}

function loadPatients() {
    $.ajax({
        url: '/admin/patients/data',
        method: 'GET',
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
        url: '/admin/procedures/data',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#proceduresSelect');
                select.empty();
                response.data.forEach(function(procedure) {
                    select.append(`<option value="${procedure.id}">${procedure.name} - R$ ${procedure.price}</option>`);
                });
            }
        }
    });
}

function filterAppointments() {
    const status = $('#statusFilter').val();
    const dentist = $('#dentistFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    $.ajax({
        url: '/admin/appointments/data',
        method: 'GET',
        data: {
            status: status,
            dentist_id: dentist,
            date_from: dateFrom,
            date_to: dateTo
        },
        success: function(response) {
            if (response.success) {
                displayAppointments(response.data);
            }
        }
    });
}

function clearFilters() {
    $('#filterForm')[0].reset();
    loadAppointments();
}

function editAppointment(id) {
    // Implementar edição
    alert('Editar agendamento ' + id);
}

function confirmAppointment(id) {
    if (confirm('Confirmar este agendamento?')) {
        $.ajax({
            url: `/admin/appointments/${id}/confirm`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    loadAppointments();
                    showSuccess('Agendamento confirmado com sucesso!');
                } else {
                    showError('Erro ao confirmar agendamento');
                }
            }
        });
    }
}

function cancelAppointment(id) {
    if (confirm('Cancelar este agendamento?')) {
        $.ajax({
            url: `/admin/appointments/${id}/cancel`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    loadAppointments();
                    showSuccess('Agendamento cancelado com sucesso!');
                } else {
                    showError('Erro ao cancelar agendamento');
                }
            }
        });
    }
}

function saveAppointment() {
    const formData = {
        patient_id: $('#patientSelect').val(),
        dentist_id: $('#dentistSelect').val(),
        appointment_date: $('#appointmentDate').val(),
        appointment_time: $('#appointmentTime').val(),
        procedure_ids: $('#proceduresSelect').val(),
        notes: $('#notes').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };
    
    $.ajax({
        url: '/admin/appointments',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                $('#newAppointmentModal').modal('hide');
                loadAppointments();
                showSuccess('Agendamento criado com sucesso!');
            } else {
                showError('Erro ao criar agendamento: ' + response.message);
            }
        },
        error: function() {
            showError('Erro ao criar agendamento');
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
        'no_show': 'Não Compareceu'
    };
    return texts[status] || status;
}

function formatDate(dateString) {
    if (!dateString) return '-';
    
    if (dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
        const [year, month, day] = dateString.split('-');
        return `${day}/${month}/${year}`;
    }
    
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '-';
    
    return date.toLocaleDateString('pt-BR');
}

function formatTime(timeString) {
    if (!timeString) return '-';
    
    // Se for uma string de tempo (HH:MM:SS), pegar apenas HH:MM
    if (timeString.includes(':')) {
        return timeString.substring(0, 5);
    }
    
    // Se for uma data completa, extrair apenas o horário
    if (timeString.includes('T')) {
        const date = new Date(timeString);
        return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    }
    
    return timeString;
}

function showSuccess(message) {
    // Implementar notificação de sucesso
    alert(message);
}

function showError(message) {
    // Implementar notificação de erro
    alert(message);
}
</script>
@endsection
