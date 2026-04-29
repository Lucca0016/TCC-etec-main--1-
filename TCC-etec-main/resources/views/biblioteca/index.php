<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca FETEL</title>
    <meta name="description" content="Explore livros, materiais didáticos e conteúdos acadêmicos na Biblioteca FETEL.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/TCC-etec/css/style.css">
    <style>
        .biblioteca-page {
            background: #f8fbff;
            color: #0f172a;
        }

        .biblioteca-page .hero {
            position: relative;
            padding: 100px 0 70px;
            background: radial-gradient(circle at 15% 20%, rgba(59,130,246,0.28), transparent 24%),
                        radial-gradient(circle at 85% 10%, rgba(99,102,241,0.14), transparent 22%),
                        linear-gradient(180deg, #0f172a 0%, #16213a 100%);
            overflow: hidden;
            min-height: 520px;
        }

        .biblioteca-page .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 80% 30%, rgba(56,189,248,0.12), transparent 0 45%),
                        radial-gradient(circle at 20% 70%, rgba(147,197,253,0.16), transparent 0 40%);
            pointer-events: none;
        }

        .biblioteca-page .hero-inner {
            position: relative;
            z-index: 2;
            max-width: 900px;
        }

        .biblioteca-page .hero h1 {
            font-size: clamp(3rem, 5vw, 4.5rem);
            font-weight: 800;
            line-height: 0.98;
            letter-spacing: -0.04em;
            margin-bottom: 18px;
            color: #f8fafc;
        }

        .biblioteca-page .lead {
            margin-bottom: 34px;
            color: rgba(248,250,252,0.88);
            font-size: 1.05rem;
            max-width: 720px;
            line-height: 1.8;
        }

        .biblioteca-page .hero-cta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
        }

        .biblioteca-page .search-input {
            width: min(100%, 600px);
            padding: 18px 18px 18px 48px;
            border-radius: 999px;
            border: 1px solid rgba(248,250,252,0.32);
            background: rgba(255,255,255,0.12);
            color: #f8fafc;
            box-shadow: 0 24px 80px rgba(15,23,42,0.18);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M21 21l-4.35-4.35'/%3E%3Ccircle cx='10' cy='10' r='6.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 16px center;
            background-size: 18px 18px;
            transition: transform .28s ease, border-color .28s ease, background .28s ease;
        }

        .biblioteca-page .search-input::placeholder {
            color: rgba(248,250,252,0.74);
        }

        .biblioteca-page .search-input:focus {
            outline: none;
            transform: translateY(-1px);
            border-color: rgba(59,130,246,0.65);
            background: rgba(255,255,255,0.2);
        }

        .biblioteca-page .hero-cta .btn.primary {
            min-width: 180px;
            padding: 18px 28px;
            border-radius: 999px;
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            border: none;
            color: #fff;
            box-shadow: 0 24px 72px rgba(59,130,246,0.3);
            transition: transform .28s ease, box-shadow .28s ease, opacity .28s ease;
        }

        .biblioteca-page .hero-cta .btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 30px 90px rgba(59,130,246,0.35);
        }

        .biblioteca-page .section {
            padding: 78px 0;
        }

        .biblioteca-page .section.alt {
            background: transparent;
        }

        .biblioteca-page .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .biblioteca-page .section-header h2 {
            font-size: clamp(2rem, 2.7vw, 2.7rem);
            color: #0f172a;
            margin: 0;
        }

        .biblioteca-page .section-sub {
            color: #475569;
            font-size: 1rem;
            max-width: 680px;
        }

        .biblioteca-page .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(220px, 1fr));
            gap: 24px;
            margin-top: 30px;
        }

        .biblioteca-page .stat-card {
            padding: 30px 28px;
            border-radius: 28px;
            background: linear-gradient(180deg, #ffffff, #f8fbff);
            border: 1px solid rgba(99,102,241,0.14);
            box-shadow: 0 22px 55px rgba(15,23,42,0.08);
            transition: transform .28s ease, box-shadow .28s ease;
        }

        .biblioteca-page .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 30px 75px rgba(15,23,42,0.12);
        }

        .biblioteca-page .stat-card small {
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .biblioteca-page .stat-card strong {
            display: block;
            margin-top: 14px;
            font-size: 2.6rem;
            color: #1d4ed8;
        }

        .biblioteca-page .panel {
            padding: 40px;
            border-radius: 30px;
            border: 1px solid rgba(148,163,184,0.18);
            background: linear-gradient(180deg, #ffffff 0%, #eff6ff 100%);
            box-shadow: 0 28px 80px rgba(15,23,42,0.08);
        }

        .biblioteca-page .panel-header h2 {
            font-size: 1.75rem;
            margin-bottom: 8px;
        }

        .biblioteca-page .filters-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(240px, 1fr));
            gap: 22px;
            margin-top: 28px;
        }

        .biblioteca-page .filters-grid label {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 20px;
            border-radius: 24px;
            background: #ffffff;
            border: 1px solid rgba(59,130,246,0.16);
            color: #0f172a;
            box-shadow: 0 16px 32px rgba(15,23,42,0.06);
        }

        .biblioteca-page .filters-grid input,
        .biblioteca-page .filters-grid select {
            width: 100%;
            border: none;
            background: #f8fbff;
            padding: 16px 14px;
            border-radius: 16px;
            color: #0f172a;
            font-size: 1rem;
            transition: box-shadow .28s ease, transform .28s ease;
        }

        .biblioteca-page .filters-grid input:focus,
        .biblioteca-page .filters-grid select:focus {
            outline: none;
            box-shadow: 0 0 0 5px rgba(59,130,246,0.1);
            transform: translateY(-1px);
        }

        .biblioteca-page .filters-grid select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23647589'%3E%3Cpath d='M5.23 7.21a.75.75 0 011.06.02L10 11.22l3.71-3.99a.75.75 0 111.08 1.04l-4.25 4.57a.75.75 0 01-1.08 0L5.25 8.27a.75.75 0 01-.02-1.06z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px 16px;
        }

        .biblioteca-page .form-actions {
            justify-content: flex-end;
            display: flex;
            margin-top: 8px;
        }

        .biblioteca-page .btn.ghost {
            border-color: rgba(59,130,246,0.24);
            color: #1d4ed8;
            background: rgba(59,130,246,0.06);
        }

        .biblioteca-page .btn.ghost:hover {
            background: rgba(59,130,246,0.12);
        }

        .biblioteca-page .cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 28px;
            margin-top: 36px;
        }

        .biblioteca-page .book-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 22px;
            padding: 28px;
            border-radius: 28px;
            border: 1px solid rgba(148,163,184,0.18);
            background: linear-gradient(180deg, #ffffff, #f8fbff);
            box-shadow: 0 35px 90px rgba(15,23,42,0.1);
            transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
        }

        .biblioteca-page .book-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 40px 110px rgba(15,23,42,0.18);
            border-color: rgba(59,130,246,0.32);
        }

        .biblioteca-page .book-card-header {
            display: flex;
            gap: 18px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .biblioteca-page .book-cover {
            width: 140px;
            min-height: 220px;
            border-radius: 24px;
            background: linear-gradient(180deg, rgba(59,130,246,0.14), rgba(148,163,184,0.08));
            box-shadow: 0 18px 40px rgba(15,23,42,0.08);
            border: none;
        }

        .biblioteca-page .book-info {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 0;
        }

        .biblioteca-page .book-info h3 {
            margin: 0;
            font-size: 1.28rem;
            line-height: 1.25;
            font-weight: 800;
            letter-spacing: -0.02em;
            max-width: 100%;
            color: #0f172a;
        }

        .biblioteca-page .book-info h3 {
            display: block;
            word-break: break-word;
        }

        .biblioteca-page .book-info p {
            margin: 0;
            color: #475569;
            font-size: 0.96rem;
        }

        .biblioteca-page .book-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .biblioteca-page .book-card-footer strong {
            font-size: 1.5rem;
            color: #0f172a;
        }

        .biblioteca-page .book-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .biblioteca-page .modal-backdrop {
            background: rgba(15,23,42,0.84);
            backdrop-filter: blur(4px);
        }

        .biblioteca-page .modal-panel {
            border-radius: 28px;
            border: 1px solid rgba(148,163,184,0.18);
            box-shadow: 0 45px 120px rgba(15,23,42,0.35);
            overflow: hidden;
            animation: modal-pop .32s ease-out;
        }

        .biblioteca-page .modal-grid {
            grid-template-columns: 380px 1fr;
            min-height: 420px;
        }

        .biblioteca-page .modal-cover {
            min-height: 100%;
            background-size: cover;
            background-position: center;
        }

        .biblioteca-page .modal-content {
            padding: 42px;
        }

        .biblioteca-page .modal-content h2 {
            margin-top: 0;
            margin-bottom: 18px;
            color: #0f172a;
            font-size: clamp(2rem, 2.2vw, 2.4rem);
        }

        .biblioteca-page .modal-content p,
        .biblioteca-page .modal-meta span {
            color: #475569;
        }

        .biblioteca-page .modal-meta {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
            margin: 24px 0;
        }

        .biblioteca-page .modal-actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .biblioteca-page .modal-close {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #0f172a;
            color: #f8fafc;
            box-shadow: 0 18px 40px rgba(15,23,42,0.2);
            transition: transform .24s ease, background .24s ease;
        }

        .biblioteca-page .modal-close:hover {
            transform: translateY(-1px);
            background: #2563eb;
        }

        .biblioteca-page .btn.primary:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .biblioteca-page .paginacao {
            margin-top: 42px;
            justify-content: center;
            gap: 16px;
        }

        .biblioteca-page .paginacao a.btn {
            min-width: 140px;
        }

        .biblioteca-page .estado-vazio {
            background: linear-gradient(180deg, #ffffff, #f8fafc);
            color: #334155;
            border: 1px solid rgba(148,163,184,0.2);
            box-shadow: 0 20px 50px rgba(15,23,42,0.06);
            padding: 56px 32px;
            border-radius: 24px;
        }

        .biblioteca-page .estado-vazio h2 {
            margin-bottom: 12px;
            color: #0f172a;
        }

        .biblioteca-page .alert {
            background: #fef3f4;
            border-color: rgba(239,68,68,0.22);
        }

        .biblioteca-page .site-footer {
            background: #f8fafc;
            border-top: 1px solid rgba(148,163,184,0.18);
        }

        .biblioteca-page .footer-inner {
            border-top: 1px solid rgba(148,163,184,0.16);
            padding-top: 18px;
        }

        @keyframes modal-pop {
            from {
                opacity: 0;
                transform: translateY(18px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (max-width: 1120px) {
            .biblioteca-page .stats-grid {
                grid-template-columns: repeat(2, minmax(180px, 1fr));
            }

            .biblioteca-page .cards {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .biblioteca-page .modal-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 860px) {
            .biblioteca-page .filters-grid {
                grid-template-columns: 1fr;
            }

            .biblioteca-page .book-card-header {
                flex-direction: column;
            }

            .biblioteca-page .book-cover {
                width: 100%;
                min-height: 260px;
            }
        }

        @media (max-width: 720px) {
            .biblioteca-page .hero {
                padding: 72px 0 48px;
            }

            .biblioteca-page .hero-cta {
                flex-direction: column;
            }

            .biblioteca-page .cards {
                grid-template-columns: 1fr;
            }

            .biblioteca-page .stats-grid {
                grid-template-columns: 1fr;
            }

            .biblioteca-page .panel {
                padding: 24px;
            }

            .biblioteca-page .modal-panel {
                width: calc(100% - 32px);
            }
        }
    </style>
</head>
<body class="biblioteca-page">
    <header class="site-header">
        <div class="container header-inner">
            <a class="logo" href="/TCC-etec/">
                <img src="/TCC-etec/img/fetel_sem_fundo.png" alt="FETEL" style="height:96px; width:auto; display:inline-block; vertical-align:middle;">
                <span class="brand"></span>
            </a>

            <button class="nav-toggle" id="navToggle" aria-label="Abrir menu" aria-expanded="false">
                <span class="hamburger"></span>
            </button>

            <nav class="nav" id="main-nav">
                <ul class="nav-list">
                    <li><a href="/TCC-etec/cursos">Cursos</a></li>
                    <li><a href="/TCC-etec/secretaria">Secretaria</a></li>
                    <li><a href="/TCC-etec/vestibular">Vestibular</a></li>
                    <li><a href="/TCC-etec/contato">Contato</a></li>
                    <li><a href="/TCC-etec/login" class="btn ghost">Perfil</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero" id="biblioteca">
            <div class="container hero-inner">
                <h1>Biblioteca FETEL</h1>
                <p class="lead">Explore livros, materiais didáticos e conteúdos acadêmicos.</p>
                <form class="hero-cta" action="/TCC-etec/biblioteca" method="get">
                    <input class="search-input" type="text" name="search" placeholder="Buscar por título, autor ou categoria" value="<?= htmlspecialchars($busca ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    <button type="submit" class="btn primary">Pesquisar</button>
                </form>
            </div>
        </section>

        <section class="section alt">
            <div class="container">
                <div class="section-header">
                    <div>
                        <h2>Visão geral do acervo</h2>
                        <p class="section-sub">Acompanhe estatísticas de livros, disponibilidade e solicitações ativas.</p>
                    </div>
                </div>

                <div class="stats-grid">
                    <article class="card stat-card">
                        <small>Total de livros</small>
                        <strong><?= (int)($total_livros ?? 0) ?></strong>
                    </article>
                    <article class="card stat-card">
                        <small>Livros disponíveis</small>
                        <strong><?= (int)($total_disponiveis ?? 0) ?></strong>
                    </article>
                    <article class="card stat-card">
                        <small>Empréstimos ativos</small>
                        <strong><?= (int)($emprestimos_ativos_count ?? 0) ?></strong>
                    </article>
                    <article class="card stat-card">
                        <small>Categorias</small>
                        <strong><?= (int)($categorias_count ?? 0) ?></strong>
                    </article>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <?php if (!empty($erro)): ?>
                    <div class="alert alert-error">
                        <strong>Erro:</strong> <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <h2>Filtros profissionais</h2>
                            <p class="section-sub">Refine sua pesquisa por categoria, curso, autor, ano e disponibilidade.</p>
                        </div>
                        <a href="/TCC-etec/biblioteca" class="btn ghost">Limpar filtros</a>
                    </div>

                    <form class="filters-grid" action="/TCC-etec/biblioteca" method="get">
                        <label>
                            Pesquisa
                            <input type="text" name="search" placeholder="Título, autor ou categoria" value="<?= htmlspecialchars($filtros['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </label>

                        <label>
                            Autor
                            <input type="text" name="autor" placeholder="Nome do autor" value="<?= htmlspecialchars($filtros['autor'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </label>

                        <label>
                            Categoria
                            <select name="categoria">
                                <option value="">Todas as categorias</option>
                                <?php foreach (($categorias ?? []) as $categoria): ?>
                                    <option value="<?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?>" <?= ($categoria === ($filtros['categoria'] ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label>
                            Curso relacionado
                            <select name="curso">
                                <option value="">Todos os cursos</option>
                                <?php foreach (($cursos ?? []) as $curso): ?>
                                    <option value="<?= htmlspecialchars($curso, ENT_QUOTES, 'UTF-8') ?>" <?= ($curso === ($filtros['curso'] ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($curso, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label>
                            Ano
                            <select name="ano">
                                <option value="">Todos os anos</option>
                                <?php foreach (($anos ?? []) as $ano): ?>
                                    <option value="<?= htmlspecialchars($ano, ENT_QUOTES, 'UTF-8') ?>" <?= ((string)$ano === ($filtros['ano'] ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($ano, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label>
                            Disponibilidade
                            <select name="disponibilidade">
                                <option value="">Qualquer status</option>
                                <option value="disponiveis" <?= (($filtros['disponibilidade'] ?? '') === 'disponiveis') ? 'selected' : '' ?>>Disponíveis</option>
                                <option value="indisponiveis" <?= (($filtros['disponibilidade'] ?? '') === 'indisponiveis') ? 'selected' : '' ?>>Indisponíveis</option>
                            </select>
                        </label>

                        <div class="form-actions">
                            <button type="submit" class="btn primary">Aplicar filtros</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="section alt">
            <div class="container">
                <div class="section-header">
                    <div>
                        <h2>Livros em destaque</h2>
                        <p class="section-sub">Seleção atualizada com os materiais mais relevantes para o seu estudo.</p>
                    </div>
                    <?php if (!empty($total_livros)): ?>
                        <span class="badge-info"><?= (int)$total_livros ?> livros encontrados</span>
                    <?php endif; ?>
                </div>

                <?php if (empty($livros)): ?>
                    <div class="estado-vazio">
                        <h2>Nenhum livro encontrado</h2>
                        <p>Use os filtros acima para ajustar sua pesquisa ou confira o acervo mais tarde.</p>
                    </div>
                <?php else: ?>
                    <div class="cards">
                        <?php foreach ($livros as $livro): ?>
                            <?php
                                $livroId = (int)($livro['id'] ?? 0);
                                $titulo = htmlspecialchars($livro['titulo'] ?? 'Título indisponível', ENT_QUOTES, 'UTF-8');
                                $autor = htmlspecialchars($livro['autor'] ?? 'Autor não informado', ENT_QUOTES, 'UTF-8');
                                $categoria = htmlspecialchars($livro['categoria'] ?? ($livro['genero'] ?? 'Sem categoria'), ENT_QUOTES, 'UTF-8');
                                $curso = htmlspecialchars($livro['curso'] ?? 'Curso não informado', ENT_QUOTES, 'UTF-8');
                                $ano = htmlspecialchars($livro['ano_publicacao'] ?? $livro['ano'] ?? '—', ENT_QUOTES, 'UTF-8');
                                $copias = isset($livro['copias_disponiveis']) ? (int)$livro['copias_disponiveis'] : 0;
                                $disponivel = $copias > 0 || (isset($livro['disponivel']) && (int)$livro['disponivel'] === 1);
                                $statusTexto = $disponivel ? 'Disponível' : 'Indisponível';
                                $statusClasse = $disponivel ? 'badge-success' : 'badge-danger';
                                $cover = !empty($livro['imagem_capa']) ? htmlspecialchars($livro['imagem_capa'], ENT_QUOTES, 'UTF-8') : '';
                            ?>
                            <article class="card book-card">
                                <div class="book-card-header">
                                    <div class="book-cover" style="background-image: <?= $cover ? 'url(\'' . $cover . '\')' : 'linear-gradient(135deg, rgba(0,61,153,0.12), rgba(0,86,179,0.06))' ?>;"></div>
                                    <div class="book-info">
                                        <span class="badge <?= $statusClasse ?>"><?= $statusTexto ?></span>
                                        <h3><?= $titulo ?></h3>
                                        <p class="muted">Autor: <?= $autor ?></p>
                                        <p class="muted">Categoria: <?= $categoria ?></p>
                                        <p class="muted">Curso: <?= $curso ?></p>
                                        <p class="muted">Ano: <?= $ano ?></p>
                                    </div>
                                </div>
                                <div class="book-card-footer">
                                    <div>
                                        <p class="muted">Cópias disponíveis</p>
                                        <strong><?= $copias ?></strong>
                                    </div>
                                    <div class="book-actions">
                                        <button type="button" class="btn ghost btn-detalhes" data-livro-id="<?= $livroId ?>">Ver detalhes</button>
                                        <button type="button" class="btn primary btn-solicitar" data-livro-id="<?= $livroId ?>" <?= $disponivel ? '' : 'disabled' ?>>Solicitar empréstimo</button>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <?php if (!empty($total_paginas) && $total_paginas > 1): ?>
                        <div class="paginacao">
                            <?php $paginaAtual = max(1, (int)($pagina_atual ?? 1)); ?>
                            <?php $totalPaginas = max(1, (int)$total_paginas); ?>

                            <?php if ($paginaAtual > 1): ?>
                                <a href="/TCC-etec/biblioteca?search=<?= urlencode($busca ?? '') ?>&categoria=<?= urlencode($filtros['categoria'] ?? '') ?>&curso=<?= urlencode($filtros['curso'] ?? '') ?>&autor=<?= urlencode($filtros['autor'] ?? '') ?>&ano=<?= urlencode($filtros['ano'] ?? '') ?>&disponibilidade=<?= urlencode($filtros['disponibilidade'] ?? '') ?>&page=<?= $paginaAtual - 1 ?>" class="btn ghost">Anterior</a>
                            <?php endif; ?>
                            <span>Página <?= $paginaAtual ?> de <?= $totalPaginas ?></span>
                            <?php if ($paginaAtual < $totalPaginas): ?>
                                <a href="/TCC-etec/biblioteca?search=<?= urlencode($busca ?? '') ?>&categoria=<?= urlencode($filtros['categoria'] ?? '') ?>&curso=<?= urlencode($filtros['curso'] ?? '') ?>&autor=<?= urlencode($filtros['autor'] ?? '') ?>&ano=<?= urlencode($filtros['ano'] ?? '') ?>&disponibilidade=<?= urlencode($filtros['disponibilidade'] ?? '') ?>&page=<?= $paginaAtual + 1 ?>" class="btn primary">Próxima</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <div class="modal-backdrop hidden" id="bookModal" aria-hidden="true">
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <button type="button" class="modal-close" id="modalClose" aria-label="Fechar modal">×</button>
            <div class="modal-grid">
                <div class="modal-cover" id="modalCover"></div>
                <div class="modal-content">
                    <span class="badge badge-info" id="modalStatus"></span>
                    <h2 id="modalTitle">Título do livro</h2>
                    <p class="muted" id="modalAuthor"></p>
                    <p class="muted" id="modalCourse"></p>
                    <div class="modal-meta">
                        <span id="modalCategory"></span>
                        <span id="modalYear"></span>
                    </div>
                    <p id="modalDescription"></p>
                    <div class="modal-actions">
                        <button type="button" class="btn primary" id="modalLoanBtn">Solicitar empréstimo</button>
                        <a class="btn ghost hidden" href="/TCC-etec/login" id="modalLoginLink">Entrar para solicitar</a>
                    </div>
                    <div class="modal-message" id="modalMessage"></div>
                </div>
            </div>
        </div>
    </div>

    <footer class="site-footer">
        <div class="container footer-inner">
            <div class="contacts">
                <h4>Contatos</h4>
                <p>Rua Exemplo, 123 — Centro, São Paulo, SP</p>
                <p>Telefone: (11) 4000-0000 | E-mail: contato@fetel.edu.br</p>
            </div>

            <div class="social">
                <h4>Siga-nos</h4>
                <div class="social-links">
                    <a href="#" aria-label="Facebook" class="social-link">
                        <svg viewBox="0 0 24 24"><path fill="#0056b3" d="M22 12a10 10 0 10-11.5 9.9v-7h-2v-3h2v-2.3c0-2 1.2-3.1 3-3.1.9 0 1.8.2 1.8.2v2h-1c-1 0-1.3.6-1.3 1.2V12h2.3l-.4 3h-1.9v7A10 10 0 0022 12z"/></svg>
                    </a>
                    <a href="#" aria-label="Instagram" class="social-link">
                        <svg viewBox="0 0 24 24"><path fill="#0056b3" d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm5 6a4 4 0 100 8 4 4 0 000-8zM18 6a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="container copyright">
            <p>© <span id="year"></span> FETEL — Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('bookModal');
            const modalClose = document.getElementById('modalClose');
            const modalCover = document.getElementById('modalCover');
            const modalTitle = document.getElementById('modalTitle');
            const modalAuthor = document.getElementById('modalAuthor');
            const modalCourse = document.getElementById('modalCourse');
            const modalCategory = document.getElementById('modalCategory');
            const modalYear = document.getElementById('modalYear');
            const modalDescription = document.getElementById('modalDescription');
            const modalStatus = document.getElementById('modalStatus');
            const modalLoanBtn = document.getElementById('modalLoanBtn');
            const modalMessage = document.getElementById('modalMessage');
            const modalLoginLink = document.getElementById('modalLoginLink');
            let selectedBookId = 0;

            function openModal() {
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                modalMessage.textContent = '';
            }

            function setStatus(element, isAvailable, alreadyLoaned) {
                if (alreadyLoaned) {
                    element.textContent = 'Solicitado';
                    element.className = 'badge badge-warning';
                    return;
                }
                element.textContent = isAvailable ? 'Disponível' : 'Indisponível';
                element.className = isAvailable ? 'badge badge-success' : 'badge badge-danger';
            }

            async function fetchBookDetails(id) {
                const response = await fetch('/TCC-etec/biblioteca/detalhes/' + id, { headers: { 'Accept': 'application/json' } });
                return response.json();
            }

            async function loadBookModal(id) {
                try {
                    const data = await fetchBookDetails(id);
                    if (data.error) {
                        modalMessage.textContent = data.error;
                        openModal();
                        return;
                    }

                    const book = data.livro;
                    selectedBookId = id;

                    modalCover.style.backgroundImage = book.imagem_capa ? 'url(' + book.imagem_capa + ')' : 'linear-gradient(135deg, rgba(0,61,153,0.18), rgba(0,86,179,0.08))';
                    modalTitle.textContent = book.titulo || 'Título indisponível';
                    modalAuthor.textContent = 'Autor: ' + (book.autor || 'Não informado');
                    modalCourse.textContent = 'Curso: ' + (book.curso || 'Não informado');
                    modalCategory.textContent = 'Categoria: ' + (book.categoria || book.genero || 'Sem categoria');
                    modalYear.textContent = 'Ano: ' + (book.ano_publicacao || book.ano || 'Não informado');
                    modalDescription.textContent = book.descricao || book.sinopse || 'Descrição não disponível no momento.';
                    setStatus(modalStatus, book.disponivel, data.ja_emprestado);

                    if (!data.autenticado) {
                        modalLoanBtn.style.display = 'none';
                        modalLoginLink.style.display = 'inline-flex';
                    } else {
                        modalLoginLink.style.display = 'none';
                        modalLoanBtn.style.display = 'inline-flex';
                        modalLoanBtn.disabled = !book.disponivel || data.ja_emprestado;
                        modalLoanBtn.textContent = data.ja_emprestado ? 'Empréstimo solicitado' : 'Solicitar empréstimo';
                    }

                    openModal();
                } catch (error) {
                    modalMessage.textContent = 'Não foi possível carregar os detalhes do livro.';
                    openModal();
                }
            }

            document.querySelectorAll('.btn-detalhes, .btn-solicitar').forEach(button => {
                button.addEventListener('click', function () {
                    const bookId = this.getAttribute('data-livro-id');
                    if (!bookId) return;
                    loadBookModal(bookId);
                });
            });

            modalClose.addEventListener('click', closeModal);
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            modalLoanBtn.addEventListener('click', async function () {
                if (!selectedBookId) return;
                modalLoanBtn.disabled = true;
                modalLoanBtn.textContent = 'Enviando...';
                modalMessage.textContent = '';

                try {
                    const response = await fetch('/TCC-etec/biblioteca/solicitar', {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' },
                        body: new URLSearchParams({ livro_id: selectedBookId })
                    });
                    const data = await response.json();

                    if (data.success) {
                        modalMessage.textContent = data.message || 'Sua solicitação foi registrada.';
                        modalLoanBtn.disabled = true;
                        modalLoanBtn.textContent = 'Solicitação enviada';
                    } else {
                        modalMessage.textContent = data.error || 'Não foi possível solicitar o livro.';
                        modalLoanBtn.disabled = false;
                        modalLoanBtn.textContent = 'Solicitar empréstimo';
                    }
                } catch (error) {
                    modalMessage.textContent = 'Erro ao processar sua solicitação. Tente novamente.';
                    modalLoanBtn.disabled = false;
                    modalLoanBtn.textContent = 'Solicitar empréstimo';
                }
            });
        });
    </script>

    <script src="/TCC-etec/js/script.js" defer></script>
    <script src="/TCC-etec/public/js/auth.js"></script>
    <script>
        // Verifica se está logado
        (function () {
            const token = localStorage.getItem('access_token');
            if (!token) {
                window.location.href = '/TCC-etec/public/login.html';
            }
        })();
    </script>
</body>
</html>
