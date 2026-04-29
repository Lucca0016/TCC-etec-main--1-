<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato | FETEL</title>
    <meta name="description" content="Entre em contato com a FETEL para dúvidas sobre cursos, matrículas, vestibular e suporte ao aluno.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/TCC-etec/css/style.css">
    <style>
        .contact-hero {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            min-height: 520px;
        }

        .contact-hero .hero-copy {
            max-width: 620px;
        }

        .hero-visual {
            position: relative;
            display: flex;
            justify-content: center;
        }

        .hero-card {
            width: 100%;
            max-width: 520px;
            background: rgba(255,255,255,0.95);
            border: 1px solid rgba(3,28,70,0.08);
            border-radius: 28px;
            padding: 32px;
            box-shadow: 0 30px 60px rgba(3,28,70,0.08);
            display: grid;
            gap: 20px;
        }

        .hero-card h3 {
            margin: 0;
            color: #08304e;
            font-size: 1.35rem;
        }

        .hero-illustration {
            border-radius: 32px;
            background: linear-gradient(180deg, rgba(0,61,153,0.08), rgba(255,255,255,0.95));
            padding: 36px;
            display: grid;
            place-items: center;
            box-shadow: 0 30px 60px rgba(3,28,70,0.08);
        }

        .hero-illustration svg {
            width: 100%;
            max-width: 320px;
            color: #0056b3;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
            gap: 32px;
            align-items: start;
        }

        .contact-panel {
            padding: 36px;
        }

        .form-row {
            display: grid;
            gap: 18px;
        }

        .form-row.double {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .form-row.double .field {
            display: grid;
            gap: 10px;
        }

        .field label {
            font-weight: 600;
            color: #0b1f3e;
        }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            padding: 16px 18px;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            background: #fff;
            color: #0f172a;
            font-size: 1rem;
            outline: none;
        }

        .field textarea {
            min-height: 180px;
            resize: vertical;
        }

        .contact-quickcards {
            display: grid;
            gap: 16px;
        }

        .contact-card {
            padding: 24px;
            border-radius: 20px;
            background: #fff;
            border: 1px solid rgba(3,28,70,0.08);
            box-shadow: 0 24px 48px rgba(3,28,70,0.05);
            display: grid;
            gap: 14px;
        }

        .contact-card strong {
            display: block;
            color: #08304e;
            font-size: 0.95rem;
            margin-bottom: 4px;
        }

        .contact-card p {
            margin: 0;
            color: #475569;
            line-height: 1.7;
        }

        .sector-cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            margin-top: 16px;
        }

        .sector-card {
            padding: 28px;
            border-radius: 22px;
            background: #fff;
            border: 1px solid rgba(3,28,70,0.08);
            box-shadow: 0 28px 50px rgba(3,28,70,0.06);
        }

        .sector-card h4 {
            margin: 0 0 10px;
            font-size: 1.05rem;
            color: #08304e;
        }

        .faq-grid {
            display: grid;
            gap: 18px;
            margin-top: 18px;
        }

        .faq-item {
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid rgba(3,28,70,0.08);
            background: #fff;
            box-shadow: 0 24px 48px rgba(3,28,70,0.05);
        }

        .faq-question {
            background: #f8fbff;
            padding: 20px 24px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .faq-question h3 {
            margin: 0;
            font-size: 1rem;
            color: #08304e;
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.32s ease, padding 0.32s ease;
            padding: 0 24px;
        }

        .faq-answer.open {
            padding: 18px 24px 24px;
            max-height: 400px;
        }

        .faq-answer p {
            margin: 0;
            color: #475569;
            line-height: 1.8;
        }

        .cta-banner {
            padding: 48px 32px;
            border-radius: 28px;
            background: linear-gradient(135deg, #003d99, #0056d6);
            color: #fff;
            text-align: center;
            box-shadow: 0 32px 70px rgba(0,61,153,0.18);
            margin-top: 30px;
        }

        .cta-banner h2 {
            margin: 0 0 14px;
            font-size: 2rem;
            line-height: 1.1;
        }

        .cta-banner p {
            margin: 0 0 24px;
            color: rgba(255,255,255,0.88);
            font-size: 1.05rem;
        }

        .map-frame {
            border-radius: 24px;
            overflow: hidden;
            min-height: 360px;
            border: 1px solid rgba(3,28,70,0.1);
            box-shadow: 0 28px 60px rgba(3,28,70,0.08);
            margin-top: 18px;
        }

        .social-group {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .social-badge {
            background: #fff;
            border: 1px solid rgba(3,28,70,0.08);
            border-radius: 18px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 20px 40px rgba(3,28,70,0.06);
        }

        .social-badge span {
            font-size: 1.4rem;
        }

        .contact-success {
            display: none;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 18px 22px;
            border-radius: 16px;
            margin-bottom: 18px;
        }

        @media (max-width: 980px) {
            .contact-hero,
            .contact-grid,
            .sector-cards,
            .social-group {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .hero { padding: 60px 0 40px; }
            .hero-inner { gap: 30px; }
            .hero-visual { order: -1; }
        }
    </style>
</head>
<body>
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
                    <li><a href="/TCC-etec/vestibular">Vestibular</a></li>
                    <li><a href="/TCC-etec/biblioteca">Biblioteca</a></li>
                    <li><a href="/TCC-etec/secretaria">Secretaria</a></li>
                    <li><a class="active-link" href="/TCC-etec/contato">Contato</a></li>
                    <li><a href="/TCC-etec/login" class="btn ghost">Perfil</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container contact-hero">
                <div class="hero-copy">
                    <p class="section-sub">Contato institucional</p>
                    <h1>Entre em contato com a FETEL</h1>
                    <p class="lead">Estamos disponíveis para ajudar com cursos, matrículas, suporte acadêmico, biblioteca, vestibular e dúvidas administrativas.</p>
                    <a class="btn primary" href="#contato-form">Falar com a secretaria</a>
                    <div class="contact-counters" style="display:flex; gap:20px; flex-wrap:wrap; margin-top: 30px;">
                        <div style="background:#fff; border-radius:18px; padding:18px 22px; box-shadow:0 24px 48px rgba(3,28,70,0.08); border:1px solid rgba(3,28,70,0.08);">
                            <strong>+10 anos</strong>
                            <p style="margin:6px 0 0; color:#475569;">de experiência educacional</p>
                        </div>
                        <div style="background:#fff; border-radius:18px; padding:18px 22px; box-shadow:0 24px 48px rgba(3,28,70,0.08); border:1px solid rgba(3,28,70,0.08);">
                            <strong>+20 mil</strong>
                            <p style="margin:6px 0 0; color:#475569;">alunos atendidos</p>
                        </div>
                    </div>
                </div>

                <div class="hero-visual">
                    <div class="hero-card">
                        <h3>Atendimento personalizado</h3>
                        <p>Receba suporte rápido da nossa equipe para todas as etapas do seu processo acadêmico.</p>
                        <ul style="list-style:none; padding:0; margin:0; display:grid; gap:12px;">
                            <li style="display:flex; gap:12px; align-items:center;"><span style="font-size:1.4rem;">📩</span> Orientação de inscrição e vestibular</li>
                            <li style="display:flex; gap:12px; align-items:center;"><span style="font-size:1.4rem;">🎓</span> Dúvidas sobre cursos e horários</li>
                            <li style="display:flex; gap:12px; align-items:center;"><span style="font-size:1.4rem;">📚</span> Apoio à biblioteca e materiais</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="contato-form">
            <div class="container">
                <div class="section-header">
                    <h2>Solicite atendimento</h2>
                    <p class="section-sub">Preencha os dados abaixo para enviar sua mensagem à equipe FETEL.</p>
                </div>

                <div class="contact-grid">
                    <div class="card contact-panel">
                        <div class="contact-success" id="contactSuccess">Mensagem enviada com sucesso! Em breve retornaremos.</div>
                        <form id="contactForm" action="/TCC-etec/contato" method="post" class="form-row">
                            <div class="form-row double">
                                <div class="field">
                                    <label for="nome">Nome completo</label>
                                    <input id="nome" name="nome" type="text" placeholder="Seu nome" required>
                                </div>
                                <div class="field">
                                    <label for="email">E-mail</label>
                                    <input id="email" name="email" type="email" placeholder="seu@email.com" required>
                                </div>
                            </div>

                            <div class="form-row double">
                                <div class="field">
                                    <label for="telefone">Telefone</label>
                                    <input id="telefone" name="telefone" type="tel" placeholder="(11) 4000-0000">
                                </div>
                                <div class="field">
                                    <label for="tipo">Tipo de contato</label>
                                    <select id="tipo" name="tipo" required>
                                        <option value="">Selecione o setor</option>
                                        <option value="Cursos">Cursos</option>
                                        <option value="Vestibular">Vestibular</option>
                                        <option value="Secretaria">Secretaria</option>
                                        <option value="Biblioteca">Biblioteca</option>
                                        <option value="Suporte Técnico">Suporte Técnico</option>
                                    </select>
                                </div>
                            </div>

                            <div class="field">
                                <label for="assunto">Assunto</label>
                                <input id="assunto" name="assunto" type="text" placeholder="Motivo do contato" required>
                            </div>

                            <div class="field">
                                <label for="mensagem">Mensagem</label>
                                <textarea id="mensagem" name="mensagem" rows="6" placeholder="Escreva sua mensagem" required></textarea>
                            </div>

                            <div style="display:flex; flex-wrap:wrap; gap:14px; align-items:center; margin-top:8px;">
                                <button type="submit" class="btn primary">Enviar mensagem</button>
                                <button type="button" class="btn ghost" id="resetForm">Limpar formulário</button>
                            </div>
                        </form>
                    </div>

                    <aside class="contact-quickcards">
                        <div class="contact-card">
                            <strong>📞 Telefone</strong>
                            <p>(11) 4000-0000</p>
                        </div>
                        <div class="contact-card">
                            <strong>📧 E-mail</strong>
                            <p>contato@fetel.edu.br</p>
                        </div>
                        <div class="contact-card">
                            <strong>📍 Endereço</strong>
                            <p>Rua Exemplo, 123 — Centro, São Paulo, SP</p>
                        </div>
                        <div class="contact-card">
                            <strong>🕒 Horário</strong>
                            <p>Segunda a sexta, 08h às 18h</p>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <section class="section alt">
            <div class="container">
                <div class="section-header">
                    <h2>Contatos por setor</h2>
                    <p class="section-sub">Envie sua solicitação diretamente ao setor mais indicado.</p>
                </div>
                <div class="sector-cards">
                    <div class="sector-card">
                        <h4>Secretaria Acadêmica</h4>
                        <p>secretaria@fetel.edu.br</p>
                    </div>
                    <div class="sector-card">
                        <h4>Biblioteca</h4>
                        <p>biblioteca@fetel.edu.br</p>
                    </div>
                    <div class="sector-card">
                        <h4>Vestibular</h4>
                        <p>vestibular@fetel.edu.br</p>
                    </div>
                    <div class="sector-card">
                        <h4>Financeiro</h4>
                        <p>financeiro@fetel.edu.br</p>
                    </div>
                    <div class="sector-card">
                        <h4>Coordenação</h4>
                        <p>coordenacao@fetel.edu.br</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2>Perguntas frequentes</h2>
                    <p class="section-sub">Dúvidas comuns sobre matrícula, biblioteca, atendimento e senha.</p>
                </div>
                <div class="faq-grid">
                    <div class="faq-item">
                        <button type="button" class="faq-question" data-target="faq1">
                            <h3>Como faço minha matrícula?</h3>
                            <span>+</span>
                        </button>
                        <div class="faq-answer" id="faq1">
                            <p>Para realizar sua matrícula, verifique os documentos necessários e entre em contato com a secretaria acadêmica via e-mail ou formulário. Nosso time irá orientar o processo completo.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button type="button" class="faq-question" data-target="faq2">
                            <h3>Como acessar a biblioteca?</h3>
                            <span>+</span>
                        </button>
                        <div class="faq-answer" id="faq2">
                            <p>A biblioteca pode ser acessada pelos alunos e colaboradores presencialmente. Para dúvidas sobre empréstimos e consultas, contate o setor de biblioteca pelo e-mail informado.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button type="button" class="faq-question" data-target="faq3">
                            <h3>Como solicitar documentos?</h3>
                            <span>+</span>
                        </button>
                        <div class="faq-answer" id="faq3">
                            <p>Solicitações de documentos acadêmicos, históricos e declarações devem ser feitas diretamente pela secretaria ou pelo formulário de contato, indicando o tipo de documento desejado.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button type="button" class="faq-question" data-target="faq4">
                            <h3>Como falar com a secretaria?</h3>
                            <span>+</span>
                        </button>
                        <div class="faq-answer" id="faq4">
                            <p>Para falar com a secretaria, utilize o e-mail secretaria@fetel.edu.br ou envie sua mensagem pelo formulário. Caso prefira, deixe o telefone no formulário e nós retornaremos.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button type="button" class="faq-question" data-target="faq5">
                            <h3>Como redefinir senha?</h3>
                            <span>+</span>
                        </button>
                        <div class="faq-answer" id="faq5">
                            <p>Se você não consegue acessar sua conta, solicite redefinição de senha pela área de login ou entre em contato com suporte técnico pelo formulário ou e-mail.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section alt">
            <div class="container">
                <div class="section-header">
                    <h2>Localização</h2>
                    <p class="section-sub">Nossa sede em São Paulo está pronta para receber visitantes e estudantes.</p>
                </div>
                <div class="map-frame">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3656.004526616228!2d-46.6559!3d-23.5649!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce59c0e160bb7f%3A0x20d44ec7e832d4d6!2sAvenida%20Paulista%2C%20100%2C%20São%20Paulo!5e0!3m2!1spt-BR!2sbr!4v1700000000000"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        aria-label="Mapa do endereço da FETEL"></iframe>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2>Redes sociais</h2>
                    <p class="section-sub">Acompanhe a FETEL nas principais plataformas digitais.</p>
                </div>
                <div class="social-group">
                    <a class="social-badge" href="#" aria-label="Instagram">
                        <span>📸</span>
                        <div>
                            <strong>Instagram</strong>
                            <p>@fetel_oficial</p>
                        </div>
                    </a>
                    <a class="social-badge" href="#" aria-label="Facebook">
                        <span>📘</span>
                        <div>
                            <strong>Facebook</strong>
                            <p>/fetel</p>
                        </div>
                    </a>
                    <a class="social-badge" href="#" aria-label="WhatsApp">
                        <span>💬</span>
                        <div>
                            <strong>WhatsApp</strong>
                            <p>(11) 4000-0000</p>
                        </div>
                    </a>
                    <a class="social-badge" href="#" aria-label="LinkedIn">
                        <span>🔗</span>
                        <div>
                            <strong>LinkedIn</strong>
                            <p>FETEL</p>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <section class="section alt">
            <div class="container cta-banner">
                <h2>Precisa de ajuda imediata?</h2>
                <p>Fale agora com a equipe da FETEL pelo WhatsApp e receba atendimento prioritário.</p>
                <a href="https://wa.me/551140000000" class="btn primary">Entrar em contato no WhatsApp</a>
            </div>
        </section>
    </main>

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
        const contactForm = document.getElementById('contactForm');
        const contactSuccess = document.getElementById('contactSuccess');
        const resetForm = document.getElementById('resetForm');

        if (contactForm) {
            contactForm.addEventListener('submit', function (event) {
                event.preventDefault();
                contactSuccess.style.display = 'block';
                contactForm.reset();
                setTimeout(() => {
                    contactSuccess.style.display = 'none';
                }, 6500);
            });
        }

        if (resetForm) {
            resetForm.addEventListener('click', function () {
                contactForm.reset();
                contactSuccess.style.display = 'none';
            });
        }

        document.querySelectorAll('.faq-question').forEach(function (button) {
            button.addEventListener('click', function () {
                const target = document.getElementById(this.dataset.target);
                const isOpen = target.classList.contains('open');
                document.querySelectorAll('.faq-answer').forEach((answer) => answer.classList.remove('open'));
                document.querySelectorAll('.faq-question span').forEach((span) => span.textContent = '+');
                if (!isOpen) {
                    target.classList.add('open');
                    this.querySelector('span').textContent = '−';
                }
            });
        });
    </script>
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
