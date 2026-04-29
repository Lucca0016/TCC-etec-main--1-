---
name: TCC-etec project overview
description: Overview of the FETEL educational platform project structure and key components
type: project
---

Projeto TCC-etec é uma plataforma escolar chamada FETEL - Sistema de gestão educacional escrito em PHP.

**Estrutura principal:**
- app/: Contém controllers, models, services, core, domain, http, support, config
- Public/: Assets públicos
- Resources/: Views/templates
- Routes/: Definições de rotas
- vendor/: Dependências Composer
- node_modules/: Dependências frontend
- .env: Variáveis de ambiente
- composer.json: Requer PHP ^8.1 e vlucas/phpdotenv

**Componentes-chave examinados:**
1. AuthService.php (app/Services/): Sistema de autenticação com:
   - Rate limiting por fingerprint (IP + User-Agent)
   - Validação de perfis (admin, professor, secretaria, aluno)
   - Bloqueio de conta após 5 tentativas falhas/dia
   - Log de auditoria
   - Redirecionamento baseado no papel

2. User.php (app/Models/): Modelo de dados com:
   - Busca por email/ID
   - Controle de tentativas (bloqueio após 5 attempts/dia)
   - Validação de senha com password_verify/bcrypt
   - Funcionalidades de auditoria e busca de notícias
   - Paginação e contagem

**Fluxo de autenticação:**
1. Validação de entrada (email/senha obrigatórios)
2. Verificação de rate limiting (fingerprint)
3. Busca do usuário por email
4. Validação de perfil/papel
5. Verificação se conta está bloqueada
6. Validação de senha
7. Sucesso: limpa tentativas, retorna dados do usuário
8. Falha: incrementa tentativas, retorna erro com tentativas restantes

O projeto segue boa separação de responsabilidades e boas práticas de código PHP moderno.
---