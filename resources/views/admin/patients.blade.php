@extends('layouts.app')

@section('title', 'Gerenciar Pacientes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users me-2"></i>Gerenciar Pacientes</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newPatientModal">
                    <i class="fas fa-plus me-2"></i>Novo Paciente
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nome, email ou CPF...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="statusFilter">
                                <option value="">Todos os status</option>
                                <option value="active">Ativo</option>
                                <option value="inactive">Inativo</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="dateFilter" placeholder="Filtrar por data">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary w-100" onclick="applyFilters()">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                <i class="fas fa-times me-1"></i>Limpar
                            </button>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-success w-100" onclick="exportPatients()">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Pacientes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="patientsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>CPF</th>
                                    <th>Telefone</th>
                                    <th>Data de Nascimento</th>
                                    <th>Status</th>
                                    <th>Última Consulta</th>
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

<!-- Modal Novo Paciente -->
<div class="modal fade" id="newPatientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="patientForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cpf" class="form-label">CPF *</label>
                            <input type="text" class="form-control" id="cpf" name="cpf" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="birth_date" class="form-label">Data de Nascimento *</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gênero</label>
                            <select class="form-control" id="gender" name="gender">
                                <option value="">Selecione...</option>
                                <option value="male">Masculino</option>
                                <option value="female">Feminino</option>
                                <option value="other">Outro</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">Endereço</label>
                            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="savePatient()">Salvar</button>
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
    $('#patientsTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        },
        ajax: {
            url: '/admin/patients/data',
            type: 'GET'
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'cpf' },
            { data: 'phone' },
            { data: 'birth_date' },
            { data: 'status' },
            { data: 'last_appointment' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
    
    // Listener para remover backdrop quando o modal for fechado
    $('#newPatientModal').on('hidden.bs.modal', function () {
        // Remover backdrop se existir
        $('.modal-backdrop').remove();
        // Remover classes e estilos do body
        $('body').removeClass('modal-open');
        $('body').css({
            'overflow': '',
            'padding-right': ''
        });
    });
});

function applyFilters() {
    // Implementar filtros
}

function clearFilters() {
    // Implementar limpeza de filtros
}

function exportPatients() {
    // Implementar exportação
}

function savePatient() {
    // Validar formulário
    const form = document.getElementById('patientForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Coletar dados do formulário
    const formData = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        cpf: document.getElementById('cpf').value,
        phone: document.getElementById('phone').value,
        birth_date: document.getElementById('birth_date').value,
        gender: document.getElementById('gender').value,
        address: document.getElementById('address').value,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    // Fazer requisição AJAX
    $.ajax({
        url: '/admin/patients',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Fechar modal usando jQuery/Bootstrap
                $('#newPatientModal').modal('hide');
                
                // Limpar formulário
                form.reset();
                
                // Recarregar tabela
                if ($.fn.DataTable.isDataTable('#patientsTable')) {
                    $('#patientsTable').DataTable().ajax.reload();
                }
                
                // Mostrar mensagem de sucesso
                if (typeof showToast === 'function') {
                    showToast(response.message || 'Paciente criado com sucesso!', 'success');
                } else {
                    alert(response.message || 'Paciente criado com sucesso!');
                }
            } else {
                // Mostrar mensagem de erro
                if (typeof showToast === 'function') {
                    showToast(response.message || 'Erro ao criar paciente', 'error');
                } else {
                    alert(response.message || 'Erro ao criar paciente');
                }
            }
        },
        error: function(xhr) {
            let errorMessage = 'Erro ao criar paciente';
            
            if (xhr.responseJSON) {
                // Priorizar mensagem descritiva do servidor
                if (xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON.errors) {
                    // Se não houver mensagem, construir a partir dos erros
                    const errors = xhr.responseJSON.errors;
                    const errorArray = [];
                    
                    // Coletar todas as mensagens de erro
                    Object.keys(errors).forEach(function(field) {
                        const fieldErrors = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
                        fieldErrors.forEach(function(err) {
                            errorArray.push(err);
                        });
                    });
                    
                    errorMessage = errorArray.length > 0 
                        ? errorArray.join(' ') 
                        : 'Por favor, verifique os dados informados.';
                }
            } else if (xhr.status === 0) {
                errorMessage = 'Erro de conexão. Verifique sua internet.';
            } else if (xhr.status >= 500) {
                errorMessage = 'Erro no servidor. Tente novamente mais tarde.';
            }
            
            if (typeof showToast === 'function') {
                showToast(errorMessage, 'error');
            } else {
                alert(errorMessage);
            }
        }
    });
}
</script>
@endsection
