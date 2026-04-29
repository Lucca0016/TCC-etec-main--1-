<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca | Acervo de Livros</title>
    <link rel="stylesheet" href="/TCC-etec/public/css/admin.css">
    <style>
        :root {
            --accent: #1f6feb;
            --ink: #0c1b2f;
            --canvas: #0d1b2a;
            --card: #f7f9fc;
            --text-primary: #0c1b2f;
            --text-secondary: #6b7280;
            --border-light: #e5e7eb;
            --success-color: #10b981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background: linear-gradient(135deg, var(--canvas) 0%, #1a2f4a 100%);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .biblioteca-header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .biblioteca-header h1 {
            font-size: 40px;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .biblioteca-header h1::before {
            content: '📚';
            font-size: 44px;
        }

        .biblioteca-header p {
            font-size: 16px;
            color: #d1d5db;
            max-width: 600px;
            margin: 0 auto;
        }

        .biblioteca-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            color: white;
        }

        .stat-card h3 {
            font-size: 12px;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
            color: var(--accent);
        }

        .search-container {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 12px 16px;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            font-size: 14px;
            background: white;
            color: var(--text-primary);
        }

        .search-input::placeholder {
            color: var(--text-secondary);
        }

        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .book-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .book-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(31, 111, 235, 0.2);
        }

        .book-cover-container {
            width: 100%;
            aspect-ratio: 3/4;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .book-cover-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .book-cover-placeholder {
            font-size: 60px;
            color: #d1d5db;
        }

        .book-info {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .book-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
            line-height: 1.3;
            display: -webkit-box;
            line-clamp: 2;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .book-author {
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .book-availability {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            font-size: 12px;
        }

        .availability-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 500;
        }

        .availability-badge.available {
            background: #d1fae5;
            color: var(--success-color);
        }

        .availability-badge.unavailable {
            background: #fee2e2;
            color: #dc2626;
        }

        .book-actions {
            display: flex;
            gap: 10px;
            margin-top: auto;
        }

        .btn-read {
            flex: 1;
            padding: 10px 12px;
            background: linear-gradient(135deg, var(--accent) 0%, #1555d8 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-read:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(31, 111, 235, 0.3);
        }

        .btn-read:disabled {
            background: #d1d5db;
            cursor: not-allowed;
            transform: none;
        }

        .btn-info {
            padding: 10px 12px;
            background: white;
            color: var(--text-primary);
            border: 1px solid var(--border-light);
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-info:hover {
            background: var(--card);
            border-color: var(--accent);
            color: var(--accent);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: white;
        }

        .empty-state-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.7;
        }

        .empty-state h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #d1d5db;
            font-size: 16px;
        }

        /* Modal para detalhes */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 500px;
            width: 100%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-light);
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: color 0.2s;
        }

        .close-btn:hover {
            color: var(--text-primary);
        }

        .modal-body {
            padding: 20px;
        }

        .detail-row {
            margin-bottom: 16px;
        }

        .detail-label {
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 14px;
            color: var(--text-primary);
        }

        @media (max-width: 768px) {
            .books-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 15px;
            }

            .biblioteca-header h1 {
                font-size: 28px;
            }

            .search-container {
                flex-direction: column;
            }

            .search-input {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="biblioteca-header">
            <h1>Biblioteca Digital</h1>
            <p>Acesso a um acervo completo de livros para sua formação acadêmica</p>
        </div>

        <!-- Statistics -->
        <div class="biblioteca-stats">
            <div class="stat-card">
                <h3>Total de Livros</h3>
                <div class="number"><?php echo $total_livros ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>Livros Disponíveis</h3>
                <div class="number"><?php echo $livros_disponiveis ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>Com Link</h3>
                <div class="number"><?php echo $livros_com_link ?? 0; ?></div>
            </div>
        </div>

        <!-- Search -->
        <div class="search-container">
            <input type="text" class="search-input" id="searchInput" placeholder="🔍 Buscar por título, autor ou ISBN...">
        </div>

        <!-- Books Grid -->
        <?php if (!empty($livros)): ?>
            <div class="books-grid" id="booksGrid">
                <?php foreach ($livros as $livro): ?>
                    <div class="book-card" data-title="<?php echo strtolower($livro['titulo']); ?>" data-author="<?php echo strtolower($livro['autor']); ?>">
                        <div class="book-cover-container">
                            <?php if (!empty($livro['imagem_capa'])): ?>
                                <img src="<?php echo htmlspecialchars($livro['imagem_capa']); ?>" alt="<?php echo htmlspecialchars($livro['titulo']); ?>" class="book-cover-img">
                            <?php else: ?>
                                <div class="book-cover-placeholder">📖</div>
                            <?php endif; ?>
                        </div>

                        <div class="book-info">
                            <div class="book-title"><?php echo htmlspecialchars($livro['titulo']); ?></div>
                            <div class="book-author">por <?php echo htmlspecialchars($livro['autor']); ?></div>

                            <div class="book-availability">
                                <span class="availability-badge <?php echo ((int)($livro['disponivel'] ?? 0) ? 'available' : 'unavailable'); ?>">
                                    <?php echo ((int)($livro['disponivel'] ?? 0) ? '● Disponível' : '● Indisponível'); ?>
                                </span>
                            </div>

                            <div class="book-actions">
                                <?php if (!empty($livro['link_pdf'])): ?>
                                    <a href="<?php echo htmlspecialchars($livro['link_pdf']); ?>" target="_blank" rel="noopener noreferrer" class="btn-read">
                                        📖 Ler Livro
                                    </a>
                                <?php else: ?>
                                    <button class="btn-read" disabled>Sem Link</button>
                                <?php endif; ?>
                                <button class="btn-info" onclick="showDetails(<?php echo htmlspecialchars(json_encode($livro), ENT_QUOTES, 'UTF-8'); ?>)">ℹ️ Info</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ((int)($total_paginas ?? 1) > 1): ?>
                <div class="pagination-container" style="text-align: center; margin-top: 40px;">
                    <?php if ((int)($pagina_atual ?? 1) > 1): ?>
                        <a href="?page=1" class="pagination-btn">« Primeira</a>
                        <a href="?page=<?php echo (int)($pagina_atual ?? 1) - 1; ?>" class="pagination-btn">‹ Anterior</a>
                    <?php endif; ?>

                    <?php for ($i = max(1, (int)($pagina_atual ?? 1) - 2); $i <= min((int)($total_paginas ?? 1), (int)($pagina_atual ?? 1) + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="pagination-btn <?php echo $i === (int)($pagina_atual ?? 1) ? 'active' : ''; ?>" style="margin: 0 5px;">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ((int)($pagina_atual ?? 1) < (int)($total_paginas ?? 1)): ?>
                        <a href="?page=<?php echo (int)($pagina_atual ?? 1) + 1; ?>" class="pagination-btn">Próxima ›</a>
                        <a href="?page=<?php echo (int)($total_paginas ?? 1); ?>" class="pagination-btn">Última »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">📚</div>
                <h2>Nenhum livro encontrado</h2>
                <p>A biblioteca está sendo preenchida. Volte em breve!</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Book Details Modal -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Detalhes do Livro</h2>
                <button class="close-btn" onclick="closeDetails()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Conteúdo dinamicamente preenchido -->
            </div>
        </div>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.book-card');

            cards.forEach(card => {
                const title = card.dataset.title;
                const author = card.dataset.author;
                const matches = title.includes(query) || author.includes(query);
                card.style.display = matches ? '' : 'none';
            });
        });

        // Show details modal
        function showDetails(livro) {
            const html = `
                <div class="detail-row">
                    <div class="detail-label">Título</div>
                    <div class="detail-value">${escapeHtml(livro.titulo)}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Autor</div>
                    <div class="detail-value">${escapeHtml(livro.autor)}</div>
                </div>
                ${livro.editora ? `
                    <div class="detail-row">
                        <div class="detail-label">Editora</div>
                        <div class="detail-value">${escapeHtml(livro.editora)}</div>
                    </div>
                ` : ''}
                ${livro.isbn ? `
                    <div class="detail-row">
                        <div class="detail-label">ISBN</div>
                        <div class="detail-value">${escapeHtml(livro.isbn)}</div>
                    </div>
                ` : ''}
                <div class="detail-row">
                    <div class="detail-label">Cópias Disponíveis</div>
                    <div class="detail-value">${livro.copias_disponiveis} de ${livro.copias_totais}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <span class="availability-badge ${livro.disponivel ? 'available' : 'unavailable'}">
                            ${livro.disponivel ? '● Disponível' : '● Indisponível'}
                        </span>
                    </div>
                </div>
                ${livro.link_pdf ? `
                    <div class="detail-row" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-light);">
                        <a href="${escapeHtml(livro.link_pdf)}" target="_blank" rel="noopener noreferrer" class="btn-read" style="width: 100%; text-decoration: none;">
                            📖 Acessar Livro Completo
                        </a>
                    </div>
                ` : ''}
            `;
            document.getElementById('modalBody').innerHTML = html;
            document.getElementById('detailsModal').classList.add('active');
        }

        function closeDetails() {
            document.getElementById('detailsModal').classList.remove('active');
        }

        // Close modal on background click
        window.onclick = function(event) {
            const modal = document.getElementById('detailsModal');
            if (event.target === modal) {
                closeDetails();
            }
        };

        // HTML escape helper
        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>
</body>
</html>
