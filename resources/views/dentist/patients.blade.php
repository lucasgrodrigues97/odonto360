@extends('layouts.app')

@section('title', 'Meus Pacientes')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Meus Pacientes</h1>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newPatientModal">
                <i class="fas fa-plus me-1"></i>Novo Paciente
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" placeholder="Nome, email ou telefone">
                </div>
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">Todos</option>
                        <option value="active">Ativo</option>
                        <option value="inactive">Inativo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="lastAppointment" class="form-label">Última Consulta</label>
                    <select class="form-select" id="lastAppointment">
                        <option value="">Todas</option>
                        <option value="last_week">Última semana</option>
                        <option value="last_month">Último mês</option>
                        <option value="last_3_months">Últimos 3 meses</option>
                        <option value="over_3_months">Mais de 3 meses</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-primary me-2" onclick="filterPatients()">
                        <i class="fas fa-search me-1"></i>Filtrar
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                        <i class="fas fa-times me-1"></i>Limpar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Patients Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Pacientes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="patientsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Data Nascimento</th>
                            <th>Última Consulta</th>
                            <th>Status</th>
                            <th>Ações</th>
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

<!-- New Patient Modal -->
<div class="modal fade" id="newPatientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newPatientForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="patientName" class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control" id="patientName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="patientEmail" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="patientEmail" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="patientPhone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control" id="patientPhone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="patientBirthDate" class="form-label">Data de Nascimento</label>
                                <input type="date" class="form-control" id="patientBirthDate">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="patientCpf" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="patientCpf" placeholder="000.000.000-00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="patientGender" class="form-label">Sexo</label>
                                <select class="form-select" id="patientGender">
                                    <option value="">Selecione</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Feminino</option>
                                    <option value="O">Outro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="patientAddress" class="form-label">Endereço</label>
                        <textarea class="form-control" id="patientAddress" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="patientNotes" class="form-label">Observações</label>
                        <textarea class="form-control" id="patientNotes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Paciente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Patient Details Modal -->
<div class="modal fade" id="patientDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="patientDetailsContent">
                <!-- Patient details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="editPatient()">Editar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    loadPatients();
    
    // Event handlers
    $('#newPatientForm').on('submit', function(e) {
        e.preventDefault();
        savePatient();
    });
    
    $('#patientCpf').on('input', function() {
        let value = this.value.replace(/\D/g, '');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        this.value = value;
    });
});

function loadPatients() {
    $.ajax({
        url: '/api/dentist/patients',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayPatients(response.data);
            }
        },
        error: function() {
            displayPatientsError();
        }
    });
}

function displayPatients(patients) {
    const tbody = $('#patientsTable tbody');
    tbody.empty();
    
    if (patients.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Nenhum paciente encontrado</p>
                </td>
            </tr>
        `);
        return;
    }
    
    patients.forEach(function(patient) {
        const lastAppointment = patient.last_appointment ? 
            new Date(patient.last_appointment).toLocaleDateString('pt-BR') : 'Nunca';
        
        const statusClass = patient.status === 'active' ? 'bg-success' : 'bg-secondary';
        const statusText = patient.status === 'active' ? 'Ativo' : 'Inativo';
        
        tbody.append(`
            <tr>
                <td>${patient.name}</td>
                <td>${patient.email}</td>
                <td>${patient.phone || '-'}</td>
                <td>${patient.birth_date ? new Date(patient.birth_date).toLocaleDateString('pt-BR') : '-'}</td>
                <td>${lastAppointment}</td>
                <td><span class="badge ${statusClass}">${statusText}</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewPatient(${patient.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="editPatient(${patient.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="scheduleAppointment(${patient.id})">
                        <i class="fas fa-calendar-plus"></i>
                    </button>
                </td>
            </tr>
        `);
    });
}

function displayPatientsError() {
    const tbody = $('#patientsTable tbody');
    tbody.html(`
        <tr>
            <td colspan="7" class="text-center py-4">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <p class="text-muted">Erro ao carregar pacientes</p>
                <button class="btn btn-sm btn-outline-primary" onclick="loadPatients()">
                    <i class="fas fa-refresh me-1"></i>Tentar novamente
                </button>
            </td>
        </tr>
    `);
}

function filterPatients() {
    const search = $('#search').val();
    const status = $('#statusFilter').val();
    const lastAppointment = $('#lastAppointment').val();
    
    $.ajax({
        url: '/api/dentist/patients',
        method: 'GET',
        data: {
            search: search,
            status: status,
            last_appointment: lastAppointment
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayPatients(response.data);
            }
        },
        error: function() {
            displayPatientsError();
        }
    });
}

function clearFilters() {
    $('#filterForm')[0].reset();
    loadPatients();
}

function savePatient() {
    const formData = {
        name: $('#patientName').val(),
        email: $('#patientEmail').val(),
        phone: $('#patientPhone').val(),
        birth_date: $('#patientBirthDate').val(),
        cpf: $('#patientCpf').val(),
        gender: $('#patientGender').val(),
        address: $('#patientAddress').val(),
        notes: $('#patientNotes').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };
    
    $.ajax({
        url: '/api/dentist/patients',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#newPatientModal').modal('hide');
                loadPatients();
                showToast('Paciente criado com sucesso!', 'success');
            } else {
                showToast('Erro ao criar paciente: ' + response.message, 'error');
            }
        },
        error: function() {
            showToast('Erro ao criar paciente', 'error');
        }
    });
}

function viewPatient(patientId) {
    $.ajax({
        url: `/api/dentist/patients/${patientId}`,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayPatientDetails(response.data);
                $('#patientDetailsModal').modal('show');
            }
        },
        error: function() {
            showToast('Erro ao carregar detalhes do paciente', 'error');
        }
    });
}

function displayPatientDetails(patient) {
    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6>Informações Pessoais</h6>
                <p><strong>Nome:</strong> ${patient.name}</p>
                <p><strong>Email:</strong> ${patient.email}</p>
                <p><strong>Telefone:</strong> ${patient.phone || '-'}</p>
                <p><strong>CPF:</strong> ${patient.cpf || '-'}</p>
                <p><strong>Data de Nascimento:</strong> ${patient.birth_date ? new Date(patient.birth_date).toLocaleDateString('pt-BR') : '-'}</p>
                <p><strong>Sexo:</strong> ${patient.gender || '-'}</p>
            </div>
            <div class="col-md-6">
                <h6>Endereço</h6>
                <p>${patient.address || 'Não informado'}</p>
                
                <h6 class="mt-3">Observações</h6>
                <p>${patient.notes || 'Nenhuma observação'}</p>
            </div>
        </div>
    `;
    
    $('#patientDetailsContent').html(content);
}

function editPatient(patientId) {
    // Implementar edição de paciente
    showToast('Funcionalidade de edição em desenvolvimento', 'info');
}

function scheduleAppointment(patientId) {
    // Redirecionar para agendamento
    window.location.href = `/dentist/appointments?patient_id=${patientId}`;
}

function showToast(message, type) {
    // Implementar toast notification
    alert(message);
}
</script>
@endsection
