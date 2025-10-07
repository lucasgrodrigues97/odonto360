@extends('layouts.app')

@section('title', 'Dashboard Administrativo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard Administrativo</h2>
            <hr>
        </div>
    </div>
    
    <!-- Navegação por Abas -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
                        <i class="fas fa-chart-pie me-2"></i>Visão Geral
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab" aria-controls="appointments" aria-selected="false">
                        <i class="fas fa-calendar-alt me-2"></i>Agendamentos
                    </button>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Conteúdo das Abas -->
    <div class="tab-content" id="adminTabsContent">
        <!-- Aba Visão Geral -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
    
    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Pacientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPatients">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total de Dentistas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalDentists">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-md fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Agendamentos Hoje
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="todayAppointments">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Receita Mensal
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="monthlyRevenue">R$ 0,00</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <div class="col-xl-4 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Consultas por Status</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Consultas por Dentista</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="dentistsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Receita Mensal</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabelas de Dados Recentes -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Agendamentos Recentes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="recentAppointmentsTable">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Dentista</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dados carregados via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dentistas Mais Ativos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="activeDentistsTable">
                            <thead>
                                <tr>
                                    <th>Dentista</th>
                                    <th>Agendamentos</th>
                                    <th>Receita</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dados carregados via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
        
        <!-- Aba Agendamentos -->
        <div class="tab-pane fade" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Gerenciar Agendamentos</h6>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadAppointments()">
                                    <i class="fas fa-sync-alt me-1"></i>Atualizar
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
                                    <i class="fas fa-plus me-1"></i>Novo Agendamento
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="appointmentsTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Paciente</th>
                                            <th>Dentista</th>
                                            <th>Data</th>
                                            <th>Horário</th>
                                            <th>Status</th>
                                            <th>Procedimentos</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Dados carregados via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Filtrar por Status:</label>
                    <select class="form-select" id="statusFilter" onchange="filterAppointments()">
                        <option value="">Todos</option>
                        <option value="scheduled">Agendado</option>
                        <option value="confirmed">Confirmado</option>
                        <option value="completed">Concluído</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="dentistFilter" class="form-label">Filtrar por Dentista:</label>
                    <select class="form-select" id="dentistFilter" onchange="filterAppointments()">
                        <option value="">Todos</option>
                        <!-- Carregado via AJAX -->
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="dateFromFilter" class="form-label">Data Inicial:</label>
                    <input type="date" class="form-control" id="dateFromFilter" onchange="filterAppointments()">
                </div>
                <div class="col-md-3">
                    <label for="dateToFilter" class="form-label">Data Final:</label>
                    <input type="date" class="form-control" id="dateToFilter" onchange="filterAppointments()">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Novo Agendamento -->
<div class="modal fade" id="newAppointmentModal" tabindex="-1" aria-labelledby="newAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newAppointmentModalLabel">Novo Agendamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newAppointmentForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="patientSelect" class="form-label">Paciente *</label>
                                <select class="form-select" id="patientSelect" required>
                                    <option value="">Selecione um paciente</option>
                                    <!-- Carregado via AJAX -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dentistSelect" class="form-label">Dentista *</label>
                                <select class="form-select" id="dentistSelect" required>
                                    <option value="">Selecione um dentista</option>
                                    <!-- Carregado via AJAX -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="appointmentDate" class="form-label">Data *</label>
                                <input type="date" class="form-control" id="appointmentDate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="appointmentTime" class="form-label">Horário *</label>
                                <input type="time" class="form-control" id="appointmentTime" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="appointmentReason" class="form-label">Motivo da Consulta</label>
                        <textarea class="form-control" id="appointmentReason" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="createAppointment()">Criar Agendamento</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>


$(document).ready(function() {
    
    // Carregar dados do dashboard
    loadDashboardData();
    
    // Inicializar gráficos
    initCharts();
    
    // Inicializar DataTable para agendamentos
    initAppointmentsTable();
    
    // Carregar dados para os filtros
    loadFilterData();
});

function loadDashboardData() {
    console.log('Carregando dados do dashboard admin...');
    
    // Carregar estatísticas do dashboard
    $.ajax({
        url: '/dashboard/stats',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Estatísticas recebidas:', response);
            if (response.success) {
                updateDashboardStats(response.data);
            } else {
                console.log('Erro nas estatísticas:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro ao carregar estatísticas do dashboard:', error, xhr.responseText);
        }
    });
    
    // Carregar dados dos gráficos
    $.ajax({
        url: '/admin/reports/data',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Dados dos gráficos recebidos:', response);
            if (response.success) {
                updateCharts(response.charts);
            } else {
                console.log('Erro nos dados dos gráficos:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro ao carregar dados dos gráficos:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
        }
    });
}

function updateDashboardStats(data) {
    $('#totalPatients').text(data.total_patients || 0);
    $('#totalDentists').text(data.total_dentists || 0);
    $('#todayAppointments').text(data.today_appointments || 0);
    $('#monthlyRevenue').text('R$ ' + (data.monthly_revenue || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2}));
}

function updateCharts(chartsData) {
    
    // Atualizar gráfico de status
    if (chartsData.status) {
        updateStatusChart(chartsData.status);
    }
    
    // Atualizar gráfico de dentistas
    if (chartsData.dentists) {
        updateDentistsChart(chartsData.dentists);
    }
    
    // Atualizar gráfico de receita
    if (chartsData.revenue) {
        updateRevenueChart(chartsData.revenue);
    }
}

function updateStatusChart(data) {
    const ctx = document.getElementById('statusChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels || ['Agendado', 'Confirmado', 'Concluído', 'Cancelado'],
            datasets: [{
                data: data.data || [0, 0, 0, 0],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function updateDentistsChart(data) {
    const ctx = document.getElementById('dentistsChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels || [],
            datasets: [{
                label: 'Agendamentos',
                data: data.data || [],
                backgroundColor: '#36A2EB'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function updateRevenueChart(data) {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels || [],
            datasets: [{
                label: 'Receita',
                data: data.data || [],
                borderColor: '#4BC0C0',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function initCharts() {
    
    // Verificar se o Chart.js está carregado
    if (typeof Chart === 'undefined') {
        console.error('Chart.js não está carregado!');
        return;
    }
    
    // Gráfico de agendamentos por mês
    const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
    new Chart(appointmentsCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Agendamentos',
                data: [12, 19, 3, 5, 2, 3],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Gráfico de procedimentos
    const proceduresCtx = document.getElementById('proceduresChart').getContext('2d');
    new Chart(proceduresCtx, {
        type: 'doughnut',
        data: {
            labels: ['Limpeza', 'Restauração', 'Extração', 'Ortodontia'],
            datasets: [{
                data: [30, 25, 20, 25],
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

// Funções para gerenciar agendamentos
function initAppointmentsTable() {
    if ($.fn.DataTable) {
        $('#appointmentsTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
            },
            ajax: {
                url: '/admin/appointments/data',
                type: 'GET'
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
                const patientSelect = $('#patientSelect');
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
</script>
@endsection
