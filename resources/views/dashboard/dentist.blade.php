<!-- Dentist Dashboard -->
<div class="row g-4 mb-4">
    <!-- Quick Stats -->
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Consultas Hoje</h6>
                        <h3 class="mb-0" id="todayAppointments">-</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-day fa-2x opacity-75"></i>
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
                        <h6 class="card-title">Pacientes Únicos</h6>
                        <h3 class="mb-0" id="uniquePatients">-</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x opacity-75"></i>
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
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Taxa de Cancelamento</h6>
                        <h3 class="mb-0" id="cancellationRate">-</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-times-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row g-4">
    <!-- Today's Schedule -->
    <div class="col-xl-6">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-day me-2"></i>Agenda de Hoje
                </h6>
                <div>
                    <button class="btn btn-sm btn-outline-primary me-2" onclick="loadTodaySchedule()">
                        <i class="fas fa-sync-alt me-1"></i>
                        Atualizar
                    </button>
                    <a href="{{ route('dentist.appointments') }}" class="btn btn-sm btn-outline-secondary">
                        Ver Todas
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div id="todaySchedule">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-xl-6">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt me-2"></i>Ações Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('dentist.appointments') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-alt me-2"></i>Ver Agendamentos
                    </a>
                    <a href="{{ route('dentist.patients') }}" class="btn btn-outline-primary">
                        <i class="fas fa-users me-2"></i>Meus Pacientes
                    </a>
                    <a href="{{ route('dentist.schedule') }}" class="btn btn-outline-primary">
                        <i class="fas fa-clock me-2"></i>Configurar Agenda
                    </a>
                    <a href="{{ route('dentist.statistics') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar me-2"></i>Ver Estatísticas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mt-4">
    <!-- Appointments by Status -->
    <div class="col-xl-6">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie me-2"></i>Consultas por Status
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="appointmentsStatusChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Monthly Revenue -->
    <div class="col-xl-6">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line me-2"></i>Receita Mensal
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="monthlyRevenueChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Patients -->
<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user-friends me-2"></i>Pacientes Recentes
                </h6>
            </div>
            <div class="card-body">
                <div id="recentPatients">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    console.log('Dashboard dentista carregando...');
    
    // Carregar dados específicos do dentista
    loadTodaySchedule();
    loadRecentPatients();
    loadCharts();
});

function loadTodaySchedule() {
    console.log('Carregando agenda de hoje...');
    
    $.ajax({
        url: '/api/dentist/appointments/today',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Agenda de hoje recebida:', response);
            if (response.success) {
                displayTodaySchedule(response.data);
            } else {
                displayTodayScheduleError();
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro ao carregar agenda de hoje:', error);
            displayTodayScheduleError();
        }
    });
}

function displayTodaySchedule(appointments) {
    const container = $('#todaySchedule');
    
    if (appointments.length === 0) {
        container.html(`
            <div class="text-center py-4">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhum agendamento para hoje</p>
            </div>
        `);
        return;
    }
    
    let html = '<div class="list-group">';
    appointments.forEach(appointment => {
        const statusClass = getStatusClass(appointment.status);
        const statusText = getStatusText(appointment.status);
        
        html += `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">${appointment.patient_name}</h6>
                    <p class="mb-1 text-muted">${appointment.time} - ${appointment.procedures ? appointment.procedures.join(', ') : 'N/A'}</p>
                </div>
                <span class="badge ${statusClass}">${statusText}</span>
            </div>
        `;
    });
    html += '</div>';
    
    container.html(html);
}

function displayTodayScheduleError() {
    const container = $('#todaySchedule');
    container.html(`
        <div class="text-center py-4">
            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <p class="text-muted">Erro ao carregar agenda</p>
            <button class="btn btn-sm btn-outline-primary" onclick="loadTodaySchedule()">
                <i class="fas fa-refresh me-1"></i>Tentar novamente
            </button>
        </div>
    `);
}

function loadRecentPatients() {
    console.log('Carregando pacientes recentes...');
    
    $.ajax({
        url: '/api/dentist/patients/recent',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Pacientes recentes recebidos:', response);
            if (response.success) {
                displayRecentPatients(response.data);
            } else {
                displayRecentPatientsError();
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro ao carregar pacientes recentes:', error);
            displayRecentPatientsError();
        }
    });
}

function displayRecentPatients(patients) {
    const container = $('#recentPatients');
    
    if (patients.length === 0) {
        container.html(`
            <div class="text-center py-4">
                <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhum paciente recente</p>
            </div>
        `);
        return;
    }
    
    let html = '<div class="list-group">';
    patients.forEach(patient => {
        html += `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">${patient.name}</h6>
                    <p class="mb-1 text-muted">Última consulta: ${patient.last_appointment || 'Nunca'}</p>
                </div>
                <span class="badge bg-primary">${patient.appointments_count || 0} consultas</span>
            </div>
        `;
    });
    html += '</div>';
    
    container.html(html);
}

function displayRecentPatientsError() {
    const container = $('#recentPatients');
    container.html(`
        <div class="text-center py-4">
            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <p class="text-muted">Erro ao carregar pacientes</p>
            <button class="btn btn-sm btn-outline-primary" onclick="loadRecentPatients()">
                <i class="fas fa-refresh me-1"></i>Tentar novamente
            </button>
        </div>
    `);
}

function loadCharts() {
    console.log('Carregando gráficos...');
    
    // Carregar gráfico de status
    $.ajax({
        url: '/api/dentist/charts/appointments-status',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Dados do gráfico de status recebidos:', response);
            if (response.success) {
                createAppointmentsStatusChart(response.data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro ao carregar gráfico de status:', error);
        }
    });
    
    // Carregar gráfico de receita mensal
    $.ajax({
        url: '/api/dentist/charts/monthly-revenue',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Dados do gráfico de receita recebidos:', response);
            if (response.success) {
                createMonthlyRevenueChart(response.data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro ao carregar gráfico de receita:', error);
        }
    });
}

function createAppointmentsStatusChart(data) {
    const ctx = document.getElementById('appointmentsStatusChart');
    if (!ctx) {
        console.log('Elemento appointmentsStatusChart não encontrado');
        return;
    }
    
    console.log('Criando gráfico de status com dados:', data);
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels || ['Concluído', 'Agendado', 'Cancelado'],
            datasets: [{
                data: data.data || [0, 0, 0],
                backgroundColor: ['#28a745', '#007bff', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function createMonthlyRevenueChart(data) {
    const ctx = document.getElementById('monthlyRevenueChart');
    if (!ctx) {
        console.log('Elemento monthlyRevenueChart não encontrado');
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
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
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
</script>