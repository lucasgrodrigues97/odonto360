@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $user = auth()->user();
    $isAdmin = $user->hasRole('admin') || $user->email === 'admin@odonto360.com';
    $isDentist = $user->hasRole('dentist');
    $isPatient = $user->hasRole('patient');
@endphp

@if($isAdmin)
    @include('dashboard.admin')
@elseif($isDentist)
    @include('dashboard.dentist')
@elseif($isPatient)
    @include('dashboard.patient')
@else
    @include('dashboard.default')
@endif
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    console.log('Dashboard carregando...');
    
    // Load dashboard data based on user type
    @if($isAdmin)
        console.log('Carregando dashboard admin (tela antiga)');
        loadAdminAppointmentsData();
    @elseif($isDentist)
        console.log('Carregando dashboard dentista');
        loadDentistDashboardData();
    @elseif($isPatient)
        console.log('Carregando dashboard paciente');
        loadPatientDashboardData();
    @else
        console.log('Carregando dashboard padrão');
        loadDefaultDashboardData();
    @endif
});

// Admin dashboard functions (tela antiga de agendamentos)
function loadAdminAppointmentsData() {
    console.log('Carregando dados de agendamentos do admin...');
    
    // Inicializar DataTable para agendamentos
    initAppointmentsTable();
    
    // Carregar dados para os filtros
    loadFilterData();
}

function initAppointmentsTable() {
    console.log('Inicializando DataTable...');
    console.log('Elemento appointmentsTable encontrado:', $('#appointmentsTable').length);
    
    if ($.fn.DataTable) {
        console.log('DataTable disponível, criando tabela...');
        $('#appointmentsTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
            },
            ajax: {
                url: '/admin/appointments/data',
                type: 'GET',
                error: function(xhr, error, thrown) {
                    console.error('Erro ao carregar dados da tabela:', error, xhr.responseText);
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'patient_name', name: 'patient_name' },
                { data: 'dentist_name', name: 'dentist_name' },
                { data: 'appointment_date', name: 'appointment_date' },
                { data: 'appointment_time', name: 'appointment_time' },
                { data: 'status', name: 'status' },
                { data: 'procedures', name: 'procedures' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ]
        });
        console.log('DataTable criado com sucesso!');
    } else {
        console.error('DataTable não está disponível!');
    }
}

function loadAppointments() {
    if ($.fn.DataTable) {
        $('#appointmentsTable').DataTable().ajax.reload();
    }
}

function loadFilterData() {
    // Carregar dentistas para o filtro
    $.ajax({
        url: '/admin/dentists/data',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const dentistSelect = $('#dentistFilter');
                const dentistModalSelect = $('#dentistSelect');
                
                response.data.forEach(function(dentist) {
                    const option = `<option value="${dentist.id}">${dentist.name}</option>`;
                    dentistSelect.append(option);
                    dentistModalSelect.append(option);
                });
            }
        }
    });
    
    // Carregar pacientes para o modal
    $.ajax({
        url: '/admin/patients/data',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const patientSelect = $('#patientSelect');
                
                response.data.forEach(function(patient) {
                    const option = `<option value="${patient.id}">${patient.name}</option>`;
                    patientSelect.append(option);
                });
            }
        }
    });
}

function filterAppointments() {
    const status = $('#statusFilter').val();
    const dentist = $('#dentistFilter').val();
    const dateFrom = $('#dateFromFilter').val();
    const dateTo = $('#dateToFilter').val();
    
    if ($.fn.DataTable) {
        const table = $('#appointmentsTable').DataTable();
        
        // Aplicar filtros
        table.ajax.url(`/admin/appointments/data?status=${status}&dentist=${dentist}&date_from=${dateFrom}&date_to=${dateTo}`).load();
    }
}

function createAppointment() {
    const formData = {
        patient_id: $('#patientSelect').val(),
        dentist_id: $('#dentistSelect').val(),
        appointment_date: $('#appointmentDate').val(),
        appointment_time: $('#appointmentTime').val(),
        reason: $('#appointmentReason').val(),
        status: 'scheduled'
    };
    
    if (!formData.patient_id || !formData.dentist_id || !formData.appointment_date || !formData.appointment_time) {
        alert('Por favor, preencha todos os campos obrigatórios.');
        return;
    }
    
    $.ajax({
        url: '/api/appointments',
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                alert('Agendamento criado com sucesso!');
                $('#newAppointmentModal').modal('hide');
                $('#newAppointmentForm')[0].reset();
                loadAppointments();
            } else {
                alert('Erro ao criar agendamento: ' + response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert('Erro ao criar agendamento: ' + (response.message || 'Erro desconhecido'));
        }
    });
}

