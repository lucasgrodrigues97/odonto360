@extends('layouts.app')

@section('title', 'Minhas Estatísticas')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Minhas Estatísticas</h1>
        <div>
            <button class="btn btn-primary" onclick="exportStatistics()">
                <i class="fas fa-download me-1"></i>Exportar Relatório
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Período</h6>
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
                    <label for="periodPreset" class="form-label">Período Rápido</label>
                    <select class="form-select" id="periodPreset" onchange="setPeriodPreset()">
                        <option value="">Personalizado</option>
                        <option value="today">Hoje</option>
                        <option value="week">Esta Semana</option>
                        <option value="month" selected>Este Mês</option>
                        <option value="quarter">Este Trimestre</option>
                        <option value="year">Este Ano</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary" onclick="loadStatistics()">
                        <i class="fas fa-search me-1"></i>Carregar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-uppercase small font-weight-bold">Total Consultas</div>
                            <div class="h3 mb-0" id="totalAppointments">-</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-uppercase small font-weight-bold">Consultas Concluídas</div>
                            <div class="h3 mb-0" id="completedAppointments">-</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-uppercase small font-weight-bold">Taxa de Comparecimento</div>
                            <div class="h3 mb-0" id="attendanceRate">-</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-uppercase small font-weight-bold">Receita Total</div>
                            <div class="h3 mb-0" id="totalRevenue">-</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Consultas por Status</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Consultas por Dia da Semana</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="weeklyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Procedimentos Mais Realizados</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="proceduresTable">
                            <thead>
                                <tr>
                                    <th>Procedimento</th>
                                    <th>Quantidade</th>
                                    <th>Receita</th>
                                    <th>% do Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Resumo Mensal</h6>
                </div>
                <div class="card-body">
                    <div id="monthlySummary">
                        <!-- Monthly summary will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Estatísticas de Pacientes</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary" id="totalPatients">-</h4>
                                <p class="text-muted">Total de Pacientes</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success" id="newPatients">-</h4>
                                <p class="text-muted">Novos Pacientes</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-info" id="returningPatients">-</h4>
                                <p class="text-muted">Pacientes Retornando</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-warning" id="avgAppointmentsPerPatient">-</h4>
                                <p class="text-muted">Média Consultas/Paciente</p>
                            </div>
                        </div>
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
    loadStatistics();
});

function loadStatistics() {
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    
    $.ajax({
        url: '/api/dentist/statistics',
        method: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateSummaryCards(response.summary);
                updateCharts(response.charts);
                updateProceduresTable(response.procedures);
                updateMonthlySummary(response.monthly);
                updatePatientStats(response.patients);
            }
        },
        error: function() {
            showToast('Erro ao carregar estatísticas', 'error');
        }
    });
}

function updateSummaryCards(summary) {
    $('#totalAppointments').text(summary.total_appointments || 0);
    $('#completedAppointments').text(summary.completed_appointments || 0);
    $('#attendanceRate').text((summary.attendance_rate || 0) + '%');
    $('#totalRevenue').text('R$ ' + (summary.total_revenue || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2}));
}

function updateCharts(chartsData) {
    // Status Chart
    if (chartsData.status) {
        updateStatusChart(chartsData.status);
    }
    
    // Weekly Chart
    if (chartsData.weekly) {
        updateWeeklyChart(chartsData.weekly);
    }
}

function updateStatusChart(data) {
    const ctx = document.getElementById('statusChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels || ['Concluído', 'Agendado', 'Cancelado', 'Não Compareceu'],
            datasets: [{
                data: data.data || [0, 0, 0, 0],
                backgroundColor: ['#28a745', '#007bff', '#dc3545', '#ffc107']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function updateWeeklyChart(data) {
    const ctx = document.getElementById('weeklyChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels || ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
            datasets: [{
                label: 'Consultas',
                data: data.data || [0, 0, 0, 0, 0, 0, 0],
                backgroundColor: '#007bff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function updateProceduresTable(procedures) {
    const tbody = $('#proceduresTable tbody');
    tbody.empty();
    
    if (procedures.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="4" class="text-center py-4">
                    <i class="fas fa-stethoscope fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Nenhum procedimento encontrado</p>
                </td>
            </tr>
        `);
        return;
    }
    
    procedures.forEach(function(procedure) {
        tbody.append(`
            <tr>
                <td>${procedure.name}</td>
                <td>${procedure.quantity}</td>
                <td>R$ ${procedure.revenue.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                <td>${procedure.percentage}%</td>
            </tr>
        `);
    });
}

function updateMonthlySummary(monthly) {
    const container = $('#monthlySummary');
    
    if (!monthly) {
        container.html('<p class="text-muted">Nenhum dado disponível</p>');
        return;
    }
    
    container.html(`
        <div class="mb-3">
            <h6>Consultas</h6>
            <p class="mb-1">Este mês: <strong>${monthly.this_month || 0}</strong></p>
            <p class="mb-1">Mês anterior: <strong>${monthly.last_month || 0}</strong></p>
            <p class="mb-0">
                Variação: 
                <span class="${monthly.variation >= 0 ? 'text-success' : 'text-danger'}">
                    ${monthly.variation >= 0 ? '+' : ''}${monthly.variation || 0}%
                </span>
            </p>
        </div>
        <div class="mb-3">
            <h6>Receita</h6>
            <p class="mb-1">Este mês: <strong>R$ ${(monthly.this_month_revenue || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</strong></p>
            <p class="mb-1">Mês anterior: <strong>R$ ${(monthly.last_month_revenue || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</strong></p>
        </div>
    `);
}

function updatePatientStats(patients) {
    $('#totalPatients').text(patients.total || 0);
    $('#newPatients').text(patients.new || 0);
    $('#returningPatients').text(patients.returning || 0);
    $('#avgAppointmentsPerPatient').text((patients.avg_appointments || 0).toFixed(1));
}

function setPeriodPreset() {
    const preset = $('#periodPreset').val();
    const today = new Date();
    
    switch(preset) {
        case 'today':
            $('#startDate').val(today.toISOString().split('T')[0]);
            $('#endDate').val(today.toISOString().split('T')[0]);
            break;
        case 'week':
            const weekStart = new Date(today);
            weekStart.setDate(today.getDate() - today.getDay());
            $('#startDate').val(weekStart.toISOString().split('T')[0]);
            $('#endDate').val(today.toISOString().split('T')[0]);
            break;
        case 'month':
            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
            $('#startDate').val(monthStart.toISOString().split('T')[0]);
            $('#endDate').val(today.toISOString().split('T')[0]);
            break;
        case 'quarter':
            const quarterStart = new Date(today.getFullYear(), Math.floor(today.getMonth() / 3) * 3, 1);
            $('#startDate').val(quarterStart.toISOString().split('T')[0]);
            $('#endDate').val(today.toISOString().split('T')[0]);
            break;
        case 'year':
            const yearStart = new Date(today.getFullYear(), 0, 1);
            $('#startDate').val(yearStart.toISOString().split('T')[0]);
            $('#endDate').val(today.toISOString().split('T')[0]);
            break;
    }
}

function exportStatistics() {
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    
    window.open(`/api/dentist/statistics/export?start_date=${startDate}&end_date=${endDate}`, '_blank');
}

function showToast(message, type) {
    // Implementar toast notification
    alert(message);
}
</script>
@endsection
