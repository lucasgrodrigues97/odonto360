<!-- Admin Dashboard -->
<div class="row g-4 mb-4">
    <!-- Quick Stats -->
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total de Pacientes</h6>
                        <h3 class="mb-0" id="totalPatients">-</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x opacity-75"></i>
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
                        <h6 class="card-title">Dentistas Ativos</h6>
                        <h3 class="mb-0" id="activeDentists">-</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-md fa-2x opacity-75"></i>
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
</div>

<div class="row g-4">
    <!-- Recent Appointments -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Agendamentos Recentes
                </h5>
                <a href="{{ route('admin.appointments') }}" class="btn btn-sm btn-outline-primary">
                    Ver Todos
                </a>
            </div>
            <div class="card-body">
                <div id="recentAppointments">
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
                    <a href="{{ route('admin.patients') }}" class="btn btn-primary">
                        <i class="fas fa-users me-2"></i>
                        Gerenciar Pacientes
                    </a>
                    <a href="{{ route('admin.dentists') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-md me-2"></i>
                        Gerenciar Dentistas
                    </a>
                    <a href="{{ route('admin.appointments') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Ver Agendamentos
                    </a>
                    <a href="{{ route('admin.procedures') }}" class="btn btn-outline-info">
                        <i class="fas fa-procedures me-2"></i>
                        Gerenciar Procedimentos
                    </a>
                    <a href="{{ route('admin.reports') }}" class="btn btn-outline-success">
                        <i class="fas fa-chart-bar me-2"></i>
                        Relatórios
                    </a>
                </div>
            </div>
        </div>
        
        <!-- System Status -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-server me-2"></i>
                    Status do Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>API Status</span>
                    <span class="badge bg-success" id="apiStatus">Online</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Banco de Dados</span>
                    <span class="badge bg-success" id="dbStatus">Conectado</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Cache</span>
                    <span class="badge bg-success" id="cacheStatus">Ativo</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Última Atualização</span>
                    <small class="text-muted" id="lastUpdate">-</small>
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
                    <i class="fas fa-chart-bar me-2"></i>
                    Consultas por Dentista
                </h5>
            </div>
            <div class="card-body">
                <canvas id="appointmentsByDentistChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Chart -->
<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Receita Mensal
                </h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyRevenueChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
let appointmentsStatusChart = null;
let appointmentsByDentistChart = null;
let monthlyRevenueChart = null;

function loadDashboardData() {
    loadAdminStatistics();
    loadRecentAppointments();
    loadCharts();
    updateSystemStatus();
}

function loadAdminStatistics() {
    // Load total patients
    fetch('/api/patients?limit=1', {
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('totalPatients').textContent = data.data.total || 0;
        }
    })
    .catch(error => {
        console.error('Error loading patients count:', error);
    });

    // Load active dentists
    fetch('/api/dentists?is_active=1&limit=1', {
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('activeDentists').textContent = data.data.total || 0;
        }
    })
    .catch(error => {
        console.error('Error loading dentists count:', error);
    });

    // Load today's appointments
    const today = new Date().toISOString().split('T')[0];
    fetch(`/api/appointments?start_date=${today}&end_date=${today}&limit=1`, {
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

    // Load total revenue (this would need a specific endpoint)
    document.getElementById('totalRevenue').textContent = 'R$ 0,00';
}

function loadRecentAppointments() {
    fetch('/api/appointments?limit=10', {
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = document.getElementById('recentAppointments');
            
            if (data.data.data && data.data.data.length > 0) {
                let html = '';
                data.data.data.forEach(appointment => {
                    const appointmentDate = new Date(appointment.appointment_date);
                    const appointmentTime = new Date(appointment.appointment_time);
                    
                    html += `
                        <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-${getStatusColor(appointment.status)} bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-calendar text-${getStatusColor(appointment.status)}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">${appointment.patient.user.name}</h6>
                                    <small class="text-muted">
                                        ${appointment.dentist.user.name} - ${appointmentDate.toLocaleDateString('pt-BR')} às ${appointmentTime.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'})}
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
                container.innerHTML = '<p class="text-muted text-center py-4">Nenhum agendamento encontrado</p>';
            }
        }
    })
    .catch(error => {
        console.error('Error loading recent appointments:', error);
    });
}

function loadCharts() {
    loadAppointmentsStatusChart();
    loadAppointmentsByDentistChart();
    loadMonthlyRevenueChart();
}

function loadAppointmentsStatusChart() {
    // This would typically come from an API endpoint
    const ctx = document.getElementById('appointmentsStatusChart').getContext('2d');
    
    if (appointmentsStatusChart) {
        appointmentsStatusChart.destroy();
    }
    
    appointmentsStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Concluídas', 'Agendadas', 'Canceladas', 'Em Andamento'],
            datasets: [{
                data: [45, 25, 15, 10],
                backgroundColor: [
                    '#28a745',
                    '#007bff',
                    '#dc3545',
                    '#ffc107'
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

function loadAppointmentsByDentistChart() {
    // This would typically come from an API endpoint
    const ctx = document.getElementById('appointmentsByDentistChart').getContext('2d');
    
    if (appointmentsByDentistChart) {
        appointmentsByDentistChart.destroy();
    }
    
    appointmentsByDentistChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Dr. João Silva', 'Dra. Maria Santos', 'Dr. Pedro Costa'],
            datasets: [{
                label: 'Consultas',
                data: [25, 30, 20],
                backgroundColor: [
                    'rgba(0, 123, 255, 0.8)',
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function loadMonthlyRevenueChart() {
    const ctx = document.getElementById('monthlyRevenueChart').getContext('2d');
    
    if (monthlyRevenueChart) {
        monthlyRevenueChart.destroy();
    }
    
    monthlyRevenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            datasets: [{
                label: 'Receita (R$)',
                data: [25000, 30000, 35000, 28000, 32000, 40000, 38000, 42000, 36000, 39000, 45000, 50000],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
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

function updateSystemStatus() {
    // Check API status
    fetch('/api/health', {
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            document.getElementById('apiStatus').className = 'badge bg-success';
            document.getElementById('apiStatus').textContent = 'Online';
        } else {
            document.getElementById('apiStatus').className = 'badge bg-danger';
            document.getElementById('apiStatus').textContent = 'Offline';
        }
    })
    .catch(error => {
        document.getElementById('apiStatus').className = 'badge bg-danger';
        document.getElementById('apiStatus').textContent = 'Offline';
    });

    // Update last update time
    document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString('pt-BR');
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
