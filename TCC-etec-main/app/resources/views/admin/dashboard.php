<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard — Painel Admin FETEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/TCC-etec/public/css/admin.css" />
    <style>
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: var(--card); border: 1px solid var(--card-border); border-radius: 12px; padding: 24px; transition: all 0.3s; }
        .stat-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.08); transform: translateY(-2px); }
        .stat-icon { font-size: 2.4rem; margin-bottom: 12px; }
        .stat-label { color: var(--ink-muted); font-size: 0.9rem; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .stat-value { font-size: 2rem; font-weight: 700; color: var(--accent); }
        .stat-change { font-size: 0.85rem; color: #10b981; margin-top: 8px; }
        .quick-actions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .action-card { background: var(--card); border: 1px solid var(--card-border); border-radius: 12px; padding: 24px; text-decoration: none; color: inherit; transition: all 0.3s; display: flex; align-items: flex-start; gap: 16px; }
        .action-card:hover { box-shadow: 0 12px 32px rgba(0,0,0,0.12); transform: translateY(-4px); border-color: var(--accent); }
        .action-icon { font-size: 2rem; }
        .action-content { flex: 1; }
        .action-title { margin: 0; font-weight: 700; color: var(--ink); font-size: 1rem; }
        .action-description { margin: 6px 0 0; color: var(--ink-muted); font-size: 0.9rem; }
        .section { background: var(--card); border: 1px solid var(--card-border); border-radius: 12px; padding: 24px; margin-bottom: 40px; }
        .section-title-small { font-size: 1.2rem; font-weight: 700; margin: 0 0 16px; color: var(--ink); }
        .recent-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--card-border); }
        .recent-item:last-child { border-bottom: 0; }
        .recent-name { font-weight: 600; color: var(--ink); }
        .recent-email { font-size: 0.9rem; color: var(--ink-muted); }
        .recent-date { font-size: 0.9rem; color: var(--ink-muted); }
        .system-status { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
        .status-item { padding: 12px; background: rgba(16, 185, 129, 0.1); border-radius: 8px; }
        .status-label { font-size: 0.9rem; color: var(--ink-muted); margin-bottom: 4px; }
        .status-value { font-weight: 700; color: #10b981; }
        @media (max-width: 768px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            .stat-value { font-size: 1.6rem; }
        }
    </style>
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
                        <span class="avatar-initials"><?php echo strtoupper(substr($user['nome'] ?? 'Admin', 0, 2)); ?></span>
                    </div>
                    <div class="user-details">
                        <p class="user-name"><?php echo htmlspecialchars($user['nome'] ?? 'Administrador', ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="user-role">Administrador</p>
                    </div>
                    <a href="/TCC-etec/logout" class="btn btn-outline">Sair</a>
                </div>
            </div>
        </div>
    </header>

    <main class="admin-main">
        <div class="container">
            <section>
                <h2 class="section-title">Dashboard de Controle</h2>
                <p class="section-description">Bem-vindo ao painel administrativo FETEL</p>
            </section>

            <!-- Estatísticas Principais -->
            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <div class="stat-label">Livros no Catálogo</div>
                    <div class="stat-value"><?php echo number_format($stats['total_livros'] ?? 0); ?></div>
                    <div class="stat-change">Total disponível</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📰</div>
                    <div class="stat-label">Notícias Publicadas</div>
                    <div class="stat-value"><?php echo number_format($stats['total_noticias'] ?? 0); ?></div>
                    <div class="stat-change">Comunicações ativas</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-label">Usuários Cadastrados</div>
                    <div class="stat-value"><?php echo number_format($stats['total_usuarios'] ?? 0); ?></div>
                    <div class="stat-change">No sistema</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-label">Solicitações Pendentes</div>
                    <div class="stat-value"><?php echo number_format($stats['solicitacoes_pendentes'] ?? 0); ?></div>
                    <div class="stat-change">Aguardando aprovação</div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <section>
                <h3 class="section-title-small">Ações Rápidas</h3>
            </section>

            <div class="quick-actions-grid">
                <a href="/TCC-etec/admin/usuarios" class="action-card">
                    <div class="action-icon">👤</div>
                    <div class="action-content">
                        <div class="action-title">Gerenciar Usuários</div>
                        <div class="action-description">Crie, edite e remova usuários do sistema</div>
                    </div>
                </a>

                <a href="/TCC-etec/admin/usuarios/criar" class="action-card">
                    <div class="action-icon">➕</div>
                    <div class="action-content">
                        <div class="action-title">Criar Novo Usuário</div>
                        <div class="action-description">Adicione um novo usuário ao sistema</div>
                    </div>
                </a>

                <a href="/TCC-etec/admin/noticias" class="action-card">
                    <div class="action-icon">📝</div>
                    <div class="action-content">
                        <div class="action-title">Gerenciar Notícias</div>
                        <div class="action-description">Publique e edite notícias do site</div>
                    </div>
                </a>

                <a href="/TCC-etec/admin/livros" class="action-card">
                    <div class="action-icon">📚</div>
                    <div class="action-content">
                        <div class="action-title">Gerenciar Livros</div>
                        <div class="action-description">Administre o catálogo da biblioteca</div>
                    </div>
                </a>
            </div>

            <!-- Usuários Recentes -->
            <div class="section">
                <h3 class="section-title-small">Usuários Recentes</h3>
                <?php if (!empty($recentUsers)): ?>
                    <?php foreach ($recentUsers as $usr): ?>
                        <div class="recent-item">
                            <div>
                                <div class="recent-name"><?php echo htmlspecialchars($usr['nome_completo'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="recent-email"><?php echo htmlspecialchars($usr['email'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                            <div class="recent-date"><?php echo date('d/m/Y', strtotime($usr['criado_em'] ?? 'now')); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--ink-muted);">Nenhum usuário cadastrado recentemente.</p>
                <?php endif; ?>
            </div>

            <!-- Saúde do Sistema -->
            <div class="section">
                <h3 class="section-title-small">Saúde do Sistema</h3>
                <div class="system-status">
                    <div class="status-item">
                        <div class="status-label">Banco de Dados</div>
                        <div class="status-value">✓ Online</div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">Autenticação</div>
                        <div class="status-value">✓ Funcionando</div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">API</div>
                        <div class="status-value">✓ Ativa</div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">Cache</div>
                        <div class="status-value">✓ Normal</div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif; background: var(--canvas); color: var(--ink); }
        .container { max-width: 1400px; margin: 0 auto; padding: 0 24px; }
        .admin-header { background: var(--card); border-bottom: 1px solid var(--card-border); padding: 16px 0; position: sticky; top: 0; z-index: 100; }
        .header-content { display: flex; justify-content: space-between; align-items: center; }
        .brand-section { display: flex; align-items: center; gap: 16px; }
        .brand-logo { width: 36px; height: auto; }
        .app-title { margin: 0; font-size: 1.2rem; color: var(--ink); }
        .app-subtitle { margin: 2px 0 0; font-size: 0.85rem; color: var(--ink-muted); }
        .user-info { display: flex; align-items: center; gap: 12px; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--accent); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .user-name { margin: 0; font-weight: 600; font-size: 0.95rem; }
        .user-role { margin: 2px 0 0; font-size: 0.85rem; color: var(--ink-muted); }
        .user-details { text-align: right; }
        .btn { padding: 10px 16px; border-radius: 8px; border: 0; cursor: pointer; text-decoration: none; font-weight: 600; transition: all 0.2s; display: inline-block; }
        .btn-outline { background: transparent; border: 1px solid var(--card-border); color: var(--ink); }
        .btn-outline:hover { background: var(--card-border); }
        .admin-main { padding: 40px 0; }
        .section-title { font-size: 1.8rem; margin: 0 0 6px; color: var(--ink); font-weight: 700; }
        .section-description { margin: 0 0 28px; color: var(--ink-muted); font-size: 0.95rem; }
        :root {
            --canvas: #0d1b2a;
            --canvas-muted: #111f33;
            --card: #f7f9fc;
            --card-border: #e0e5ee;
            --ink: #0c1b2f;
            --ink-muted: #5b6678;
            --accent: #1f6feb;
            --error-bg: #fdecec;
            --error-text: #9f1d1d;
        }
    </style>
</body>
</html>
