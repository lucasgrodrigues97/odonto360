@extends('layouts.app')

@section('title', 'Agendamentos - Dentista')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-calendar-alt me-2"></i>Meus Agendamentos</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select class="form-control" id="statusFilter">
                                <option value="">Todos os status</option>
                                <option value="scheduled">Agendado</option>
                                <option value="confirmed">Confirmado</option>
                                <option value="completed">Concluído</option>
                                <option value="cancelled">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="dateFilter" placeholder="Filtrar por data">
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Buscar paciente...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped" id="appointmentsTable">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Paciente</th>
                                    <th>Procedimento</th>
                                    <th>Status</th>
                                    <th>Valor</th>
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

<!-- Modal Detalhes do Agendamento -->
<div class="modal fade" id="appointmentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Agendamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informações do Paciente</h6>
                        <p><strong>Nome:</strong> <span id="patientName"></span></p>
                        <p><strong>Email:</strong> <span id="patientEmail"></span></p>
                        <p><strong>Telefone:</strong> <span id="patientPhone"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Informações do Agendamento</h6>
                        <p><strong>Data:</strong> <span id="appointmentDate"></span></p>
                        <p><strong>Horário:</strong> <span id="appointmentTime"></span></p>
                        <p><strong>Duração:</strong> <span id="appointmentDuration"></span> min</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h6>Procedimentos</h6>
                        <ul id="proceduresList"></ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h6>Observações</h6>
                        <p id="appointmentNotes"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="confirmAppointmentBtn">
                    <i class="fas fa-check me-2"></i>Confirmar
                </button>
                <button type="button" class="btn btn-warning" id="completeAppointmentBtn">
                    <i class="fas fa-check-circle me-2"></i>Concluir
                </button>
                <button type="button" class="btn btn-danger" id="cancelAppointmentBtn">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Verificar se DataTable está disponível
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('DataTable não está carregado!');
        return;
    }
    
    // Inicializar DataTable
    $('#appointmentsTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        }
    });
    
    // Carregar dados dos agendamentos
    loadAppointments();
    
    // Eventos
    $('#statusFilter, #dateFilter').change(function() {
        filterAppointments();
    });
    
    $('#confirmAppointmentBtn').click(function() {
        updateAppointmentStatus('confirmed');
    });
    
    $('#completeAppointmentBtn').click(function() {
        updateAppointmentStatus('completed');
    });
    
    $('#cancelAppointmentBtn').click(function() {
        updateAppointmentStatus('cancelled');
    });
});

function loadAppointments() {
    // Implementar carregamento via AJAX
}

function filterAppointments() {
    // Implementar filtros
}

function updateAppointmentStatus(status) {
    // Implementar atualização de status
}
</script>
@endsection
