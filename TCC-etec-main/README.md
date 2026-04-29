# 📚 TCC ETEC - Sistema de Gestão Integrado # FETEL — Plataforma Escolar (stack PHP 8 + MVC)



## 🎯 Visão Geral> Código totalmente reorganizado em março/2026 para seguir um fluxo MVC claro, com autoloader PSR-4, roteador centralizado e separação explícita entre domínio, apresentação e heranças legadas.



Sistema web completo de **Gestão Integrada** desenvolvido como Trabalho de Conclusão de Curso (TCC) na ETEC. Inclui autenticação segura, biblioteca digital com catálogo online e gerenciamento de empréstimos.## Sumário



**Tecnologias**: PHP 8.1+ | MySQL 8+ | HTML5 | CSS3 | JavaScript Vanilla | OOP1. [Visão geral](#visão-geral)

2. [Stack & requisitos](#stack--requisitos)

---3. [Como rodar](#como-rodar)

4. [Estrutura do projeto](#estrutura-do-projeto)

## 📦 Funcionalidades Principais5. [Fluxo HTTP](#fluxo-http)

6. [Convenções de código](#convenções-de-código)

### 🔐 Autenticação e Segurança7. [Build Frontend](#build-frontend)

- Login com 5 perfis (Admin, Aluno, Professor, Secretário)8. [Diagnóstico rápido](#diagnóstico-rápido)

- Recuperação de senha segura9. [Próximos passos sugeridos](#próximos-passos-sugeridos)

- Sessões protegidas

- Auditoria de acessos---

- Proteção contra SQL Injection

## Visão geral

### 📚 Biblioteca Digital

- **Catálogo Online** com busca em tempo real- `public/index.php` é o **único** front-controller. Ele carrega `vendor/autoload.php`, registra rotas em `routes/web.php` e delega toda requisição ao `App\Core\Application`.

- Filtros por categoria, ano, disponibilidade- `app/Core` contém o runtime (bootstrap, router, aplicação, sessão, database helper e view renderer).

- Paginação inteligente- `app/Http/Controllers` encapsula toda lógica HTTP. As ações renderizam views de `resources/views` usando o helper `view()` ou retornam JSON.

- Capas de livros (integração com banco)- `app/Domain` abriga regras de negócio isoladas (ex.: fluxo “Fale Conosco”).

- Botão "Ler PDF" para acesso a livros digitais- Tudo que ainda depende de scripts procedurais foi movido para `app/Legacy/**` e permanece acessível apenas para consulta/migração (não é servido pelo front-controller).

- Sincronização automática com banco de dados

---

### 👥 Gestão de Usuários

- Dashboard personalizado por perfil## Stack & requisitos

- Histórico de empréstimos

- Controle de disponibilidade| Componente | Versão mínima | Observações |

- Multas automáticas para atrasos|------------|---------------|-------------|

| PHP        | 8.1           | PDO habilitado, `ext-mbstring`, `ext-json` |

### 📰 Notícias e Comunicados| Composer   | 2.x           | Para instalar deps e gerar autoload |

- Publicação de notícias| Node.js    | 18.x          | Apenas p/ build do frontend (Vite + Tailwind) |

- Feed atualizado| Banco      | MySQL/MariaDB | Configurado via `.env` (usa `config/database.php`) |

- Alertas e comunicados

Variáveis `.env` relevantes:

---

```

## 🏗️ Arquitetura do ProjetoAPP_ENV=local

APP_DEBUG=true

```APP_URL=http://localhost/TCC-etec

TCC-etec/DB_HOST=127.0.0.1

├── public/                          # Frontend - Tudo acessível publicamenteDB_PORT=3306

│   ├── index.html                  # Página inicialDB_DATABASE=tcc_etec

│   ├── login.html                  # Sistema de login (4 abas)DB_USERNAME=root

│   ├── recuperar_senha.html        # Recuperação de senhaDB_PASSWORD=secret

│   ├── catalogo.html               # Catálogo online ✨```

│   ├── api.php                     # Endpoints REST ✨

│   ├── css/---

│   ├── js/

│   └── img/## Como rodar

│

├── app/                             # Backend - OOP```powershell

│   ├── Config/# Dentro de d:\wamp64\www\TCC-etec

│   │   └── Database.php            # Singleton Connectioncopy .env.example .env            # configure o arquivo com seus dados

│   ├── Models/composer install                  # instala PHP deps

│   │   └── LivroModel.php          # Data Access Layernpm install --prefix resources/views/frontend-src  # instala build do front (opcional)

│   ├── Controllers/npm run build --prefix resources/views/frontend-src

│   │   └── CatalogoController.php  # Business Logic

│   └── ...# Servidor PHP embutido (ou use WAMP apontando para /public)

│php -S localhost:8080 -t public

├── pdf/                             # Armazenamento de PDFs```

│   └── README.md

│Abra `http://localhost:8080` e todo tráfego passará pelo roteador.

├── Docs/

│   └── CATALOGO_SISTEMA.md         # Documentação completa---

│

└── .htaccess                        # URL Routing (clean URLs)## Estrutura do projeto

```

```

---TCC-etec/

├── public/                     # assets acessíveis pelo servidor web

## 🚀 Começando│   ├── index.php               # front-controller

│   ├── index.html              # landing estática (injeção de assets automatizada)

### Requisitos│   ├── css/, js/, img/, frontend/

- PHP 8.1+├── app/

- MySQL 8.0+│   ├── Core/                   # Application, Router, SessionManager, View, Database, Bootstrap

- Apache 2.4+ com mod_rewrite│   ├── Http/Controllers/       # controladores atuais (Home, FaleConosco, Notícias...)

- Composer (opcional)│   ├── Domain/                 # regras de negócio (ex.: FaleConosco)

│   ├── Models/                 # modelos POO já migrados

### Instalação│   ├── Support/                # helpers globais, Auth, Frontend renderer

│   ├── Legacy/**               # scripts e classes procedurais arquivadas

1. **Clone o repositório**├── config/

```bash│   ├── app.php                 # nome, timezone, locale...

git clone https://github.com/vivolli/TCC-etec.git│   └── database.php            # conexões PDO

cd TCC-etec├── resources/

```│   └── views/                  # templates PHP/HTML + assets manifest-aware

├── routes/

2. **Configure o banco de dados**│   └── web.php                 # definição das rotas HTTP

```bash├── composer.json               # autoload PSR-4 ("App\\": "app/") + helpers globais

# Crie banco: TCC_ETEC├── .htaccess                   # reescreve tudo para /public

# Importe: database.sql└── README.md                   # este arquivo

mysql -u root TCC_ETEC < database.sql```

```

Legado catalogado:

3. **Configure credenciais** (se necessário)

Edite `app/Config/Database.php`:| Pasta | Uso atual |

```php|-------|-----------|

$host = 'localhost';| `app/Legacy/Controllers` | Implementações antigas (procedural). Servem como referência para futuras migrações. |

$dbname = 'TCC_ETEC';| `app/Legacy/Models` | Modelos antigos (com consultas diretas). Mantidos apenas para consulta. |

$user = 'root';| `app/Legacy-root-*` | Arquivos/pastas que ficavam soltos na raiz antes da reestruturação. |

$password = '';

```---



4. **Acesse no navegador**## Fluxo HTTP

```

http://localhost/TCC-etec/1. Apache (ou PHP built-in) recebe a requisição em `/`.

```2. `.htaccess` garante que tudo passe por `public/index.php`.

3. `public/index.php` carrega `vendor/autoload.php`, registra rotas em `routes/web.php` e chama `$app->run()`.

---4. `App\Core\Application` delega para `App\Core\Router`.

5. Controller resolve a resposta (HTML via `view()`, JSON ou redirect). Se nenhuma rota corresponder, o fallback renderiza `public/index.html` + assets Vite.

## 📚 Catálogo Online (Principal)

Rotas atuais (`routes/web.php`):

### URL de Acesso

```| Método | Caminho | Controller | Comentário |

http://localhost/TCC-etec/catalogo|--------|---------|------------|------------|

```| GET    | `/`                 | `HomeController@__invoke` | Renderiza landing page e trata redirecionamento por papel | 

| GET/POST | `/fale-conosco`  | `FaleConoscoController@index` | Formulário + processamento CSRF usando domínio POO |

### Funcionalidades| GET    | `/Noticias`         | `NoticiasController@index` | Lista padrão |

- ✅ Busca em tempo real (debounced 300ms)| GET    | `/Noticias/{slug}`  | `NoticiasController@show`  | Carrega view específica em `resources/views/Noticias/` |

- ✅ Filtros dinâmicos (categoria, ano, disponibilidade)

- ✅ Paginação (20 livros/página)---

- ✅ Capas dos livros (imagens)

- ✅ Botão "Ler PDF" (se disponível)## Convenções de código

- ✅ Badge de disponibilidade

- ✅ Responsivo (Desktop, Tablet, Mobile)- **Nome de classes**: `App\` namespace único; subdomínios (Http, Domain, Support) seguem PSR-4.

- **Views**: arquivos em `resources/views`. Use `view('pasta/arquivo', ['dados' => $valor])` para renderizar.

### Dados do Catálogo- **Helpers globais**: definidos em `app/Support/helpers.php` (`base_path()`, `config()`, `db()`, `view()`, etc.). São carregados automaticamente pelo Composer.

- **Sessão/Autenticação**: utilize `App\Support\Auth` ao invés dos antigos `_sessao.php`/`autenticacao.php`. Uma camada procedural compatível continua em `app/php/autenticacao.php` apenas para arquivos não migrados.

Todos os dados vêm da tabela `biblioteca_livros_midias`:- **Banco**: `App\Core\Database::connection()` retorna uma instância singleton de `PDO` baseada em `config/database.php`.



```sql---

SELECT 

  bl.id, bl.titulo, bl.autor, bl.categoria,## Build Frontend

  bl.copias_disponiveis, blm.url_imagem, blm.url_link

FROM biblioteca_livros blO novo frontend (Tailwind + Vite + TypeScript) vive dentro de `resources/views/frontend-src`.

LEFT JOIN biblioteca_livros_midias blm ON bl.id = blm.livro_id

``````powershell

npm install --prefix resources/views/frontend-src

### API Endpointsnpm run dev   --prefix resources/views/frontend-src  # ambiente watch com HMR

npm run build --prefix resources/views/frontend-src  # gera /public/frontend/manifest.json

#### 1. Listar Livros```

```

GET /TCC-etec/api/livrosA partial `resources/views/partials/assets.php` lê o manifest e injeta automaticamente `<link>`/`<script>` na landing page e em qualquer view que a inclua.

Parâmetros:

  - titulo (string)---

  - autor (string)

  - categoria (string)## Diagnóstico rápido

  - ano (int)

  - disponibilidade (todos|disponiveis|indisponiveis)| Sintoma | Ação recomendada |

  - ordenacao (titulo|data|autor)|---------|------------------|

  - pagina (int)| “Class not found” | Rode `composer dump-autoload`. Confirme namespace/arquivo em `app/`. |

| Erro de conexão PDO | Revise `.env` + `config/database.php`. Use `php artisan tinker`? (aqui: `php -r "var_dump(db()->query('select 1'))"`). |

Exemplo:| Tokens CSRF inválidos | Garanta que o formulário inclui `name="_csrf"` e que a sessão está habilitada. Veja `App\Core\Csrf`. |

GET /TCC-etec/api/livros?categoria=Programação&pagina=1| Frontend sem estilos | Execute `npm run build` no diretório `frontend-src` ou verifique `public/frontend/manifest.json`. |

```

---

#### 2. Detalhes do Livro

```## Próximos passos sugeridos

GET /TCC-etec/api/livros/:id

1. **Migrar dashboards** (`AdminInterface.php`, `secretaria.php`, etc.) para controllers dentro de `app/Http/Controllers`, usando layouts compartilhados.

Exemplo:2. **Substituir páginas de login antigas** por um fluxo dedicado (`AuthController`) com `App\Support\Auth` + `App\Core\Csrf`.

GET /TCC-etec/api/livros/13. **Escrever testes** (PHPUnit ou Pest) para `App\Domain\FaleConosco` e controllers críticos.

```4. **Mover APIs externas** (antiga pasta `APIs/`) para `routes/api.php` + controllers dedicados.

5. **Configurar CI** (GitHub Actions) executando `composer validate`, `php -l` e `npm run build`.

#### 3. Metadados do Catálogo

```Com essa fundação, o próximo desenvolvedor consegue evoluir somente lidando com classes e rotas claras — sem precisar navegar por arquivos soltos na raiz. 💪

GET /TCC-etec/api/catalogo/metadados

Retorna:
{
  "categorias": ["Ficção", "Programação", ...],
  "anos": [2023, 2022, 2021, ...]
}
```

---

## 📊 Banco de Dados

### Tabelas Principais

#### `biblioteca_livros`
```sql
CREATE TABLE biblioteca_livros (
  id INT PRIMARY KEY AUTO_INCREMENT,
  isbn VARCHAR(32),
  titulo VARCHAR(255) NOT NULL,
  autor VARCHAR(255),
  editora VARCHAR(255),
  ano_publicacao SMALLINT,
  copias_total INT DEFAULT 1,
  copias_disponiveis INT DEFAULT 1,
  descricao TEXT,
  categoria VARCHAR(100),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

#### `biblioteca_livros_midias`
```sql
CREATE TABLE biblioteca_livros_midias (
  id INT PRIMARY KEY AUTO_INCREMENT,
  livro_id INT,
  url_imagem VARCHAR(500),      -- URL da capa
  url_link VARCHAR(500),         -- URL do PDF ou link
  tipo VARCHAR(50),              -- 'pdf', 'link', etc
  FOREIGN KEY (livro_id) REFERENCES biblioteca_livros(id)
);
```

#### `biblioteca_emprestimos`
```sql
CREATE TABLE biblioteca_emprestimos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  livro_id INT,
  usuario_id INT,
  emprestado_em DATETIME,
  vencimento_em DATETIME,
  devolvido_em DATETIME,
  status VARCHAR(50),
  multa_centavos INT DEFAULT 0,
  FOREIGN KEY (livro_id) REFERENCES biblioteca_livros(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
```

---

## 🎨 Interface e UX

### Paleta de Cores
- **Primária**: #0056b3 (Azul Escuro)
- **Secundária**: #1e90ff (Azul Claro)
- **Sucesso**: #10b981 (Verde)
- **Erro**: #ef4444 (Vermelho)

### Responsividade
```
Desktop:  3 colunas
Tablet:   2 colunas
Mobile:   1 coluna
```

### Acessibilidade
- ✅ ARIA labels
- ✅ Contraste adequado
- ✅ Navegação por teclado
- ✅ Escape para fechar modais

---

## 🔒 Segurança

### Implementado
- **SQL Injection**: Prepared Statements em 100% das queries
- **XSS**: Escape de HTML com `htmlspecialchars()`
- **CSRF**: Tokens de segurança
- **Autenticação**: Hash bcrypt + salt
- **Sessões**: Timeout e regeneração de ID
- **Rate Limiting**: Máximo 5 tentativas de login/dia

### Headers de Segurança
```
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
```

---

## 📋 Como Usar

### Para Alunos
1. Acesse `/TCC-etec/login`
2. Selecione aba "Aluno"
3. Use credenciais
4. Clique em "Catálogo" no dashboard
5. Busque e acesse PDFs

### Para Secretários
1. Acesse `/TCC-etec/login`
2. Selecione aba "Secretário"
3. Gerencie catálogo e empréstimos
4. Monitore multas e devoluções

### Para Administradores
1. Acesse `/TCC-etec/login`
2. Selecione aba "Admin"
3. Controle total do sistema

---

## 🛠️ Desenvolvedor

### Adicionar um Livro COM PDF

**Passo 1**: Insira na tabela biblioteca_livros
```sql
INSERT INTO biblioteca_livros (titulo, autor, categoria, copias_total, copias_disponiveis)
VALUES ('Novo Livro', 'Autor', 'Categoria', 1, 1);
```

**Passo 2**: Adicione mídia
```sql
INSERT INTO biblioteca_livros_midias (livro_id, url_imagem, url_link, tipo)
VALUES (123, 'https://...capa.jpg', '/TCC-etec/pdf/livro.pdf', 'pdf');
```

**Passo 3**: Suba o PDF
Coloque arquivo em `/TCC-etec/pdf/livro.pdf`

**Passo 4**: Pronto!
O livro aparecerá automaticamente no catálogo com botão "Ler PDF"

### Estrutura OOP

#### LivroModel
```php
$model = new Livro($database);

// Buscar com filtros
$resultado = $model->buscar([
  'titulo' => 'Clean Code',
  'categoria' => 'Programação',
  'pagina' => 1
]);

// Obter categorias dinâmicas
$categorias = $model->obterCategorias();

// Obter anos de publicação
$anos = $model->obterAnos();

// Obter mídias de um livro
$midias = $model->obterMidias(123);
```

#### CatalogoController
```php
$controller = new CatalogoController();
$livros = $controller->listarLivros();
$metadados = $controller->obterMetadadosCatalogo();
```

---

## 📈 Performance

- **Lazy Loading** de imagens
- **Prepared Statements** para eficiência SQL
- **Paginação** de 20 livros/página
- **Cache** de filtros dinâmicos
- **Debouncing** de busca (300ms)

---

## 🔄 Fluxo de Dados

```
Usuário acessa /TCC-etec/catalogo
         ↓
JavaScript carrega /api/livros
         ↓
CatalogoController processa
         ↓
LivroModel executa query com JOIN
         ↓
Banco retorna livros + mídias
         ↓
API retorna JSON
         ↓
JavaScript renderiza cards
         ↓
Usuário vê capas + botões
```

---

## 📚 Documentação Adicional

Veja `Docs/CATALOGO_SISTEMA.md` para:
- Guia completo de uso
- Exemplos de API
- Configuração avançada
- Troubleshooting

---

## 🐛 Troubleshooting

### Catálogo não carrega
1. Verifique se MySQL está rodando
2. Confirme credenciais em `app/Config/Database.php`
3. Teste: `curl http://localhost/TCC-etec/api/livros`

### Botão "Ler PDF" não aparece
1. Verifique se `url_link` contém `.pdf`
2. Confirme caminho: `/TCC-etec/pdf/arquivo.pdf`
3. Teste URL direto no navegador

### URLs limpas não funcionam
1. Ative mod_rewrite: `a2enmod rewrite`
2. Copie `.htaccess` para raiz
3. Reinicie Apache

---

## 📝 Licença

Projeto educacional - ETEC 2026

---

## 👨‍💻 Autor

Desenvolvido como TCC ETEC

---

## ✅ Status

**Versão**: 2.0  
**Status**: ✅ Pronto para Produção  
**Última Atualização**: 29 de Março de 2026

---

## 🚀 Próximas Melhorias

- [ ] Leitor PDF integrado (PDFjs)
- [ ] Dashboard admin avançado
- [ ] Recomendações personalizadas
- [ ] Notificações em tempo real
- [ ] App mobile

---

**Para dúvidas ou sugestões, abra uma issue no repositório.**
