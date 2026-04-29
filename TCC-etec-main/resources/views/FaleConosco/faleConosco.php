<?php
$resultado = $resultado ?? '';
$csrf = $csrf ?? '';
function safe(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fale Conosco — FETEL</title>
    <meta name="description" content="Entre em contato com a FETEL para dúvidas, reclamações, elogios ou problemas de login.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/TCC-etec/css/style.css">
    <style>
        .contact-page { padding: 72px 0; }
        .contact-header { display: grid; gap: 18px; }
        .contact-header h1 { margin: 0; font-size: clamp(2.4rem, 4vw, 3.4rem); color: #003d99; }
        .contact-header p { margin: 0; color: #475569; max-width: 680px; }
        .contact-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 34px; align-items: start; }
        .contact-card, .contact-panel { background: #fff; border-radius: 24px; border: 1px solid rgba(15,52,96,0.08); box-shadow: 0 24px 60px rgba(3,28,70,0.06); padding: 30px; }
        .contact-card h2, .contact-panel h2 { margin-top: 0; color: #08304e; }
        .contact-card p { color: #475569; line-height: 1.7; }
        .contact-panel form { display: grid; gap: 18px; }
        .field label { display: block; margin-bottom: 8px; font-weight: 600; color: #0b1f3e; }
        .field input, .field textarea, .field select { width: 100%; padding: 14px 16px; border: 1px solid #d1d5db; border-radius: 14px; font-size: 1rem; color: #0f172a; background: #fff; }
        .field textarea { min-height: 140px; resize: vertical; }
        .field-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .field-row.single { grid-template-columns: 1fr; }
        .alert { padding: 18px 20px; border-radius: 18px; font-weight: 600; }
        .alert.success { background: #e4f7ec; color: #1c6b3c; border: 1px solid #b7e3c6; }
        .alert.error { background: #fdecec; color: #9f1d1d; border: 1px solid #f5c2c2; }
        .form-footer { display: flex; flex-wrap: wrap; gap: 14px; justify-content: flex-start; }
        .form-footer .btn { min-width: 160px; }
        .section-sub { color: #475569; }
        .note { color: #475569; font-size: 0.95rem; }
        @media (max-width: 980px) { .contact-grid { grid-template-columns: 1fr; } }
        @media (max-width: 640px) { .field-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="logo" href="/TCC-etec/">
                <img src="/TCC-etec/img/fetel_sem_fundo.png" alt="FETEL" style="height:44px; width:auto;">
                <span class="brand">FETEL</span>
            </a>
            <button class="nav-toggle" id="navToggle" aria-label="Abrir menu" aria-expanded="false">
                <span class="hamburger"></span>
            </button>
            <nav class="nav" id="main-nav">
                <ul class="nav-list">
                    <li><a href="/TCC-etec/">Início</a></li>
                    <li><a href="/TCC-etec/cursos">Cursos</a></li>
                    <li><a href="/TCC-etec/vestibular">Vestibular</a></li>
                    <li><a href="/TCC-etec/biblioteca">Biblioteca</a></li>
                    <li><a class="active-link" href="/TCC-etec/faleconosco">Fale conosco</a></li>
                    <li><a href="/TCC-etec/login" class="btn ghost">Perfil</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <section class="section contact-page">
            <div class="container">
                <div class="contact-header">
                    <p class="section-sub">Fale com a equipe FETEL</p>
                    <h1>Feedback, dúvida ou sugestão</h1>
                    <p>Escolha um tipo de atendimento, conte sua experiência e receba um retorno rápido da nossa equipe.</p>
                </div>
                <div class="contact-grid">
                    <div class="contact-panel">
                        <?php if ($resultado !== ''): ?>
                            <div class="alert success" role="status"><?= safe($resultado) ?></div>
                        <?php endif; ?>
                        <form action="/TCC-etec/faleconosco" method="post">
                            <input type="hidden" name="_csrf" value="<?= safe($csrf) ?>">
                            <div class="field">
                                <label for="tipo">Tipo de contato</label>
                                <select id="tipo" name="tipo" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="duvida">Dúvida</option>
                                    <option value="reclamacao">Reclamação</option>
                                    <option value="elogio">Elogio</option>
                                    <option value="login">Problema de login</option>
                                    <option value="dados">Dados pessoais</option>
                                </select>
                            </div>
                            <div class="field-row">
                                <div class="field">
                                    <label for="nome">Nome completo</label>
                                    <input id="nome" name="nome" type="text" placeholder="Seu nome completo" required>
                                </div>
                                <div class="field">
                                    <label for="email">E-mail</label>
                                    <input id="email" name="email" type="email" placeholder="seu@email.com" required>
                                </div>
                            </div>
                            <div class="field">
                                <label for="mensagem">Mensagem</label>
                                <textarea id="mensagem" name="mensagem" rows="6" placeholder="Escreva aqui sua mensagem" required></textarea>
                            </div>
                            <div class="form-footer">
                                <button type="submit" class="btn primary">Enviar mensagem</button>
                            </div>
                        </form>
                    </div>
                    <div class="contact-card">
                        <h2>Precisa de ajuda imediata?</h2>
                        <p class="note">Nosso atendimento inclui:</p>
                        <ul style="margin:0; padding-left:18px; color:#475569; line-height:1.8;">
                            <li>Respostas para dúvidas sobre cursos</li>
                            <li>Tratamento de reclamações e elogios</li>
                            <li>Suporte para problemas de login</li>
                            <li>Acompanhamento de solicitações de dados pessoais</li>
                        </ul>
                        <p class="note" style="margin-top:18px;">Se preferir, envie um e-mail para <a href="mailto:contato@fetel.edu.br">contato@fetel.edu.br</a> ou use o telefone (11) 4000-0000.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer class="site-footer">
        <div class="container footer-inner">
            <div class="contacts">
                <h4>Contatos</h4>
                <p>Rua Exemplo, 123 — Centro, São Bernardo do Campo, SP</p>
                <p>Telefone: (11) 4000-0000 | E-mail: contato@fetel.edu.br</p>
            </div>
            <div class="social">
                <h4>Siga-nos</h4>
                <div class="social-links">
                    <a href="#" aria-label="Facebook" class="social-link">F</a>
                    <a href="#" aria-label="Instagram" class="social-link">I</a>
                </div>
            </div>
        </div>
        <div class="container copyright">
            <p>© <?= date('Y') ?> FETEL — Todos os direitos reservados.</p>
        </div>
    </footer>
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
