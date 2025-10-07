<!-- Admin Dashboard - Tela Antiga de Agendamentos -->
<div class="container-fluid">
    
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
    
    <!-- Tabela de Agendamentos -->
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
                        <a href="/admin/dashboard" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-chart-pie me-1"></i>Dashboard Completo
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="appointmentsTable">
                            <thead class="table-light">
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
