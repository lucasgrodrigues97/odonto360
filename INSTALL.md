# Instalação e Configuração do Odonto360

## 📋 Pré-requisitos

Antes de começar, certifique-se de ter instalado:

- **PHP 8.1+** com extensões: mbstring, dom, fileinfo, mysql, pdo_mysql, zip, gd, curl, xml, bcmath, soap, intl, readline, libxml, openssl, pdo, tokenizer, ctype, json, iconv, session, simplexml, xmlreader, xmlwriter, zip, zlib
- **Composer** 2.0+
- **Node.js** 16+ e **npm** 8+
- **MySQL** 8.0+ ou **MariaDB** 10.3+
- **Git**

## 🚀 Instalação Local

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/odonto360-system.git
cd odonto360-system
```

### 2. Instale as dependências do PHP

```bash
composer install
```

### 3. Instale as dependências do Node.js

```bash
npm install
```

### 4. Configure o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure o banco de dados

Edite o arquivo `.env` e configure as variáveis do banco:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=odonto360
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

### 6. Execute as migrações e seeders

```bash
php artisan migrate --seed
```

### 7. Compile os assets

```bash
npm run dev
```

### 8. Inicie o servidor

```bash
php artisan serve
```

A aplicação estará disponível em `http://localhost:8000`

## 🐳 Instalação com Docker

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/odonto360-system.git
cd odonto360-system
```

### 2. Configure o ambiente

```bash
cp .env.example .env
```

### 3. Inicie os containers

```bash
docker-compose up -d
```

### 4. Execute as migrações e seeders

```bash
docker-compose exec app php artisan migrate --seed
```

### 5. Compile os assets

```bash
docker-compose exec app npm run dev
```

A aplicação estará disponível em `http://localhost`

## 🔧 Configuração Adicional

### Configuração do OAuth (Google)

1. Acesse o [Google Cloud Console](https://console.cloud.google.com/)
2. Crie um novo projeto ou selecione um existente
3. Ative a API do Google+ e configure as credenciais OAuth 2.0
4. Adicione as credenciais no arquivo `.env`:

```env
GOOGLE_CLIENT_ID=seu_client_id
GOOGLE_CLIENT_SECRET=seu_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback
```

### Configuração da IA (OpenAI)

1. Acesse o [OpenAI Platform](https://platform.openai.com/)
2. Crie uma API key
3. Adicione a chave no arquivo `.env`:

```env
OPENAI_API_KEY=sua_api_key
```

### Configuração do WhatsApp (Twilio)

1. Acesse o [Twilio Console](https://console.twilio.com/)
2. Configure um número do WhatsApp
3. Adicione as credenciais no arquivo `.env`:

```env
TWILIO_SID=seu_sid
TWILIO_TOKEN=seu_token
TWILIO_WHATSAPP_NUMBER=seu_numero
```

## 🧪 Executando Testes

### Testes Unitários

```bash
php artisan test
```

### Testes com Coverage

```bash
php artisan test --coverage
```

### Testes de Integração

```bash
php artisan test --testsuite=Feature
```

## 🚀 Deploy na AWS

### 1. Configure o Terraform

```bash
cd infrastructure
cp terraform.tfvars.example terraform.tfvars
```

Edite o arquivo `terraform.tfvars` com suas configurações:

```hcl
aws_region = "us-east-1"
environment = "production"
db_password = "sua_senha_segura"
```

### 2. Inicialize o Terraform

```bash
terraform init
```

### 3. Planeje a infraestrutura

```bash
terraform plan
```

### 4. Aplique as configurações

```bash
terraform apply
```

### 5. Configure o DNS

Após o deploy, configure o DNS para apontar para o Load Balancer da AWS.

## 📊 Monitoramento

### CloudWatch

O sistema está configurado para enviar logs e métricas para o CloudWatch:

- Logs da aplicação
- Métricas de CPU, memória e disco
- Logs do Apache/Nginx

### Health Check

A aplicação possui endpoints de health check:

- `GET /api/health` - Status da API
- `GET /health` - Status básico

## 🔐 Segurança

### Configurações de Segurança

- Criptografia de dados sensíveis
- Headers de segurança configurados
- Rate limiting nas APIs
- Validação de entrada de dados
- Sanitização de outputs

### Backup

Configure backups automáticos:

```bash
# Backup do banco de dados
php artisan backup:run

# Backup dos arquivos
php artisan backup:run --only-files
```

## 🐛 Troubleshooting

### Problemas Comuns

1. **Erro de permissões**: Execute `chmod -R 755 storage bootstrap/cache`
2. **Erro de banco**: Verifique as credenciais no `.env`
3. **Erro de cache**: Execute `php artisan cache:clear`
4. **Erro de assets**: Execute `npm run dev`

### Logs

Os logs estão disponíveis em:
- `storage/logs/laravel.log` - Logs da aplicação
- `/var/log/nginx/` - Logs do Nginx
- `/var/log/apache2/` - Logs do Apache

## 📞 Suporte

Para suporte técnico, entre em contato:

- Email: 71724@sga.pucminas.br
- GitHub: [Issues](https://github.com/seu-usuario/odonto360-system/issues)

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.
