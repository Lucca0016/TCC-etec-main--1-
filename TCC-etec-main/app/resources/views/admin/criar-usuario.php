<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Criar Novo Usuário — Painel Admin FETEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/TCC-etec/public/css/admin.css" />
    <style>
        .form-container { max-width: 600px; margin: 40px auto; background: var(--card); padding: 32px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid var(--card-border); }
        .form-group { margin-bottom: 24px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--ink); font-size: 0.95rem; }
        .form-input { width: 100%; padding: 12px 14px; border: 1px solid var(--card-border); border-radius: 8px; font-family: 'Inter', system-ui; font-size: 0.95rem; transition: border-color 0.2s, box-shadow 0.2s; }
        .form-input:focus { outline: 0; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(31, 111, 235, 0.1); }
        .form-error { color: var(--error-text); font-size: 0.85rem; margin-top: 6px; }
        .form-actions { display: flex; gap: 12px; margin-top: 32px; }
        .btn { padding: 12px 20px; border-radius: 8px; border: 0; cursor: pointer; text-decoration: none; font-weight: 600; transition: all 0.2s; display: inline-block; }
        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(31, 111, 235, 0.2); }
        .btn-secondary { background: transparent; border: 1px solid var(--card-border); color: var(--ink); }
        .btn-secondary:hover { background: var(--canvas-muted); }
        .select-group { position: relative; }
        .select-group select { width: 100%; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; }
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
        <div class="form-container">
            <h2 style="margin-top: 0; margin-bottom: 6px; color: var(--ink);">Criar Novo Usuário</h2>
            <p style="margin: 0 0 24px; color: var(--ink-muted); font-size: 0.95rem;">Preencha os dados abaixo para adicionar um novo usuário ao sistema</p>

            <?php if (isset($_SESSION['form_errors']) && !empty($_SESSION['form_errors'])): ?>
                <div class="alert alert-error">
                    <strong>Erro ao validar:</strong>
                    <ul style="margin: 8px 0 0; padding-left: 20px;">
                        <?php foreach ($_SESSION['form_errors'] as $campo => $erro): ?>
                            <li><?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['form_errors']); ?>
            <?php endif; ?>

            <form method="POST" action="/TCC-etec/admin/usuarios/criar">
                <div class="form-group">
                    <label for="nome_completo" class="form-label">Nome Completo *</label>
                    <input 
                        type="text" 
                        id="nome_completo" 
                        name="nome_completo" 
                        class="form-input" 
                        placeholder="João Silva"
                        value="<?php echo htmlspecialchars($_SESSION['form_data']['nome_completo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    />
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email *</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="joao@fetel.edu.br"
                        value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    />
                </div>

                <div class="form-group">
                    <label for="senha" class="form-label">Senha *</label>
                    <input 
                        type="password" 
                        id="senha" 
                        name="senha" 
                        class="form-input" 
                        placeholder="Mínimo 8 caracteres"
                        minlength="8"
                        required
                    />
                    <small style="color: var(--ink-muted); margin-top: 6px; display: block;">Mínimo 8 caracteres com letras, números e caracteres especiais</small>
                </div>

                <div class="form-group">
                    <label for="papel" class="form-label">Perfil de Acesso *</label>
                    <div class="select-group">
                        <select id="papel" name="papel" class="form-input" required>
                            <option value="">Selecione um perfil...</option>
                            <option value="aluno" <?php echo ($_SESSION['form_data']['papel'] ?? '') === 'aluno' ? 'selected' : ''; ?>>Aluno</option>
                            <option value="professor" <?php echo ($_SESSION['form_data']['papel'] ?? '') === 'professor' ? 'selected' : ''; ?>>Professor</option>
                            <option value="secretaria" <?php echo ($_SESSION['form_data']['papel'] ?? '') === 'secretaria' ? 'selected' : ''; ?>>Secretaria</option>
                            <option value="admin" <?php echo ($_SESSION['form_data']['papel'] ?? '') === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Criar Usuário</button>
                    <a href="/TCC-etec/admin/usuarios" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>

            <?php 
                if (isset($_SESSION['form_data'])) {
                    unset($_SESSION['form_data']);
                }
            ?>
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
