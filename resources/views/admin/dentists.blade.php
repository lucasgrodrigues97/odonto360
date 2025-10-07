@extends('layouts.app')

@section('title', 'Gerenciar Dentistas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-user-md me-2"></i>Gerenciar Dentistas</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newDentistModal">
                    <i class="fas fa-plus me-2"></i>Novo Dentista
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
                            <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nome, email ou CRM...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="specializationFilter">
                                <option value="">Todas as especializações</option>
                                <option value="ortodontia">Ortodontia</option>
                                <option value="implantodontia">Implantodontia</option>
                                <option value="endodontia">Endodontia</option>
                                <option value="periodontia">Periodontia</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="statusFilter">
                                <option value="">Todos os status</option>
                                <option value="active">Ativo</option>
                                <option value="inactive">Inativo</option>
                            </select>
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
                            <button class="btn btn-success w-100" onclick="exportDentists()">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Dentistas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="dentistsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>CRM</th>
                                    <th>Especialização</th>
                                    <th>Valor Consulta</th>
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

<!-- Modal Novo Dentista -->
<div class="modal fade" id="newDentistModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Dentista</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="dentistForm">
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
                            <label for="crm" class="form-label">CRM *</label>
                            <input type="text" class="form-control" id="crm" name="crm" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="specialization" class="form-label">Especialização *</label>
                            <select class="form-control" id="specialization" name="specialization" required>
                                <option value="">Selecione...</option>
                                <option value="ortodontia">Ortodontia</option>
                                <option value="implantodontia">Implantodontia</option>
                                <option value="endodontia">Endodontia</option>
                                <option value="periodontia">Periodontia</option>
                                <option value="cirurgia">Cirurgia</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="consultation_fee" class="form-label">Valor da Consulta *</label>
                            <input type="number" class="form-control" id="consultation_fee" name="consultation_fee" step="0.01" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="bio" class="form-label">Biografia</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveDentist()">Salvar</button>
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
    $('#dentistsTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        },
        ajax: {
            url: '/admin/dentists/data',
            type: 'GET'
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'crm' },
            { data: 'specialization' },
            { data: 'consultation_fee' },
            { data: 'status' },
            { data: 'last_appointment' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});

function applyFilters() {
    // Implementar filtros
}

function clearFilters() {
    // Implementar limpeza de filtros
}

function exportDentists() {
    // Implementar exportação
}

function saveDentist() {
    // Implementar salvamento
}
</script>
@endsection
