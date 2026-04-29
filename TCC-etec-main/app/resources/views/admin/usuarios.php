<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gerenciar Usuários — Painel Admin FETEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/TCC-etec/public/css/admin.css" />
    <style>
        .table-container { overflow-x: auto; margin-top: 24px; }
        .admin-table { width: 100%; border-collapse: collapse; background: var(--card); border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .admin-table thead { background: var(--card-border); }
        .admin-table th { padding: 14px 16px; text-align: left; font-weight: 600; color: var(--ink); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .admin-table tbody tr { border-top: 1px solid var(--card-border); transition: background 0.2s; }
        .admin-table tbody tr:hover { background: rgba(31, 111, 235, 0.02); }
        .admin-table td { padding: 14px 16px; color: var(--ink); }
        .badge { display: inline-block; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; }
        .badge.admin { background: rgba(31, 111, 235, 0.15); color: var(--accent); }
        .badge.professor { background: rgba(92, 44, 166, 0.15); color: #5c2ca6; }
        .badge.secretaria { background: rgba(15, 165, 118, 0.15); color: #0fa576; }
        .badge.aluno { background: rgba(59, 107, 255, 0.15); color: #3b6bff; }
        .btn-sm { padding: 8px 14px; font-size: 0.9rem; border-radius: 8px; border: 0; cursor: pointer; transition: all 0.2s; }
        .btn-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .btn-danger:hover { background: rgba(239, 68, 68, 0.2); }
        .actions-group { display: flex; gap: 8px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .pagination { display: flex; gap: 8px; margin-top: 24px; justify-content: center; }
        .pagination a, .pagination span { padding: 8px 12px; border-radius: 6px; text-decoration: none; }
        .pagination a { background: var(--card); color: var(--accent); border: 1px solid var(--card-border); }
        .pagination a:hover { background: var(--accent); color: white; }
        .pagination .active { background: var(--accent); color: white; border-color: var(--accent); }
        .alert { padding: 14px 16px; border-radius: 8px; margin-bottom: 20px; }
        .alert.success { background: var(--success-bg); color: var(--success-text); }
        .alert.error { background: var(--error-bg); color: var(--error-text); }
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
            <div class="page-header">
                <div>
                    <h2 class="section-title">Gerenciar Usuários</h2>
                    <p class="section-description">Crie, edite e remova usuários do sistema</p>
                </div>
                <a href="/TCC-etec/admin/usuarios/criar" class="btn btn-primary">+ Novo Usuário</a>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    ✓ <?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    ✗ <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Perfil</th>
                            <th>Data de Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($usuarios)): ?>
                            <?php foreach ($usuarios as $usr): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($usr['nome_completo'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($usr['email'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <span class="badge <?php echo htmlspecialchars($usr['papel'] ?? 'aluno', ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php 
                                                $papelLabel = [
                                                    'admin' => 'Administrador',
                                                    'professor' => 'Professor',
                                                    'secretaria' => 'Secretaria',
                                                    'aluno' => 'Aluno'
                                                ];
                                                echo $papelLabel[$usr['papel'] ?? 'aluno'] ?? 'N/A';
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($usr['criado_em'] ?? 'now')); ?></td>
                                    <td>
                                        <div class="actions-group">
                                            <a href="/TCC-etec/admin/usuarios/<?php echo $usr['id']; ?>/editar" class="btn btn-sm">Editar</a>
                                            <?php if ($usr['id'] != ($user['id'] ?? null)): ?>
                                                <form method="POST" action="/TCC-etec/admin/usuarios/<?php echo $usr['id']; ?>/deletar" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja deletar este usuário?');">
                                                    <button type="submit" class="btn btn-sm btn-danger">Deletar</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--ink-muted); padding: 40px;">
                                    Nenhum usuário cadastrado ainda.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_paginas > 1): ?>
                <div class="pagination">
                    <?php if ($pagina_atual > 1): ?>
                        <a href="?page=1">« Primeira</a>
                        <a href="?page=<?php echo $pagina_atual - 1; ?>">‹ Anterior</a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $pagina_atual - 2); $i <= min($total_paginas, $pagina_atual + 2); $i++): ?>
                        <?php if ($i == $pagina_atual): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($pagina_atual < $total_paginas): ?>
                        <a href="?page=<?php echo $pagina_atual + 1; ?>">Próxima ›</a>
                        <a href="?page=<?php echo $total_paginas; ?>">Última »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
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
        .btn { padding: 10px 16px; border-radius: 8px; border: 0; background: var(--accent); color: white; cursor: pointer; text-decoration: none; transition: all 0.2s; font-weight: 600; display: inline-block; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(31, 111, 235, 0.2); }
        .btn-outline { background: transparent; border: 1px solid var(--card-border); color: var(--ink); }
        .btn-outline:hover { background: var(--card-border); }
        .admin-main { padding: 40px 0; }
        .section-title { font-size: 1.8rem; margin: 0 0 6px; color: var(--ink); font-weight: 700; }
        .section-description { margin: 0; color: var(--ink-muted); font-size: 0.95rem; }
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
            --success-bg: #e4f7ec;
            --success-text: #1c6b3c;
        }
    </style>
</body>
</html>
