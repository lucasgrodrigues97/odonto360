<!-- Patient Dashboard -->
<div class="row g-4 mb-4">
    <!-- Quick Stats -->
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Próximas Consultas</h6>
                        <h3 class="mb-0" id="upcomingAppointments">-</h3>
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
                        <h6 class="card-title">Consultas Realizadas</h6>
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
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Gasto</h6>
                        <h3 class="mb-0" id="totalSpent">-</h3>
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
                        <h6 class="card-title">Última Consulta</h6>
                        <h6 class="mb-0" id="lastAppointment">-</h6>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Upcoming Appointments -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Próximas Consultas
                </h5>
                <a href="{{ route('patient.appointments') }}" class="btn btn-sm btn-outline-primary">
                    Ver Todas
                </a>
            </div>
            <div class="card-body">
                <div id="upcomingAppointmentsList">
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
                    <a href="{{ route('patient.schedule') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>
                        Agendar Consulta
                    </a>
                    <a href="{{ route('patient.medical-history') }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-medical me-2"></i>
                        Ver Histórico Médico
                    </a>
                    <a href="{{ route('profile') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-user-edit me-2"></i>
                        Editar Perfil
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Medical History -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-file-medical me-2"></i>
                    Histórico Recente
                </h5>
            </div>
            <div class="card-body">
                <div id="recentMedicalHistory">
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

<script>
function loadDashboardData() {
    loadPatientStatistics();
    loadUpcomingAppointments();
    loadRecentMedicalHistory();
}

function loadPatientStatistics() {
    fetch('/api/patients/profile/statistics', {
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('upcomingAppointments').textContent = data.data.upcoming_appointments || 0;
            document.getElementById('completedAppointments').textContent = data.data.completed_appointments || 0;
            document.getElementById('totalSpent').textContent = 'R$ ' + (data.data.total_spent || 0).toFixed(2);
            
            if (data.data.last_appointment) {
                const lastAppointment = new Date(data.data.last_appointment.appointment_date);
                document.getElementById('lastAppointment').textContent = lastAppointment.toLocaleDateString('pt-BR');
            } else {
                document.getElementById('lastAppointment').textContent = 'Nenhuma';
            }
        }
    })
    .catch(error => {
        console.error('Error loading statistics:', error);
    });
}

function loadUpcomingAppointments() {
    fetch('/api/patients/profile/appointments?limit=5', {
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = document.getElementById('upcomingAppointmentsList');
            
            if (data.data.data && data.data.data.length > 0) {
                let html = '';
                data.data.data.forEach(appointment => {
                    const appointmentDate = new Date(appointment.appointment_date);
                    const appointmentTime = new Date(appointment.appointment_time);
                    
                    html += `
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <h6 class="mb-1">${appointment.dentist.user.name}</h6>
                                <small class="text-muted">
                                    ${appointmentDate.toLocaleDateString('pt-BR')} às ${appointmentTime.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'})}
                                </small>
                            </div>
                            <span class="badge bg-${getStatusColor(appointment.status)}">${getStatusText(appointment.status)}</span>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="text-muted text-center py-3">Nenhuma consulta agendada</p>';
            }
        }
    })
    .catch(error => {
        console.error('Error loading appointments:', error);
    });
}

function loadRecentMedicalHistory() {
    fetch('/api/patients/profile/medical-history?limit=3', {
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = document.getElementById('recentMedicalHistory');
            
            if (data.data.data && data.data.data.length > 0) {
                let html = '';
                data.data.data.forEach(history => {
                    const historyDate = new Date(history.date);
                    
                    html += `
                        <div class="d-flex justify-content-between align-items-start border-bottom py-2">
                            <div>
                                <h6 class="mb-1">${history.dentist.user.name}</h6>
                                <p class="mb-1 small">${history.description}</p>
                                <small class="text-muted">${historyDate.toLocaleDateString('pt-BR')}</small>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="text-muted text-center py-2">Nenhum histórico recente</p>';
            }
        }
    })
    .catch(error => {
        console.error('Error loading medical history:', error);
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
