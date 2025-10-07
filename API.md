# Odonto360 API Documentation

## 📋 Visão Geral

A API do Odonto360 é uma API RESTful desenvolvida em Laravel que fornece endpoints para gerenciamento de agendamentos odontológicos, pacientes, dentistas e procedimentos.

**Base URL:** `http://localhost:8000/api`

**Versão:** 1.0.0

## 🔐 Autenticação

A API utiliza autenticação baseada em tokens JWT através do Laravel Sanctum.

### Headers Obrigatórios

```http
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

## 📚 Endpoints

### Autenticação

#### POST /register
Registra um novo usuário no sistema.

**Body:**
```json
{
    "name": "João Silva",
    "email": "joao@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "(11) 99999-9999",
    "role": "patient"
}
```

**Resposta:**
```json
{
    "success": true,
    "message": "Usuário criado com sucesso",
    "data": {
        "user": {
            "id": 1,
            "name": "João Silva",
            "email": "joao@example.com"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

#### POST /login
Autentica um usuário existente.

**Body:**
```json
{
    "email": "joao@example.com",
    "password": "password123"
}
```

#### POST /logout
Desautentica o usuário atual.

**Headers:** `Authorization: Bearer {token}`

#### GET /me
Retorna os dados do usuário autenticado.

**Headers:** `Authorization: Bearer {token}`

### Agendamentos

#### GET /appointments
Lista agendamentos do usuário autenticado.

**Query Parameters:**
- `start_date` (opcional): Data inicial (YYYY-MM-DD)
- `end_date` (opcional): Data final (YYYY-MM-DD)
- `status` (opcional): Status do agendamento
- `dentist_id` (opcional): ID do dentista

#### GET /appointments/available-slots
Retorna horários disponíveis para um dentista em uma data específica.

**Query Parameters:**
- `dentist_id` (obrigatório): ID do dentista
- `date` (obrigatório): Data (YYYY-MM-DD)
- `duration` (opcional): Duração em minutos (padrão: 60)

#### GET /appointments/ai-suggestions
Retorna sugestões de horários usando IA.

**Query Parameters:**
- `dentist_id` (obrigatório): ID do dentista
- `preferred_dates` (obrigatório): Array de datas preferidas
- `duration` (opcional): Duração em minutos

#### POST /appointments
Cria um novo agendamento.

**Body:**
```json
{
    "dentist_id": 1,
    "appointment_date": "2024-01-15",
    "appointment_time": "10:00",
    "duration": 60,
    "notes": "Consulta de rotina",
    "procedures": [
        {
            "id": 1,
            "quantity": 1,
            "notes": "Limpeza dental"
        }
    ]
}
```

#### PUT /appointments/{id}/status
Atualiza o status de um agendamento.

**Body:**
```json
{
    "status": "confirmed",
    "notes": "Consulta confirmada"
}
```

#### POST /appointments/{id}/cancel
Cancela um agendamento.

**Body:**
```json
{
    "cancellation_reason": "Mudança de planos"
}
```

### Pacientes

#### GET /patients
Lista todos os pacientes (apenas admin).

#### GET /patients/profile
Retorna o perfil do paciente autenticado.

#### PUT /patients/profile
Atualiza o perfil do paciente.

**Body:**
```json
{
    "emergency_contact_name": "Maria Silva",
    "emergency_contact_phone": "(11) 88888-8888",
    "medical_conditions": ["Hipertensão", "Diabetes"],
    "allergies": ["Penicilina"],
    "medications": ["Losartana 50mg"],
    "insurance_provider": "Unimed",
    "insurance_number": "123456789"
}
```

#### GET /patients/profile/medical-history
Retorna o histórico médico do paciente.

#### GET /patients/profile/appointments
Retorna os agendamentos do paciente.

#### GET /patients/profile/statistics
Retorna estatísticas do paciente.

### Dentistas

#### GET /dentists
Lista todos os dentistas.

#### GET /dentists/{id}
Retorna dados de um dentista específico.

#### GET /dentists/profile/appointments
Retorna agendamentos do dentista autenticado.

#### GET /dentists/profile/patients
Retorna pacientes do dentista autenticado.

#### GET /dentists/profile/schedule
Retorna a agenda do dentista.

#### PUT /dentists/profile/schedule
Atualiza a agenda do dentista.

**Body:**
```json
{
    "schedule": [
        {
            "day_of_week": 1,
            "start_time": "08:00",
            "end_time": "18:00",
            "is_available": true
        }
    ]
}
```

#### GET /dentists/profile/statistics
Retorna estatísticas do dentista.

### Especializações

#### GET /specializations
Lista todas as especializações.

#### POST /specializations
Cria uma nova especialização (apenas admin).

#### PUT /specializations/{id}
Atualiza uma especialização (apenas admin).

#### DELETE /specializations/{id}
Remove uma especialização (apenas admin).

### Procedimentos

#### GET /procedures
Lista todos os procedimentos.

#### GET /procedures/{id}
Retorna dados de um procedimento específico.

#### GET /procedures/categories
Lista todas as categorias de procedimentos.

#### GET /procedures/category/{category}
Lista procedimentos por categoria.

## 📊 Códigos de Status HTTP

- `200` - Sucesso
- `201` - Criado com sucesso
- `400` - Requisição inválida
- `401` - Não autenticado
- `403` - Acesso negado
- `404` - Não encontrado
- `422` - Dados de validação inválidos
- `500` - Erro interno do servidor

## 🔍 Filtros e Paginação

### Paginação
Todos os endpoints de listagem suportam paginação:

**Query Parameters:**
- `page`: Número da página (padrão: 1)
- `per_page`: Itens por página (padrão: 15)

**Resposta:**
```json
{
    "success": true,
    "data": {
        "data": [...],
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

### Filtros
Muitos endpoints suportam filtros:

**Exemplo:**
```http
GET /appointments?start_date=2024-01-01&end_date=2024-01-31&status=confirmed
```

## 🔒 Permissões

### Roles
- `admin`: Acesso total ao sistema
- `dentist`: Acesso a agendamentos e pacientes
- `patient`: Acesso aos próprios dados e agendamentos

### Permissões
- `view-own-profile`: Visualizar próprio perfil
- `update-own-profile`: Atualizar próprio perfil
- `view-own-appointments`: Visualizar próprios agendamentos
- `create-appointment`: Criar agendamentos
- `cancel-own-appointment`: Cancelar próprios agendamentos
- `view-dentist-appointments`: Visualizar agendamentos do dentista
- `update-appointment-status`: Atualizar status de agendamentos
- `manage-specializations`: Gerenciar especializações
- `manage-procedures`: Gerenciar procedimentos

## 🚨 Tratamento de Erros

### Formato de Erro
```json
{
    "success": false,
    "message": "Descrição do erro",
    "errors": {
        "field": ["Mensagem de validação"]
    }
}
```

### Exemplos de Erros

#### Validação
```json
{
    "success": false,
    "message": "Dados inválidos",
    "errors": {
        "email": ["O campo email é obrigatório"],
        "password": ["A senha deve ter pelo menos 8 caracteres"]
    }
}
```

#### Não Autenticado
```json
{
    "success": false,
    "message": "Token de autenticação inválido"
}
```

#### Acesso Negado
```json
{
    "success": false,
    "message": "Acesso negado. Você não tem permissão para acessar este recurso"
}
```

## 📝 Exemplos de Uso

### Criar um Agendamento

```bash
curl -X POST http://localhost:8000/api/appointments \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "dentist_id": 1,
    "appointment_date": "2024-01-15",
    "appointment_time": "10:00",
    "duration": 60,
    "notes": "Consulta de rotina"
  }'
```

### Listar Agendamentos

```bash
curl -X GET "http://localhost:8000/api/appointments?start_date=2024-01-01&end_date=2024-01-31" \
  -H "Authorization: Bearer {token}"
```

### Obter Horários Disponíveis

```bash
curl -X GET "http://localhost:8000/api/appointments/available-slots?dentist_id=1&date=2024-01-15" \
  -H "Authorization: Bearer {token}"
```

## 🔄 Rate Limiting

A API implementa rate limiting para prevenir abuso:

- **Geral**: 60 requisições por minuto
- **Login**: 5 tentativas por minuto
- **API**: 10 requisições por segundo

## 📱 SDKs e Bibliotecas

### JavaScript/jQuery
```javascript
// Configurar base URL e token
$.ajaxSetup({
    headers: {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json'
    }
});

// Fazer requisição
$.get('/api/appointments', function(data) {
    console.log(data);
});
```

### PHP
```php
$response = Http::withHeaders([
    'Authorization' => 'Bearer ' . $token,
    'Accept' => 'application/json'
])->get('http://localhost:8000/api/appointments');
```

## 🆘 Suporte

Para suporte técnico ou dúvidas sobre a API:

- Email: 71724@sga.pucminas.br
- GitHub: [Issues](https://github.com/seu-usuario/odonto360-system/issues)
