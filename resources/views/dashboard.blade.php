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
    @include('admin.dashboard')
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
        console.log('Carregando dashboard admin');
        loadAdminDashboardData();
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

// Admin dashboard functions
function loadAdminDashboardData() {
    console.log('Carregando dados do dashboard admin...');
    
    // Carregar estatísticas do dashboard
    $.ajax({
        url: '/dashboard/stats',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Estatísticas admin recebidas:', response);
            if (response.success) {
                updateAdminDashboardStats(response.data);
            } else {
                console.log('Erro nas estatísticas admin:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro ao carregar estatísticas admin:', error, xhr.responseText);
        }
    });
    
    // Carregar dados dos gráficos
    $.ajax({
        url: '/admin/reports/data',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Dados dos gráficos admin recebidos:', response);
            if (response.success) {
                updateAdminCharts(response.charts);
            } else {
                console.log('Erro nos dados dos gráficos admin:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro ao carregar dados dos gráficos admin:', error, xhr.responseText);
        }
    });
}

// Admin dashboard stats update
function updateAdminDashboardStats(data) {
    console.log('Atualizando stats do admin com dados:', data);
    
    // Atualizar estatísticas do admin
    if (data.total_patients !== undefined) {
        const element = $('#totalPatients');
        console.log('Elemento totalPatients encontrado:', element.length);
        element.text(data.total_patients);
    }
    if (data.total_dentists !== undefined) {
        const element = $('#totalDentists');
        console.log('Elemento totalDentists encontrado:', element.length);
        element.text(data.total_dentists);
    }
    if (data.today_appointments !== undefined) {
        const element = $('#todayAppointments');
        console.log('Elemento todayAppointments encontrado:', element.length);
        element.text(data.today_appointments);
    }
    if (data.total_revenue !== undefined) {
        const element = $('#totalRevenue');
        console.log('Elemento totalRevenue encontrado:', element.length);
        element.text('R$ ' + data.total_revenue.toLocaleString('pt-BR', {minimumFractionDigits: 2}));
    }
}

// Admin charts update
function updateAdminCharts(chartsData) {
    console.log('Atualizando gráficos do admin com dados:', chartsData);
    
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

// Funções dos gráficos do admin
function updateStatusChart(data) {
    const ctx = document.getElementById('statusChart');
    if (!ctx) {
        console.log('Elemento statusChart não encontrado');
        return;
    }
    
    console.log('Criando gráfico de status com dados:', data);
    
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
    if (!ctx) {
        console.log('Elemento dentistsChart não encontrado');
        return;
    }
    
    console.log('Criando gráfico de dentistas com dados:', data);
    
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
    if (!ctx) {
        console.log('Elemento revenueChart não encontrado');
        return;
    }
    
    console.log('Criando gráfico de receita com dados:', data);
    
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