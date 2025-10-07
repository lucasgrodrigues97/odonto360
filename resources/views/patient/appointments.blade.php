@extends('layouts.app')

@section('title', 'Meus Agendamentos')

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
                        <div class="col-md-6">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
                                <i class="fas fa-plus me-2"></i>Novo Agendamento
                            </button>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Buscar agendamentos...">
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
                                    <th>Data</th>
                                    <th>Horário</th>
                                    <th>Dentista</th>
                                    <th>Procedimento</th>
                                    <th>Status</th>
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
<div class="modal fade" id="newAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Agendamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="appointmentForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dentist_id" class="form-label">Dentista</label>
                            <select class="form-control" id="dentist_id" name="dentist_id" required>
                                <option value="">Selecione um dentista</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="appointment_date" class="form-label">Data</label>
                            <input type="date" class="form-control" id="appointment_date" name="appointment_date" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="appointment_time" class="form-label">Horário</label>
                            <select class="form-control" id="appointment_time" name="appointment_time" required>
                                <option value="">Selecione um horário</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="duration" class="form-label">Duração (minutos)</label>
                            <input type="number" class="form-control" id="duration" name="duration" value="60" min="30" max="240">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="procedures" class="form-label">Procedimentos</label>
                        <select class="form-control" id="procedures" name="procedures[]" multiple>
                            <option value="">Selecione os procedimentos</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Motivo da Consulta</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary" id="aiSuggestionBtn">
                            <i class="fas fa-robot me-2"></i>Sugestões de IA
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveAppointmentBtn">Agendar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#appointmentsTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        }
    });
    
    // Carregar dados dos agendamentos
    loadAppointments();
    
    // Carregar dentistas e procedimentos
    loadDentists();
    loadProcedures();
    
    // Eventos
    $('#dentist_id, #appointment_date').change(function() {
        loadAvailableSlots();
    });
    
    $('#saveAppointmentBtn').click(function() {
        saveAppointment();
    });
    
    $('#aiSuggestionBtn').click(function() {
        getAISuggestions();
    });
});

function loadAppointments() {
    // Implementar carregamento via AJAX
}

function loadDentists() {
    // Implementar carregamento via AJAX
}

function loadProcedures() {
    // Implementar carregamento via AJAX
}

function loadAvailableSlots() {
    // Implementar carregamento via AJAX
}

function saveAppointment() {
    // Implementar salvamento via AJAX
}

function getAISuggestions() {
    // Implementar sugestões de IA
}
</script>
@endsection
