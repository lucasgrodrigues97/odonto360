@extends('layouts.app')

@section('title', 'Odonto360 - Sistema de Agendamento Odontológico')

@section('content')
<div class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    <i class="fas fa-tooth me-3"></i>
                    Odonto360
                </h1>
                <p class="lead mb-4">
                    Sistema completo de agendamento odontológico com inteligência artificial
                    para otimizar sua experiência e a dos seus pacientes.
                </p>
                <div class="d-flex gap-3">
                    <button class="btn btn-light btn-lg" onclick="showLoginModal()">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Entrar
                    </button>
                    <button class="btn btn-outline-light btn-lg" onclick="scrollToFeatures()">
                        <i class="fas fa-info-circle me-2"></i>
                        Saiba Mais
                    </button>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <i class="fas fa-tooth display-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<section id="features" class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="display-5 fw-bold mb-3">Funcionalidades Principais</h2>
                <p class="lead text-muted">
                    Tudo que você precisa para gerenciar sua clínica odontológica
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-calendar-alt text-primary fs-2"></i>
                        </div>
                        <h5 class="card-title">Agendamento Inteligente</h5>
                        <p class="card-text text-muted">
                            Sistema de agendamento com sugestões de IA para otimizar horários
                            e melhorar a experiência dos pacientes.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-users text-success fs-2"></i>
                        </div>
                        <h5 class="card-title">Gestão de Pacientes</h5>
                        <p class="card-text text-muted">
                            Controle completo do histórico médico, tratamentos e
                            informações dos seus pacientes.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-chart-line text-info fs-2"></i>
                        </div>
                        <h5 class="card-title">Relatórios e Analytics</h5>
                        <p class="card-text text-muted">
                            Dashboards completos com métricas de produtividade,
                            financeiro e satisfação dos pacientes.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-mobile-alt text-warning fs-2"></i>
                        </div>
                        <h5 class="card-title">Responsivo</h5>
                        <p class="card-text text-muted">
                            Interface adaptável para desktop, tablet e smartphone,
                            garantindo acesso em qualquer dispositivo.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-shield-alt text-danger fs-2"></i>
                        </div>
                        <h5 class="card-title">Segurança</h5>
                        <p class="card-text text-muted">
                            Autenticação OAuth, criptografia de dados e conformidade
                            com LGPD para máxima segurança.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-robot text-secondary fs-2"></i>
                        </div>
                        <h5 class="card-title">Inteligência Artificial</h5>
                        <p class="card-text text-muted">
                            Sugestões automáticas de horários, análise de padrões
                            e otimização de recursos com IA.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Technology Stack -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="display-5 fw-bold mb-3">Tecnologias Utilizadas</h2>
                <p class="lead text-muted">
                    Stack moderno e robusto para máxima performance e segurança
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    <div class="bg-white rounded shadow-sm p-4 mb-3">
                        <i class="fab fa-laravel text-danger fs-1"></i>
                        <h6 class="mt-2">Laravel 10</h6>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    <div class="bg-white rounded shadow-sm p-4 mb-3">
                        <i class="fas fa-database text-primary fs-1"></i>
                        <h6 class="mt-2">MySQL</h6>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    <div class="bg-white rounded shadow-sm p-4 mb-3">
                        <i class="fab fa-js-square text-warning fs-1"></i>
                        <h6 class="mt-2">jQuery</h6>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    <div class="bg-white rounded shadow-sm p-4 mb-3">
                        <i class="fab fa-aws text-warning fs-1"></i>
                        <h6 class="mt-2">AWS</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-3">Pronto para começar?</h2>
        <p class="lead mb-4">
            Transforme sua clínica odontológica com tecnologia de ponta
        </p>
        <button class="btn btn-light btn-lg" onclick="showLoginModal()">
            <i class="fas fa-rocket me-2"></i>
            Começar Agora
        </button>
    </div>
</section>
@endsection

@section('scripts')
<script>
function scrollToFeatures() {
    document.getElementById('features').scrollIntoView({ behavior: 'smooth' });
}

function showLoginModal() {
    const modal = new bootstrap.Modal(document.getElementById('loginModal'));
    modal.show();
}

function login() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (!email || !password) {
        showToast('Por favor, preencha todos os campos', 'error');
        return;
    }
    
    // Show loading
    showLoading(true);
    
    // Make API call
    fetch('/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ email, password })
    })
    .then(response => response.json())
    .then(data => {
        showLoading(false);
        if (data.success) {
            // Store token
            localStorage.setItem('token', data.data.token);
            // Redirect to dashboard
            window.location.href = '/dashboard';
        } else {
            showToast(data.message || 'Erro ao fazer login', 'error');
        }
    })
    .catch(error => {
        showLoading(false);
        showToast('Erro de conexão', 'error');
        console.error('Error:', error);
    });
}

function loginWithGoogle() {
    window.location.href = '/api/auth/google';
}

function showLoading(show) {
    const spinner = document.getElementById('loadingSpinner');
    if (show) {
        spinner.classList.remove('d-none');
    } else {
        spinner.classList.add('d-none');
    }
}

function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    
    toastMessage.textContent = message;
    
    // Change toast color based on type
    const toastHeader = toast.querySelector('.toast-header');
    const icon = toastHeader.querySelector('i');
    
    toastHeader.className = 'toast-header';
    icon.className = 'fas me-2';
    
    if (type === 'error') {
        toastHeader.classList.add('text-danger');
        icon.classList.add('fa-exclamation-circle', 'text-danger');
    } else if (type === 'success') {
        toastHeader.classList.add('text-success');
        icon.classList.add('fa-check-circle', 'text-success');
    } else {
        toastHeader.classList.add('text-primary');
        icon.classList.add('fa-info-circle', 'text-primary');
    }
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}
</script>
@endsection
