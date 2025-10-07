@extends('layouts.app')

@section('title', 'Relatórios')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2><i class="fas fa-chart-bar me-2"></i>Relatórios</h2>
        </div>
    </div>

    <!-- Filtros de Período -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filtros de Período</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="startDate" class="form-label">Data Inicial</label>
                            <input type="date" class="form-control" id="startDate" value="{{ date('Y-m-01') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="endDate" class="form-label">Data Final</label>
                            <input type="date" class="form-control" id="endDate" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="dentistFilter" class="form-label">Dentista</label>
                            <select class="form-control" id="dentistFilter">
                                <option value="">Todos os dentistas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="statusFilter" class="form-label">Status</label>
                            <select class="form-control" id="statusFilter">
                                <option value="">Todos os status</option>
                                <option value="scheduled">Agendado</option>
                                <option value="confirmed">Confirmado</option>
                                <option value="completed">Concluído</option>
                                <option value="cancelled">Cancelado</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button class="btn btn-primary" onclick="generateReports()">
                                <i class="fas fa-chart-line me-2"></i>Gerar Relatórios
                            </button>
                            <button class="btn btn-success" onclick="exportReports()">
                                <i class="fas fa-download me-2"></i>Exportar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total de Consultas</h6>
                            <h3 class="mb-0" id="totalAppointments">-</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Consultas Concluídas</h6>
                            <h3 class="mb-0" id="completedAppointments">-</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Receita Total</h6>
                            <h3 class="mb-0" id="totalRevenue">-</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Taxa de Comparecimento</h6>
                            <h3 class="mb-0" id="attendanceRate">-</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Consultas por Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Consultas por Dentista</h5>
                </div>
                <div class="card-body">
                    <canvas id="dentistChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Receita Mensal -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Receita Mensal</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Consultas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detalhamento de Consultas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="appointmentsTable">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Horário</th>
                                    <th>Paciente</th>
                                    <th>Dentista</th>
                                    <th>Procedimentos</th>
                                    <th>Status</th>
                                    <th>Valor</th>
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    loadDentists();
    generateReports();
});

function loadDentists() {
    $.ajax({
        url: '/admin/dentists/data',
        method: 'GET',
        success: function(response) {
            const select = $('#dentistFilter');
            select.empty().append('<option value="">Todos os dentistas</option>');
            
            if (response.data) {
                response.data.forEach(function(dentist) {
                    select.append(`<option value="${dentist.id}">${dentist.name}</option>`);
                });
            }
        }
    });
}

function generateReports() {
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    const dentistId = $('#dentistFilter').val();
    const status = $('#statusFilter').val();

    $.ajax({
        url: '/admin/reports/data',
        method: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate,
            dentist_id: dentistId,
            status: status
        },
        success: function(response) {
            if (response.success) {
                updateSummaryCards(response.summary);
                updateCharts(response.charts);
                updateAppointmentsTable(response.appointments);
            }
        }
    });
}

function updateSummaryCards(summary) {
    $('#totalAppointments').text(summary.total_appointments || 0);
    $('#completedAppointments').text(summary.completed_appointments || 0);
    $('#totalRevenue').text('R$ ' + (summary.total_revenue || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2}));
    $('#attendanceRate').text((summary.attendance_rate || 0) + '%');
}

function updateCharts(charts) {
    // Gráfico de Status
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: charts.status.labels,
            datasets: [{
                data: charts.status.data,
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Gráfico de Dentistas
    const dentistCtx = document.getElementById('dentistChart').getContext('2d');
    new Chart(dentistCtx, {
        type: 'bar',
        data: {
            labels: charts.dentists.labels,
            datasets: [{
                label: 'Consultas',
                data: charts.dentists.data,
                backgroundColor: '#17a2b8'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Gráfico de Receita
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: charts.revenue.labels,
            datasets: [{
                label: 'Receita',
                data: charts.revenue.data,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function updateAppointmentsTable(appointments) {
    const tbody = $('#appointmentsTable tbody');
    tbody.empty();
    
    if (appointments.length === 0) {
        tbody.append('<tr><td colspan="7" class="text-center">Nenhuma consulta encontrada</td></tr>');
        return;
    }
    
    appointments.forEach(function(appointment) {
        const row = `
            <tr>
                <td>${appointment.date}</td>
                <td>${appointment.time || '-'}</td>
                <td>${appointment.patient_name}</td>
                <td>${appointment.dentist_name}</td>
                <td>${appointment.procedures.join(', ')}</td>
                <td><span class="badge bg-${getStatusColor(appointment.status)}">${getStatusText(appointment.status)}</span></td>
                <td>R$ ${appointment.total_value.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
            </tr>
        `;
        tbody.append(row);
    });
}

function exportReports() {
    // Implementar exportação
}

function getStatusColor(status) {
    const colors = {
        'scheduled': 'primary',
        'confirmed': 'success',
        'completed': 'info',
        'cancelled': 'danger'
    };
    return colors[status] || 'secondary';
}

function getStatusText(status) {
    const texts = {
        'scheduled': 'Agendado',
        'confirmed': 'Confirmado',
        'completed': 'Concluído',
        'cancelled': 'Cancelado'
    };
    return texts[status] || status;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}
</script>
@endsection
