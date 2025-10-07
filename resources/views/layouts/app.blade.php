<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Odonto360 - Sistema de Agendamento Odontológico')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Dropdown Fix CSS -->
    <style>
    .navbar .dropdown-menu {
        display: none !important;
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        z-index: 1000 !important;
        min-width: 200px !important;
        background: white !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        padding: 0.5rem 0 !important;
        margin: 0 !important;
    }
    
    .navbar .dropdown-menu.show {
        display: block !important;
    }
    
    .navbar .dropdown {
        position: relative !important;
    }
    
    .navbar .dropdown-item {
        display: block !important;
        width: 100% !important;
        padding: 0.25rem 1rem !important;
        clear: both !important;
        font-weight: 400 !important;
        color: #212529 !important;
        text-align: inherit !important;
        text-decoration: none !important;
        white-space: nowrap !important;
        background-color: transparent !important;
        border: 0 !important;
    }
    
    .navbar .dropdown-item:hover {
        color: #1e2125 !important;
        background-color: #e9ecef !important;
    }
    
    .navbar .dropdown-divider {
        height: 0 !important;
        margin: 0.5rem 0 !important;
        overflow: hidden !important;
        border-top: 1px solid #dee2e6 !important;
    }
    </style>
    
    @yield('styles')
</head>
<body>
    <div id="app">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    <i class="fas fa-tooth me-2"></i>
                    Odonto360
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="fas fa-home me-1"></i>
                                Dashboard
                            </a>
                        </li>
                        
                        @auth
                            @if(auth()->user()->isPatient())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('patient.appointments') }}">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        Meus Agendamentos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('patient.schedule') }}">
                                        <i class="fas fa-plus-circle me-1"></i>
                                        Agendar Consulta
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('patient.medical-history') }}">
                                        <i class="fas fa-file-medical me-1"></i>
                                        Histórico Médico
                                    </a>
                                </li>
                            @elseif(auth()->user()->isDentist())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('dentist.appointments') }}">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        Agendamentos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('dentist.patients') }}">
                                        <i class="fas fa-users me-1"></i>
                                        Pacientes
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('dentist.schedule') }}">
                                        <i class="fas fa-clock me-1"></i>
                                        Minha Agenda
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('dentist.statistics') }}">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        Estatísticas
                                    </a>
                                </li>
                            @elseif(auth()->user()->email === 'admin@odonto360.com' || (auth()->user()->roles && auth()->user()->roles->contains('name', 'admin')))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-chart-pie me-1"></i>
                                        Painel de Controle
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('dashboard') }}">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        Agendamentos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.patients') }}">
                                        <i class="fas fa-users me-1"></i>
                                        Pacientes
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dentists') }}">
                                        <i class="fas fa-user-md me-1"></i>
                                        Dentistas
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.reports') }}">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        Relatórios
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>
                    
                    <ul class="navbar-nav">
                        @auth
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="userDropdown">
                                    <i class="fas fa-user me-1"></i>
                                    {{ auth()->user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile') }}">
                                        <i class="fas fa-user me-2"></i>Meu Perfil
                                    </a></li>
                                    <li><a class="dropdown-item" href="#">
                                        <i class="fas fa-cog me-2"></i>Configurações
                                    </a></li>
                                    <li><a class="dropdown-item" href="#">
                                        <i class="fas fa-bell me-2"></i>Notificações
                                    </a></li>
                                    @if(auth()->user()->email === 'admin@odonto360.com' || (auth()->user()->roles && auth()->user()->roles->contains('name', 'admin')))
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.patients') }}">
                                        <i class="fas fa-users me-2"></i>Gerenciar Pacientes
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.dentists') }}">
                                        <i class="fas fa-user-md me-2"></i>Gerenciar Dentistas
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.procedures') }}">
                                        <i class="fas fa-procedures me-2"></i>Gerenciar Procedimentos
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.reports') }}">
                                        <i class="fas fa-chart-bar me-2"></i>Relatórios
                                    </a></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="logout()">
                                            <i class="fas fa-sign-out-alt me-2"></i>Sair
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="#" onclick="showLoginModal()">
                                    <i class="fas fa-sign-in-alt me-1"></i>
                                    Entrar
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="container-fluid py-4">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-light text-center text-muted py-3 mt-5">
            <div class="container">
                <p>&copy; {{ date('Y') }} Odonto360. Todos os direitos reservados.</p>
            </div>
        </footer>
    </div>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Entrar no Sistema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="loginEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="loginPassword" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">
                                Lembrar de mim
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="login()">Entrar</button>
                    <button type="button" class="btn btn-outline-primary" onclick="loginWithGoogle()">
                        <i class="fab fa-google me-1"></i>
                        Entrar com Google
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="d-none">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toast" class="toast" role="alert">
            <div class="toast-header">
                <i class="fas fa-info-circle text-primary me-2"></i>
                <strong class="me-auto">Sistema</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                <!-- Toast message will be inserted here -->
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    
    <script>
    // Verificar se jQuery está carregado
    if (typeof jQuery === 'undefined') {
        console.error('jQuery não foi carregado corretamente!');
    } else {
        console.log('jQuery carregado com sucesso!');
    }
    
    // Inicializar dropdowns do Bootstrap
    $(document).ready(function() {
        console.log('Inicializando dropdowns...');
        console.log('Bootstrap disponível:', typeof bootstrap !== 'undefined');
        
        // Verificar se Bootstrap está carregado
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap não está carregado!');
            return;
        }
        
        // Inicializar todos os dropdowns
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        console.log('Elementos dropdown encontrados:', dropdownElementList.length);
        
        var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            console.log('Inicializando dropdown:', dropdownToggleEl.id || dropdownToggleEl.textContent);
            return new bootstrap.Dropdown(dropdownToggleEl);
        });
        
        console.log('Dropdowns inicializados:', dropdownList.length);
        
        // Toggle manual do dropdown do usuário
        $('#userDropdown').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Clique no dropdown do usuário detectado');
            
            var dropdownMenu = $(this).next('.dropdown-menu');
            if (dropdownMenu.length) {
                console.log('Dropdown menu encontrado:', dropdownMenu);
                console.log('Estado atual (show):', dropdownMenu.hasClass('show'));
                
                // Toggle simples
                if (dropdownMenu.hasClass('show')) {
                    dropdownMenu.removeClass('show');
                    console.log('Fechando dropdown');
                } else {
                    // Fechar outros dropdowns primeiro
                    $('.dropdown-menu').removeClass('show');
                    dropdownMenu.addClass('show');
                    console.log('Abrindo dropdown');
                }
            } else {
                console.error('Dropdown menu não encontrado!');
            }
        });
        
        // Fechar dropdown ao clicar fora
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').removeClass('show');
            }
        });
        
        // Debug: verificar se o dropdown está funcionando
        setTimeout(function() {
            var userDropdown = document.getElementById('userDropdown');
            if (userDropdown) {
                console.log('Dropdown do usuário encontrado:', userDropdown);
                console.log('Atributos data-bs-toggle:', userDropdown.getAttribute('data-bs-toggle'));
            }
        }, 1000);
    });
    </script>
    
    @yield('scripts')
    
    <script>
    
    // Função de logout
    function logout() {
        if (confirm('Tem certeza que deseja sair?')) {
            try {
                // Criar formulário para fazer POST para logout
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("logout") }}';
                form.style.display = 'none';
                
                // Adicionar CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Adicionar ao body e submeter
                document.body.appendChild(form);
                form.submit();
            } catch (error) {
                console.error('Erro no logout:', error);
                // Fallback: redirecionar diretamente
                window.location.href = '{{ route("logout.get") }}';
            }
        }
    }
    
    // Função para mostrar modal de login
    function showLoginModal() {
        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
        
        // Aguardar um pouco para garantir que o modal esteja completamente carregado
        setTimeout(function() {
            const emailElement = document.getElementById('loginEmail');
            const passwordElement = document.getElementById('loginPassword');
            
            if (emailElement && passwordElement) {
                // Focar no campo de email
                emailElement.focus();
                
                // Adicionar evento de teclado para login com Enter
                passwordElement.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        login();
                    }
                });
            } else {
                console.error('Erro: Campos de login não encontrados no modal');
            }
        }, 300);
    }
    
    // Função para fazer login
    function login() {
        // Verificar se os elementos existem
        const emailElement = document.getElementById('loginEmail');
        const passwordElement = document.getElementById('loginPassword');
        
        if (!emailElement || !passwordElement) {
            console.error('Elementos de login não encontrados');
            alert('Erro: Campos de login não encontrados. Recarregue a página.');
            return;
        }
        
        const email = emailElement.value.trim();
        const password = passwordElement.value.trim();
        
        if (!email || !password) {
            alert('Por favor, preencha todos os campos.');
            return;
        }
        
        // Desabilitar botão para evitar múltiplos cliques
        const loginButton = document.querySelector('button[onclick="login()"]');
        if (loginButton) {
            loginButton.disabled = true;
            loginButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Entrando...';
        }
        
        // Criar formulário para fazer POST para login
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("login.post") }}';
        form.style.display = 'none';
        
        // Adicionar CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Adicionar campos de email e senha
        const emailInput = document.createElement('input');
        emailInput.type = 'hidden';
        emailInput.name = 'email';
        emailInput.value = email;
        form.appendChild(emailInput);
        
        const passwordInput = document.createElement('input');
        passwordInput.type = 'hidden';
        passwordInput.name = 'password';
        passwordInput.value = password;
        form.appendChild(passwordInput);
        
        // Adicionar ao body e submeter
        document.body.appendChild(form);
        form.submit();
    }
    
    // Função para login com Google
    function loginWithGoogle() {
        window.location.href = '{{ route("auth.google") }}';
    }
    
    // Mostrar mensagens de sucesso/erro
    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showToast('{{ session('error') }}', 'error');
    @endif
    
    function showToast(message, type = 'info') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        const toastHeader = toast.querySelector('.toast-header i');
        
        toastMessage.textContent = message;
        
        // Alterar ícone e cor baseado no tipo
        if (type === 'success') {
            toastHeader.className = 'fas fa-check-circle text-success me-2';
        } else if (type === 'error') {
            toastHeader.className = 'fas fa-exclamation-circle text-danger me-2';
        } else {
            toastHeader.className = 'fas fa-info-circle text-primary me-2';
        }
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }
    </script>
</body>
</html>
