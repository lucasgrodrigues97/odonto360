# CI/CD Pipeline - Odonto360

Este diretório contém as configurações de CI/CD para o projeto Odonto360.

## 🚀 Workflows Disponíveis

### 1. CI/CD Pipeline Principal (`ci-cd.yml`)
Pipeline principal que executa em push/PR para as branches `main` e `develop`.

**Jobs:**
- **Test**: Executa testes unitários e de integração
- **Security**: Auditoria de segurança e análise de código
- **Integration Test**: Testes de integração com Docker
- **Build**: Cria artefatos de build para produção
- **Deploy**: Deploy automático para AWS (apenas na branch main)
- **Notify**: Notificações de status do pipeline

### 2. Deploy de Produção (`deploy-production.yml`)
Pipeline manual para deploy em ambientes específicos.

**Características:**
- Execução manual via GitHub Actions
- Escolha de ambiente (staging/production)
- Confirmação obrigatória antes do deploy
- Deploy usando Terraform

### 3. Análise de Segurança (`codeql.yml`)
Análise de segurança automatizada do código.

**Características:**
- Análise semanal automática
- Análise em PRs e pushes
- Suporte a PHP
- Integração com GitHub Security

## 🔧 Configuração Necessária

### Secrets do GitHub
Configure os seguintes secrets no repositório:

```
AWS_ACCESS_KEY_ID
AWS_SECRET_ACCESS_KEY
```

### Variáveis de Ambiente
As seguintes variáveis são configuradas automaticamente:

```
PHP_VERSION=8.1
NODE_VERSION=18
MYSQL_ROOT_PASSWORD=password
MYSQL_DATABASE=odonto360_test
APP_ENV=testing
```

## 📊 Status dos Jobs

| Job | Descrição | Status |
|-----|-----------|--------|
| Tests | Testes unitários e de integração | ✅ |
| Security | Auditoria de segurança | ✅ |
| Integration | Testes com Docker | ✅ |
| Build | Criação de artefatos | ✅ |
| Deploy | Deploy para AWS | ✅ |
| Notify | Notificações | ✅ |

## 🛠️ Ferramentas Utilizadas

- **PHPUnit**: Testes unitários
- **Laravel Pint**: Análise de código
- **Composer Audit**: Auditoria de segurança
- **Docker**: Testes de integração
- **Terraform**: Infraestrutura como código
- **AWS**: Deploy na nuvem
- **CodeQL**: Análise de segurança

## 📈 Métricas

- **Cobertura de Testes**: Configurada com Xdebug
- **Tempo de Execução**: ~15-20 minutos
- **Ambientes**: Staging e Production
- **Frequência**: A cada push/PR + semanal

## 🔍 Troubleshooting

### Problemas Comuns

1. **Falha nos testes**: Verifique se todas as dependências estão instaladas
2. **Falha no deploy**: Verifique as credenciais AWS
3. **Falha no Docker**: Verifique se o Dockerfile está correto

### Logs

Todos os logs estão disponíveis na aba "Actions" do GitHub.

## 📝 Contribuição

Para adicionar novos jobs ou modificar o pipeline:

1. Edite o arquivo `.yml` correspondente
2. Teste localmente se possível
3. Crie um PR com as mudanças
4. Aguarde a aprovação e merge

## 🔗 Links Úteis

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Laravel Testing](https://laravel.com/docs/testing)
- [Terraform AWS Provider](https://registry.terraform.io/providers/hashicorp/aws/latest)
- [Docker Documentation](https://docs.docker.com/)
