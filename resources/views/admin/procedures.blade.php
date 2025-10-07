@extends('layouts.app')

@section('title', 'Gerenciar Procedimentos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-procedures me-2"></i>Gerenciar Procedimentos</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newProcedureModal">
                    <i class="fas fa-plus me-2"></i>Novo Procedimento
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
                            <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nome...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="categoryFilter">
                                <option value="">Todas as categorias</option>
                                <option value="preventivo">Preventivo</option>
                                <option value="restaurador">Restaurador</option>
                                <option value="estetico">Estético</option>
                                <option value="cirurgico">Cirúrgico</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" id="priceMinFilter" placeholder="Preço mínimo">
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" id="priceMaxFilter" placeholder="Preço máximo">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary w-100" onclick="applyFilters()">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-success w-100" onclick="exportProcedures()">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Procedimentos -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="proceduresTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Categoria</th>
                                    <th>Duração (min)</th>
                                    <th>Preço</th>
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

<!-- Modal Novo Procedimento -->
<div class="modal fade" id="newProcedureModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Procedimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="procedureForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nome do Procedimento *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Categoria *</label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="">Selecione...</option>
                                <option value="preventivo">Preventivo</option>
                                <option value="restaurador">Restaurador</option>
                                <option value="estetico">Estético</option>
                                <option value="cirurgico">Cirúrgico</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="duration" class="form-label">Duração (minutos) *</label>
                            <input type="number" class="form-control" id="duration" name="duration" min="15" max="480" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Preço *</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="instructions" class="form-label">Instruções Pós-Procedimento</label>
                            <textarea class="form-control" id="instructions" name="instructions" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveProcedure()">Salvar</button>
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
    $('#proceduresTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        },
        ajax: {
            url: '/admin/procedures/data',
            type: 'GET'
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'description' },
            { data: 'category' },
            { data: 'duration' },
            { data: 'price' },
            { data: 'status' },
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

function exportProcedures() {
    // Implementar exportação
}

function saveProcedure() {
    // Implementar salvamento
}
</script>
@endsection
