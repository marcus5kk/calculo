# AgendaJá

Sistema de agendamentos com app Flutter (Android/iOS) e painel admin PHP hospedado na Hostgator.

## Estrutura do Projeto

```
lib/                        → Código Flutter (app mobile)
  main.dart                 → Entrada do app + splash screen
  config/app_config.dart    → URL base da API (CONFIGURE AQUI)
  models/usuario.dart       → Modelo de usuário
  services/api_service.dart → Comunicação com a API PHP
  screens/
    login_screen.dart
    register_screen.dart
    usuario_final/          → Interface do usuário final
    estabelecimento/        → Interface do estabelecimento
    funcionario/            → Interface do funcionário

php/                        → Backend PHP para hospedar na Hostgator
  api/                      → API REST consumida pelo app Flutter
    config.php              → Configuração do banco (credenciais)
    login.php
    register.php
    create_funcionario.php
    get_funcionarios.php
  admin/                    → Painel admin (abrir no navegador)
    index.php               → Login admin (admin/admin)
    dashboard.php
    estabelecimentos.php    → Criar/gerenciar estabelecimentos
    usuarios.php
  setup/
    database.sql            → SQL para criar as tabelas
    install.php             → Instalador automático
```

## 3 Níveis de Usuário

| Nível | Tipo | Como é criado | Login no app |
|-------|------|---------------|--------------|
| 1 | Usuário Final | Pelo próprio app (cadastro livre) | Sim — interface azul |
| 2 | Estabelecimento | Pelo admin no painel PHP | Sim — interface verde |
| 3 | Funcionário | Pelo estabelecimento dentro do app | Sim — interface roxa |

## Como usar

### 1. Configurar a URL da API no Flutter
Edite `lib/config/app_config.dart` e troque `SEU_DOMINIO.com.br` pelo seu domínio real:
```dart
static const String baseUrl = 'https://SEU_DOMINIO.com.br/agendaja/api';
```

### 2. Fazer upload do PHP para a Hostgator
- Suba a pasta `php/` inteira para o servidor
- Acesse `https://seu-dominio/php/setup/install.php` para criar o banco
- Delete `install.php` após instalação

### 3. Acesso ao painel admin
- URL: `https://seu-dominio/php/admin/`
- Login: `admin` / Senha: `admin`
- **Troque a senha após o primeiro acesso!**

## Banco de Dados MySQL (Hostgator)
- Host: localhost
- Banco: marc4901_agendaja
- Usuário: marc4901_agendaja

## User Preferences
- Desenvolvimento por etapas (uma coisa de cada vez)
- Idioma: Português Brasileiro
- App: Flutter para Android e iOS
- Backend: PHP + MySQL na Hostgator
