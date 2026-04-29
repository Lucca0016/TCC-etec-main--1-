<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Livros | Administrador</title>
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
            --success-bg: #e4f7ec;
            --error-bg: #fdecec;
            --warning-bg: #fff3cd;
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
        }

        .books-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .books-header h2 {
            font-size: 32px;
            font-weight: 700;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .books-header h2::before {
            content: '📚';
            font-size: 36px;
        }

        .btn-add-book {
            background: linear-gradient(135deg, var(--accent) 0%, #1555d8 100%);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(31, 111, 235, 0.3);
        }

        .btn-add-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(31, 111, 235, 0.4);
        }

        .alert-container {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
            flex: 1;
            min-width: 300px;
        }

        .alert-success {
            background: var(--success-bg);
            color: #10b981;
            border-left: 4px solid #10b981;
        }

        .alert-error {
            background: var(--error-bg);
            color: #ef4444;
            border-left: 4px solid #ef4444;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .book-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            display: flex;
            flex-direction: column;
        }

        .book-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(31, 111, 235, 0.15);
        }

        .book-cover-container {
            position: relative;
            width: 100%;
            aspect-ratio: 3/4;
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .book-cover-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .book-cover-placeholder {
            font-size: 48px;
            opacity: 0.3;
        }

        .book-card-info {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .book-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.4;
            word-break: break-word;
        }

        .book-author {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .book-isbn {
            font-size: 11px;
            color: #9ca3af;
            font-family: 'Courier New', monospace;
        }

        .book-status {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 8px;
            border-top: 1px solid var(--border-light);
            margin-top: auto;
        }

        .copies-info {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .availability-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .availability-badge.available {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .availability-badge.unavailable {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .book-actions {
            display: flex;
            gap: 6px;
            margin-top: 8px;
        }

        .btn-action {
            flex: 1;
            padding: 6px 10px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }

        .btn-edit {
            background: var(--accent);
            color: white;
        }

        .btn-edit:hover {
            background: #1555d8;
            transform: translateY(-1px);
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 16px;
        }

        .empty-state h2 {
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .empty-state p {
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .pagination-btn {
            padding: 10px 12px;
            border: 1px solid var(--border-light);
            background: white;
            color: var(--text-primary);
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .pagination-btn:hover:not(:disabled) {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        .pagination-btn.active {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
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
            cursor: pointer;
            color: var(--text-secondary);
            transition: all 0.2s ease;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-btn:hover {
            color: var(--text-primary);
            transform: rotate(90deg);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .form-input,
        .form-textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-light);
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(31, 111, 235, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .upload-zone {
            border: 2px dashed var(--accent);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: rgba(31, 111, 235, 0.05);
        }

        .upload-zone:hover {
            background: rgba(31, 111, 235, 0.1);
            border-color: #1555d8;
        }

        .upload-zone.dragover {
            background: rgba(31, 111, 235, 0.15);
            border-color: #1555d8;
        }

        .upload-zone-icon {
            font-size: 32px;
            margin-bottom: 8px;
        }

        .upload-zone-text {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .upload-zone-file-input {
            display: none;
        }

        .preview-image {
            margin-top: 12px;
            max-width: 100%;
            max-height: 200px;
            border-radius: 6px;
            display: none;
        }

        .preview-image.show {
            display: block;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 24px;
        }

        .btn-submit {
            flex: 1;
            padding: 12px 16px;
            background: linear-gradient(135deg, var(--accent) 0%, #1555d8 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(31, 111, 235, 0.3);
        }

        .btn-cancel {
            flex: 1;
            padding: 12px 16px;
            background: var(--border-light);
            color: var(--text-primary);
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-cancel:hover {
            background: #d1d5db;
        }

        @media (max-width: 768px) {
            .books-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .books-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 16px;
            }

            .modal-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container" style="padding: 30px 20px;">
        <!-- Header -->
        <div class="books-header">
            <h2>Gerenciar Livros</h2>
            <button class="btn-add-book" onclick="openAddModal()">+ Adicionar Livro</button>
        </div>

        <!-- Alerts -->
        <?php if (!empty($success)): ?>
            <div class="alert-container">
                <div class="alert alert-success">✓ <?php echo htmlspecialchars($success); ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert-container">
                <div class="alert alert-error">✕ <?php echo htmlspecialchars($error); ?></div>
            </div>
        <?php endif; ?>

        <!-- Books Grid -->
        <?php if (!empty($livros)): ?>
            <div class="books-grid">
                <?php foreach ($livros as $livro): ?>
                    <div class="book-card">
                        <div class="book-cover-container">
                            <?php if (!empty($livro['imagem_capa'])): ?>
                                <img src="<?php echo htmlspecialchars($livro['imagem_capa']); ?>" alt="<?php echo htmlspecialchars($livro['titulo']); ?>" class="book-cover-img">
                            <?php else: ?>
                                <div class="book-cover-placeholder">📖</div>
                            <?php endif; ?>
                        </div>

                        <div class="book-card-info">
                            <div class="book-title"><?php echo htmlspecialchars($livro['titulo']); ?></div>
                            <div class="book-author"><?php echo htmlspecialchars($livro['autor']); ?></div>
                            <?php if (!empty($livro['isbn'])): ?>
                                <div class="book-isbn"><?php echo htmlspecialchars($livro['isbn']); ?></div>
                            <?php endif; ?>

                            <div class="book-status">
                                <div class="copies-info">
                                    <?php echo (int)($livro['copias_disponiveis'] ?? 0); ?>/<?php echo (int)($livro['copias_totais'] ?? 0); ?>
                                </div>
                                <span class="availability-badge <?php echo ((int)($livro['disponivel'] ?? 0) ? 'available' : 'unavailable'); ?>">
                                    <?php echo ((int)($livro['disponivel'] ?? 0) ? '● Disponível' : '● Indisponível'); ?>
                                </span>
                            </div>

                            <div class="book-actions">
                                <button class="btn-action btn-edit" onclick="openEditModal(<?php echo $livro['id']; ?>, <?php echo htmlspecialchars(json_encode($livro), ENT_QUOTES, 'UTF-8'); ?>)">
                                    Editar
                                </button>
                                <button class="btn-action btn-delete" onclick="deleteBook(<?php echo $livro['id']; ?>)">
                                    Deletar
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ((int)($total_paginas ?? 1) > 1): ?>
                <div class="pagination-container">
                    <?php if ((int)($pagina_atual ?? 1) > 1): ?>
                        <a href="?page=1" class="pagination-btn">« Primeira</a>
                        <a href="?page=<?php echo (int)($pagina_atual ?? 1) - 1; ?>" class="pagination-btn">‹ Anterior</a>
                    <?php endif; ?>

                    <?php for ($i = max(1, (int)($pagina_atual ?? 1) - 2); $i <= min((int)($total_paginas ?? 1), (int)($pagina_atual ?? 1) + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="pagination-btn <?php echo $i === (int)($pagina_atual ?? 1) ? 'active' : ''; ?>">
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
                <p>Comece adicionando um novo livro à biblioteca</p>
                <button class="btn-add-book" onclick="openAddModal()">+ Adicionar Primeiro Livro</button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Add Book Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Adicionar Novo Livro</h2>
                <button class="close-btn" onclick="closeAddModal()">&times;</button>
            </div>

            <form id="addForm" method="POST" action="/TCC-etec/admin/livro/criar" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Título *</label>
                    <input type="text" name="titulo" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Autor *</label>
                    <input type="text" name="autor" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Editora</label>
                    <input type="text" name="editora" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-input" placeholder="978-8535930611">
                </div>

                <div class="form-group">
                    <label class="form-label">Link do Livro (PDF ou URL) 📎</label>
                    <input type="url" name="link_pdf" class="form-input" placeholder="https://exemplo.com/livro.pdf ou /uploads/livros/livro.pdf">
                    <small style="color: #6b7280; font-size: 12px; margin-top: 5px; display: block;">Cole o link completo do PDF ou a URL onde o livro pode ser acessado</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Quantidade de Cópias *</label>
                    <input type="number" name="copias" class="form-input" min="1" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Capa do Livro</label>
                    <div class="upload-zone" id="uploadZoneAdd" ondrop="handleDropAdd(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                        <div class="upload-zone-icon">📤</div>
                        <div class="upload-zone-text">Arraste a imagem ou clique para selecionar</div>
                        <input type="file" name="imagem_capa" class="upload-zone-file-input" id="fileInputAdd" accept="image/*" onchange="previewImageAdd(event)">
                    </div>
                    <img id="previewAdd" class="preview-image">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Criar Livro</button>
                    <button type="button" class="btn-cancel" onclick="closeAddModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Book Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Editar Livro</h2>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>

            <form id="editForm" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Título *</label>
                    <input type="text" name="titulo" id="editTitulo" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Autor *</label>
                    <input type="text" name="autor" id="editAutor" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Editora</label>
                    <input type="text" name="editora" id="editEditora" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" id="editIsbn" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Link do Livro (PDF ou URL) 📎</label>
                    <input type="url" name="link_pdf" id="editLinkPdf" class="form-input" placeholder="https://exemplo.com/livro.pdf ou /uploads/livros/livro.pdf">
                    <small style="color: #6b7280; font-size: 12px; margin-top: 5px; display: block;">Cole o link completo do PDF ou a URL onde o livro pode ser acessado</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Quantidade de Cópias *</label>
                    <input type="number" name="copias" id="editCopias" class="form-input" min="1" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Cópias Disponíveis</label>
                    <input type="number" name="copias_disponiveis" id="editCopiasDisponiveis" class="form-input" min="0" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Capa do Livro</label>
                    <div class="upload-zone" id="uploadZoneEdit" ondrop="handleDropEdit(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                        <div class="upload-zone-icon">📤</div>
                        <div class="upload-zone-text">Arraste a imagem ou clique para selecionar</div>
                        <input type="file" name="imagem_capa" class="upload-zone-file-input" id="fileInputEdit" accept="image/*" onchange="previewImageEdit(event)">
                    </div>
                    <img id="previewEdit" class="preview-image">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Salvar Alterações</button>
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Management
        function openAddModal() {
            document.getElementById('addModal').classList.add('active');
            document.getElementById('addForm').reset();
            document.getElementById('previewAdd').classList.remove('show');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.remove('active');
        }

        function openEditModal(livroId, livro) {
            document.getElementById('editTitulo').value = livro.titulo;
            document.getElementById('editAutor').value = livro.autor;
            document.getElementById('editEditora').value = livro.editora || '';
            document.getElementById('editIsbn').value = livro.isbn || '';
            document.getElementById('editLinkPdf').value = livro.link_pdf || '';
            document.getElementById('editCopias').value = livro.copias_totais;
            document.getElementById('editCopiasDisponiveis').value = livro.copias_disponiveis;

            if (livro.imagem_capa) {
                document.getElementById('previewEdit').src = livro.imagem_capa;
                document.getElementById('previewEdit').classList.add('show');
            } else {
                document.getElementById('previewEdit').classList.remove('show');
            }

            document.getElementById('editForm').action = '/TCC-etec/admin/livro/' + livroId + '/editar';
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // Close modals on background click
        window.onclick = function(event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');

            if (event.target === addModal) {
                closeAddModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
        }

        // Drag & Drop Handler
        function handleDragOver(event) {
            event.preventDefault();
            event.currentTarget.classList.add('dragover');
        }

        function handleDragLeave(event) {
            event.currentTarget.classList.remove('dragover');
        }

        function handleDropAdd(event) {
            event.preventDefault();
            event.currentTarget.classList.remove('dragover');

            const files = event.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('fileInputAdd').files = files;
                previewImageAdd({target: {files: files}});
            }
        }

        function handleDropEdit(event) {
            event.preventDefault();
            event.currentTarget.classList.remove('dragover');

            const files = event.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('fileInputEdit').files = files;
                previewImageEdit({target: {files: files}});
            }
        }

        // File Upload Zone Click
        document.addEventListener('DOMContentLoaded', function() {
            const uploadZoneAdd = document.getElementById('uploadZoneAdd');
            const uploadZoneEdit = document.getElementById('uploadZoneEdit');

            uploadZoneAdd.addEventListener('click', () => document.getElementById('fileInputAdd').click());
            uploadZoneEdit.addEventListener('click', () => document.getElementById('fileInputEdit').click());
        });

        // Image Preview
        function previewImageAdd(event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('previewAdd');
                    preview.src = e.target.result;
                    preview.classList.add('show');
                };
                reader.readAsDataURL(file);
            }
        }

        function previewImageEdit(event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('previewEdit');
                    preview.src = e.target.result;
                    preview.classList.add('show');
                };
                reader.readAsDataURL(file);
            }
        }

        // Delete Book
        function deleteBook(livroId) {
            if (confirm('Tem certeza que deseja deletar este livro? Esta ação não pode ser desfeita.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/TCC-etec/admin/livro/' + livroId + '/deletar';
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.3s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 4000);
            });
        });
    </script>
</body>
</html>