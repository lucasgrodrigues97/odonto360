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

<div class="row g-4">
    <!-- Today's Schedule -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-day me-2"></i>
                    Agenda de Hoje
                </h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadTodaySchedule()">
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
    
    <!-- Quick Actions & Stats -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('dentist.appointments') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Ver Agendamentos
                    </a>
                    <a href="{{ route('dentist.patients') }}" class="btn btn-outline-primary">
                        <i class="fas fa-users me-2"></i>
                        Meus Pacientes
                    </a>
                    <a href="{{ route('dentist.schedule') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-clock me-2"></i>
                        Configurar Agenda
                    </a>
                    <a href="{{ route('dentist.statistics') }}" class="btn btn-outline-info">
                        <i class="fas fa-chart-bar me-2"></i>
                        Ver Estatísticas
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Patients -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Pacientes Recentes
                </h5>
            </div>
            <div class="card-body">
                <div id="recentPatients">
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mt-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Consultas por Status
                </h5>
            </div>
            <div class="card-body">
                <canvas id="appointmentsStatusChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Receita Mensal
                </h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyRevenueChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
let appointmentsStatusChart = null;
let monthlyRevenueChart = null;

function loadDashboardData() {
    loadDentistStatistics();
    loadTodaySchedule();
    loadRecentPatients();
    loadCharts();
}

function loadDentistStatistics() {
    fetch('/api/dentists/profile/statistics', {
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Load today's appointments count
            loadTodayAppointmentsCount();
            
            document.getElementById('uniquePatients').textContent = data.data.unique_patients || 0;
            document.getElementById('totalRevenue').textContent = 'R$ ' + (data.data.total_revenue || 0).toFixed(2);
            
            const totalAppointments = data.data.total_appointments || 0;
            const cancelledAppointments = data.data.cancelled_appointments || 0;
            const cancellationRate = totalAppointments > 0 ? ((cancelledAppointments / totalAppointments) * 100).toFixed(1) : 0;
            document.getElementById('cancellationRate').textContent = cancellationRate + '%';
        }
    })
    .catch(error => {
        console.error('Error loading statistics:', error);
    });
}

function loadTodayAppointmentsCount() {
    const today = new Date().toISOString().split('T')[0];
    
    fetch(`/api/dentists/profile/appointments?start_date=${today}&end_date=${today}`, {
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('todayAppointments').textContent = data.data.total || 0;
        }
    })
    .catch(error => {
        console.error('Error loading today appointments count:', error);
    });
}

function loadTodaySchedule() {
    const today = new Date().toISOString().split('T')[0];
    
    fetch(`/api/dentists/profile/appointments?start_date=${today}&end_date=${today}`, {
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = document.getElementById('todaySchedule');
            
            if (data.data.data && data.data.data.length > 0) {
                let html = '';
                data.data.data.forEach(appointment => {
                    const appointmentTime = new Date(appointment.appointment_time);
                    
                    html += `
                        <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-${getStatusColor(appointment.status)} bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-clock text-${getStatusColor(appointment.status)}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">${appointment.patient.user.name}</h6>
                                    <small class="text-muted">
                                        ${appointmentTime.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'})} - ${appointment.duration} min
                                    </small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-${getStatusColor(appointment.status)}">${getStatusText(appointment.status)}</span>
                                <div class="mt-1">
                                    <small class="text-muted">R$ ${appointment.cost.toFixed(2)}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="text-muted text-center py-4">Nenhuma consulta agendada para hoje</p>';
            }
        }
    })
    .catch(error => {
        console.error('Error loading today schedule:', error);
    });
}

function loadRecentPatients() {
    fetch('/api/dentists/profile/patients?limit=5', {
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = document.getElementById('recentPatients');
            
            if (data.data.data && data.data.data.length > 0) {
                let html = '';
                data.data.data.forEach(patient => {
                    html += `
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <h6 class="mb-1">${patient.user.name}</h6>
                                <small class="text-muted">${patient.patient_code}</small>
                            </div>
                            <a href="/dentist/patients" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="text-muted text-center py-2">Nenhum paciente recente</p>';
            }
        }
    })
    .catch(error => {
        console.error('Error loading recent patients:', error);
    });
}

function loadCharts() {
    loadAppointmentsStatusChart();
    loadMonthlyRevenueChart();
}

function loadAppointmentsStatusChart() {
    fetch('/api/dentists/profile/statistics', {
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const ctx = document.getElementById('appointmentsStatusChart').getContext('2d');
            
            if (appointmentsStatusChart) {
                appointmentsStatusChart.destroy();
            }
            
            appointmentsStatusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Concluídas', 'Agendadas', 'Canceladas'],
                    datasets: [{
                        data: [
                            data.data.completed_appointments || 0,
                            data.data.upcoming_appointments || 0,
                            data.data.cancelled_appointments || 0
                        ],
                        backgroundColor: [
                            '#28a745',
                            '#007bff',
                            '#dc3545'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    })
    .catch(error => {
        console.error('Error loading appointments status chart:', error);
    });
}

function loadMonthlyRevenueChart() {
    // This would typically come from an API endpoint
    // For now, we'll create sample data
    const ctx = document.getElementById('monthlyRevenueChart').getContext('2d');
    
    if (monthlyRevenueChart) {
        monthlyRevenueChart.destroy();
    }
    
    monthlyRevenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Receita (R$)',
                data: [12000, 15000, 18000, 14000, 16000, 20000],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
}

function getStatusColor(status) {
    const colors = {
        'scheduled': 'warning',
        'confirmed': 'info',
        'in_progress': 'primary',
        'completed': 'success',
        'cancelled': 'danger',
        'no_show': 'secondary'
    };
    return colors[status] || 'secondary';
}

function getStatusText(status) {
    const texts = {
        'scheduled': 'Agendado',
        'confirmed': 'Confirmado',
        'in_progress': 'Em Andamento',
        'completed': 'Concluído',
        'cancelled': 'Cancelado',
        'no_show': 'Não Compareceu'
    };
    return texts[status] || status;
}

function getToken() {
    return localStorage.getItem('token') || '';
}
</script>
