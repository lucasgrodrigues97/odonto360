# Odonto360 - Sistema de Agendamento Odontológico

## 📋 Sobre o Projeto

O Odonto360 é um sistema completo de agendamento para clínicas odontológicas, desenvolvido como projeto de TCC. O sistema permite o gerenciamento de pacientes, dentistas, procedimentos e agendamentos, com funcionalidades avançadas de IA para sugestão de horários e integração com OAuth para autenticação.

### Credenciais
- **Efetuar login como Admin** - email: admin@odonto360.com | senha: password
- **Efetuar login como Usuário Comum** - email: joao.silva@odonto360.com | senha: password

### Apresentação
- **Vídeo** - Estou deixando o vídeo de apresentação no próprio repositório e também disponibilizei em um site gratuito na seguinte URL: https://ik.imagekit.io/vegw8unfh/Apresenta%C3%A7%C3%A3o.mp4?tr=orig&updatedAt=1762729019882

## 🚀 Tecnologias Utilizadas

### Backend
- **Laravel 10** - Framework PHP
- **MySQL** - Banco de dados relacional
- **Laravel Sanctum** - Autenticação API
- **Laravel Socialite** - OAuth (Google)
- **OpenAI API** - Inteligência Artificial para sugestões

### Frontend
- **jQuery** - Biblioteca JavaScript
- **Bootstrap 5** - Framework CSS
- **Chart.js** - Gráficos e relatórios

### DevOps & Cloud
- **AWS** - Plataforma de nuvem
- **GitHub Actions** - CI/CD Pipeline
- **Terraform** - Infrastructure as Code
- **Docker** - Containerização

## 🏗️ Arquitetura do Sistema

### Entidades Principais
1. **Pacientes** - Dados pessoais e histórico
2. **Dentistas** - Profissionais da clínica
3. **Procedimentos** - Serviços oferecidos
4. **Agendamentos** - Consultas e procedimentos
5. **Especialidades** - Áreas de atuação dos dentistas

### Relacionamentos
- Dentista 1:N Especialidades (N:M)
- Dentista 1:N Agendamentos
- Paciente 1:N Agendamentos
- Procedimento 1:N Agendamentos

## 📱 Funcionalidades

### Para Pacientes
- Cadastro e perfil
- Agendamento online
- Histórico de consultas
- Notificações por WhatsApp/Email
- Avaliação de atendimento

### Para Dentistas
- Gestão de agenda
- Histórico de pacientes
- Relatórios de produtividade
- Configuração de horários disponíveis

### Para Administradores
- Gestão completa do sistema
- Relatórios gerenciais
- Configurações da clínica
- Dashboard com métricas

## 🤖 Inteligência Artificial

O sistema utiliza IA para:
- Sugestão automática de horários disponíveis
- Análise de padrões de agendamento
- Recomendação de procedimentos baseada no histórico
- Previsão de demanda por período

## 🔐 Autenticação e Segurança

- OAuth 2.0 com Google
- Autenticação JWT
- Controle de acesso baseado em roles
- Criptografia de dados sensíveis
- Logs de auditoria

## 🚀 Como Executar

### Opção 1: Docker (Recomendado)

Esta é a forma mais fácil e rápida de executar o projeto, sem precisar instalar dependências manualmente.

#### Pré-requisitos
- Docker Desktop
- Git

#### Instalação com Docker

1. Clone o repositório:
```bash
git clone https://github.com/lucasgrodrigues97/odonto360.git
cd odonto360
```

2. Inicie os containers:
```bash
docker-compose up -d
```

3. Instale as dependências (dentro do container):
```bash
docker-compose exec app composer install
docker-compose exec app npm install
```

4. Configure o ambiente:
```bash
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate
```

5. Execute as migrações e seeders:
```bash
docker-compose exec app php artisan migrate --seed
```

6. Compile os assets:
```bash
docker-compose exec app npm run build
```

7. Acesse a aplicação:
- **Aplicação principal:** http://localhost
- **phpMyAdmin:** http://localhost:8080
- **MailHog (emails):** http://localhost:8025

#### Comandos úteis do Docker

```bash
# Ver containers rodando
docker-compose ps

# Parar todos os containers
docker-compose down

# Ver logs da aplicação
docker-compose logs app

# Entrar no container da aplicação
docker-compose exec app bash

# Reconstruir containers
docker-compose up -d --build
```

### Opção 2: Instalação Manual

#### Pré-requisitos
- PHP 8.1+
- Composer
- MySQL 8.0+
- Node.js 16+
- Git

#### Instalação Manual

1. Clone o repositório:
```bash
git clone https://github.com/lucasgrodrigues97/odonto360.git
cd odonto360
```

2. Instale as dependências:
```bash
composer install
npm install
```

3. Configure o ambiente:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure o banco de dados no arquivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=odonto360
DB_USERNAME=root
DB_PASSWORD=
```

5. Execute as migrações:
```bash
php artisan migrate --seed
```

6. Compile os assets:
```bash
npm run build
```

7. Inicie o servidor:
```bash
php artisan serve
```

8. Acesse: http://localhost:8000

## ⚙️ Configurações Adicionais

### OAuth Google
Para usar o login com Google:

1. Acesse [Google Cloud Console](https://console.cloud.google.com/)
2. Crie um projeto e ative a Google+ API
3. Configure OAuth 2.0
4. Adicione as credenciais no `.env`:
```env
GOOGLE_CLIENT_ID=seu-client-id
GOOGLE_CLIENT_SECRET=seu-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback
```

### Inteligência Artificial (OpenAI)
Para usar as funcionalidades de IA:

1. Acesse [OpenAI Platform](https://platform.openai.com/)
2. Crie uma API key
3. Adicione no `.env`:
```env
OPENAI_API_KEY=sua-api-key
```

### Deploy na AWS

O projeto inclui configurações completas para deploy na AWS usando Terraform:

```bash
cd infrastructure
terraform init
terraform plan
terraform apply
```

## 🧪 Testes

Execute os testes automatizados:

```bash
# Testes unitários
php artisan test

# Testes de integração
php artisan test --testsuite=Feature

# Testes com cobertura
php artisan test --coverage
```

## 🚀 CI/CD Pipeline

O projeto possui um pipeline CI/CD completo configurado com GitHub Actions:

- ✅ **Testes automatizados** (PHPUnit + Laravel Dusk)
- ✅ **Análise de segurança** (Composer audit + CodeQL)
- ✅ **Testes de integração** (Docker)
- ✅ **Build de produção** (Artefatos otimizados)
- ✅ **Deploy automático** (AWS + Terraform)
- ✅ **Notificações** (Status detalhado)

**Documentação completa da pipeline**: [.github/PIPELINE.md](.github/PIPELINE.md)

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 👨‍💻 Autor

Desenvolvido por Lucas Rodrigues como projeto de TCC - Projeto Integrado