function updateAppointmentStatus(appointmentId, newStatus) {
    if (confirm('Tem certeza que deseja alterar o status deste agendamento?')) {
        $.ajax({
            url: `/api/appointments/${appointmentId}/status`,
            method: 'PUT',
            data: { status: newStatus },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Status atualizado com sucesso!');
                    loadAppointments();
                } else {
                    alert('Erro ao atualizar status: ' + response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert('Erro ao atualizar status: ' + (response.message || 'Erro desconhecido'));
            }
        });
    }
}

function cancelAppointment(appointmentId) {
    if (confirm('Tem certeza que deseja cancelar este agendamento?')) {
        $.ajax({
            url: `/api/appointments/${appointmentId}/cancel`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Agendamento cancelado com sucesso!');
                    loadAppointments();
                } else {
                    alert('Erro ao cancelar agendamento: ' + response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert('Erro ao cancelar agendamento: ' + (response.message || 'Erro desconhecido'));
            }
        });
    }
}

// Dentist dashboard functions
function loadDentistDashboardData() {
    console.log('Carregando dados do dashboard dentista...');
    
    $.ajax({
        url: '/dashboard/stats',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Resposta recebida:', response);
            if (response.success) {
                updateDentistDashboardStats(response.data);
            } else {
                console.log('Resposta sem sucesso:', response.message);
                updateDentistDashboardStats({
                    today_appointments: 0,
                    upcoming_appointments: 0,
                    completed_appointments: 0,
                    total_revenue: 0
                });
            }
        },
        error: function(xhr, status, error) {
            console.log('Erro AJAX:', error, xhr.responseText);
            updateDentistDashboardStats({
                today_appointments: 0,
                upcoming_appointments: 0,
                completed_appointments: 0,
                total_revenue: 0
            });
        }
    });
}

// Patient dashboard functions
function loadPatientDashboardData() {
    console.log('Carregando dados do dashboard paciente...');
    
    $.ajax({
        url: '/dashboard/stats',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Resposta recebida:', response);
            if (response.success) {
                updatePatientDashboardStats(response.data);
            } else {
                console.log('Resposta sem sucesso:', response.message);
                updatePatientDashboardStats({
                    upcoming_appointments: 0,
                    completed_appointments: 0,
                    total_appointments: 0
                });
            }
        },
        error: function(xhr, status, error) {
            console.log('Erro AJAX:', error, xhr.responseText);
            updatePatientDashboardStats({
                upcoming_appointments: 0,
                completed_appointments: 0,
                total_appointments: 0
            });
        }
    });
}

// Default dashboard functions
function loadDefaultDashboardData() {
    console.log('Carregando dados do dashboard padrão...');
    loadRecentAppointments();
}

function loadRecentAppointments() {
    console.log('Carregando agendamentos recentes...');
    
    $.ajax({
        url: '/dashboard/recent-appointments',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Agendamentos recebidos:', response);
            if (response.success) {
                displayRecentAppointments(response.data);
            } else {
                console.log('Erro nos agendamentos:', response.message);
                displayRecentAppointmentsError();
            }
        },
        error: function(xhr, status, error) {
            console.log('Erro AJAX agendamentos:', error, xhr.responseText);
            displayRecentAppointmentsError();
        }
    });
}

