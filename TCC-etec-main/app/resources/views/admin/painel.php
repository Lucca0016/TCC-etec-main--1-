<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Painel Administrativo — FETEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/TCC-etec/public/css/admin.css" />
</head>
<body class="admin-body">
    <header class="admin-header">
        <div class="container">
            <div class="header-content">
                <div class="brand-section">
                    <img class="brand-logo" src="/TCC-etec/public/img/fetel_sem_fundo.png" alt="FETEL" loading="lazy" />
                    <div>
                        <h1 class="app-title">FETEL</h1>
                        <p class="app-subtitle">Sistema de Gestão Educacional</p>
                    </div>
                </div>

                <div class="user-info">
                    <div class="user-avatar">
                        <span class="avatar-initials"><?php echo strtoupper(substr($usuario_nome, 0, 2)); ?></span>
                    </div>
                    <div class="user-details">
                        <p class="user-name"><?php echo htmlspecialchars($usuario_nome, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="user-role">Administrador</p>
                    </div>
                    <a href="/TCC-etec/logout" class="btn btn-outline">Sair</a>
                </div>
            </div>
        </div>
    </header>

    <main class="admin-main">
        <div class="container">
            <!-- Overview Cards -->
            <section class="overview-cards">
                <div class="cards-grid">
                    <div class="card card-stat">
                        <div class="card-header">
                            <h3 class="card-title">Livros no Catálogo</h3>
                            <p class="card-subtitle">Total disponível</p>
                        </div>
                        <div class="card-body">
                            <div class="stat-number"><?php echo number_format($contadores['total_livros'] ?? 0); ?></div>
                        </div>
                    </div>

                    <div class="card card-stat">
                        <div class="card-header">
                            <h3 class="card-title">Notícias Publicadas</h3>
                            <p class="card-subtitle">Comunicações ativas</p>
                        </div>
                        <div class="card-body">
                            <div class="stat-number"><?php echo number_format($contadores['total_noticias'] ?? 0); ?></div>
                        </div>
                    </div>

                    <div class="card card-stat">
                        <div class="card-header">
                            <h3 class="card-title">Solicitações Pendentes</h3>
                            <p class="card-subtitle">Aguardando aprovação</p>
                        </div>
                        <div class="card-body">
                            <div class="stat-number"><?php echo number_format($contadores['solicitacoes_pendentes'] ?? 0); ?></div>
                        </div>
                    </div>

                    <div class="card card-stat">
                        <div class="card-header">
                            <h3 class="card-title">Empréstimos Atrasados</h3>
                            <p class="card-subtitle">Requere atenção</p>
                        </div>
                        <div class="card-body">
                            <div class="stat-number"><?php echo number_format($contadores['emprestimos_atrasados'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Actions -->
            <section class="quick-actions">
                <div class="section-header">
                    <h2 class="section-title">Ações Rápidas</h2>
                    <p class="section-description">Funcionalidades principais do administrador</p>
                </div>

                <div class="actions-grid">
                    <a href="/TCC-etec/admin/livros" class="action-card">
                        <div class="action-icon">📚</div>
                        <div class="action-content">
                            <h3 class="action-title">Gerenciar Livros</h3>
                            <p class="action-description">Adicionar, editar e remover livros do catálogo</p>
                        </div>
                    </a>

                    <a href="/TCC-etec/admin/noticias" class="action-card">
                        <div class="action-icon">📰</div>
                        <div class="action-content">
                            <h3 class="action-title">Gerenciar Notícias</h3>
                            <p class="action-description">Criar e publicar comunicados e notícias</p>
                        </div>
                    </a>

                    <a href="/TCC-etec/api/usuarios" class="action-card">
                        <div class="action-icon">👥</div>
                        <div class="action-content">
                            <h3 class="action-title">Gerenciar Usuários</h3>
                            <p class="action-description">Criar novos usuários e gerenciar perfis</p>
                        </div>
                    </a>

                    <a href="/TCC-etec/secretaria/relatorios" class="action-card">
                        <div class="action-icon">📊</div>
                        <div class="action-content">
                            <h3 class="action-title">Relatórios</h3>
                            <p class="action-description">Visão geral e estatísticas do sistema</p>
                        </div>
                    </a>
                </div>
            </section>

            <!-- Recent Activity -->
            <section class="recent-activity">
                <div class="section-header">
                    <h2 class="section-title">Atividade Recent</h2>
                </div>

                <div class="activity-list">
                    <?php if (!empty($funcionario)): ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <span class="avatar-initials bg-primary"><?php echo strtoupper(substr($usuario_nome, 0, 1)); ?></span>
                            </div>
                            <div class="activity-content">
                                <h3 class="activity-title">Login no Sistema</h3>
                                <p class="activity-description">Administrador acessou o painel</p>
                                <time class="activity-time">Agora</time>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Additional activity items would go here -->
                    <div class="activity-item">
                        <div class="activity-icon">
                            <span class="avatar-initials bg-secondary">📊</span>
                        </div>
                        <div class="activity-content">
                            <h3 class="activity-title">Visão Geral do Sistema</h3>
                            <p class="activity-description">Dashboard administrativo carregado com sucesso</p>
                            <time class="activity-time">Agora</time>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer class="admin-footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> FETEL - Sistema de Gestão Educacional. Todos os direitos reservados.</p>
                <div class="footer-links">
                    <a href="/TCC-etec/sobre">Sobre</a>
                    <a href="/TCC-etec/contato">Contato</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Admin-specific JavaScript would go here
        console.log('Admin dashboard loaded');
    </script>
</body>
</html>