// Dentist dashboard stats update
function updateDentistDashboardStats(data) {
    console.log('Atualizando stats do dentista com dados:', data);
    
    // Atualizar consultas de hoje
    if (data.today_appointments !== undefined) {
        const element = $('#todayAppointments');
        console.log('Elemento todayAppointments encontrado:', element.length);
        element.text(data.today_appointments);
    }
    
    // Atualizar pacientes únicos (usando total_patients como fallback)
    if (data.unique_patients !== undefined) {
        const element = $('#uniquePatients');
        console.log('Elemento uniquePatients encontrado:', element.length);
        element.text(data.unique_patients);
    } else if (data.total_patients !== undefined) {
        const element = $('#uniquePatients');
        console.log('Elemento uniquePatients encontrado:', element.length);
        element.text(data.total_patients);
    }
    
    // Atualizar taxa de cancelamento (usando active_dentists como fallback)
    if (data.cancellation_rate !== undefined) {
        const element = $('#cancellationRate');
        console.log('Elemento cancellationRate encontrado:', element.length);
        element.text(data.cancellation_rate + '%');
    } else if (data.active_dentists !== undefined) {
        const element = $('#cancellationRate');
        console.log('Elemento cancellationRate encontrado:', element.length);
        element.text(data.active_dentists);
    }
    
    // Atualizar receita total
    if (data.total_revenue !== undefined) {
        const element = $('#totalRevenue');
        console.log('Elemento totalRevenue encontrado:', element.length);
        element.text('R$ ' + data.total_revenue.toLocaleString('pt-BR', {minimumFractionDigits: 2}));
    }
}

// Patient dashboard stats update
function updatePatientDashboardStats(data) {
    console.log('Atualizando stats do paciente com dados:', data);
    
    if (data.upcoming_appointments !== undefined) {
        const element = $('#upcomingAppointments');
        console.log('Elemento upcomingAppointments encontrado:', element.length);
        element.text(data.upcoming_appointments);
    }
    if (data.completed_appointments !== undefined) {
        const element = $('#completedAppointments');
        console.log('Elemento completedAppointments encontrado:', element.length);
        element.text(data.completed_appointments);
    }
    if (data.total_spent !== undefined) {
        const element = $('#totalSpent');
        console.log('Elemento totalSpent encontrado:', element.length);
        element.text('R$ ' + data.total_spent.toLocaleString('pt-BR', {minimumFractionDigits: 2}));
    }
}

function displayRecentAppointments(appointments) {
    console.log('Exibindo agendamentos recentes:', appointments);
    const container = $('#recentAppointments');
    console.log('Container recentAppointments encontrado:', container.length);
    
    if (appointments.length === 0) {
        container.html(`
            <div class="text-center py-4">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhum agendamento encontrado</p>
            </div>
        `);
        return;
    }
    
    let html = '<div class="table-responsive"><table class="table table-hover">';
    html += '<thead><tr><th>Data</th><th>Horário</th><th>Paciente</th><th>Dentista</th><th>Status</th></tr></thead>';
    html += '<tbody>';
    
    appointments.forEach(function(appointment) {
        const statusClass = getStatusClass(appointment.status);
        const statusText = getStatusText(appointment.status);
        
        html += `
            <tr>
                <td>${formatDate(appointment.date)}</td>
                <td>${formatTime(appointment.time)}</td>
                <td>${appointment.patient_name}</td>
                <td>${appointment.dentist_name}</td>
                <td><span class="badge ${statusClass}">${statusText}</span></td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    container.html(html);
}

function displayRecentAppointmentsError() {
    const container = $('#recentAppointments');
    container.html(`
        <div class="text-center py-4">
            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <p class="text-muted">Erro ao carregar agendamentos</p>
            <button class="btn btn-sm btn-outline-primary" onclick="loadRecentAppointments()">
                <i class="fas fa-refresh me-1"></i>Tentar novamente
            </button>
        </div>
    `);
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
    
    // Se for uma string de data no formato YYYY-MM-DD
    if (dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
        const [year, month, day] = dateString.split('-');
        return `${day}/${month}/${year}`;
    }
    
    // Se for uma data completa com timestamp
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '-';
    
    return date.toLocaleDateString('pt-BR');
}

function formatTime(timeString) {
    if (!timeString || timeString === null || timeString === undefined) {
        return '-';
    }
    
    // Se já está no formato HH:MM, retornar como está
    if (typeof timeString === 'string' && timeString.match(/^\d{2}:\d{2}$/)) {
        return timeString;
    }
    
    // Se for uma string de tempo (HH:MM:SS), pegar apenas HH:MM
    if (typeof timeString === 'string' && timeString.includes(':')) {
        const timeParts = timeString.split(':');
        if (timeParts.length >= 2) {
            return `${timeParts[0].padStart(2, '0')}:${timeParts[1].padStart(2, '0')}`;
        }
    }
    
    // Se for uma data completa, extrair apenas o horário
    if (typeof timeString === 'string' && timeString.includes('T')) {
        const date = new Date(timeString);
        if (!isNaN(date.getTime())) {
            return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        }
    }
    
    return '-';
}
</script>
@endsection