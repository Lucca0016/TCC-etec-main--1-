<?php
$deadline = new DateTimeImmutable('2026-06-15');
$today = new DateTimeImmutable('today');
$daysLeft = max(0, $today->diff($deadline)->days);
$cursos = $cursos ?? [];
$errors = $errors ?? [];
$old = $old ?? [];
function oldValue(string $key, array $old): string
{
    return htmlspecialchars($old[$key] ?? '', ENT_QUOTES, 'UTF-8');
}
function fieldError(array $errors, string $key): string
{
    return $errors[$key] ?? '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vestibular FETEL 2026</title>
    <meta name="description" content="Inscrições abertas para o Vestibular FETEL 2026. Cursos técnicos e profissionalizantes com opções presenciais e online.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/TCC-etec/css/style.css">
    <style>
        .vestibular-hero {
            padding: 80px 0 40px;
        }
        .vestibular-hero .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
            gap: 36px;
            align-items: center;
        }
        .hero-label {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: rgba(0, 86, 179, 0.1);
            color: #003d99;
            border-radius: 999px;
            padding: 10px 18px;
            font-weight: 600;
            margin-bottom: 22px;
        }
        .hero-label strong {
            background: #0056d6;
            color: #fff;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.9rem;
        }
        .hero-copy h1 {
            max-width: 680px;
            font-size: clamp(3rem, 3.5vw, 4.5rem);
            line-height: 1.02;
            margin: 0 0 18px;
        }
        .hero-copy p.lead {
            max-width: 600px;
            font-size: 1.1rem;
            color: #334155;
            margin-bottom: 28px;
        }
        .hero-actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }
        .hero-card {
            background: rgba(0, 61, 153, 0.05);
            border: 1px solid rgba(0, 61, 153, 0.12);
            border-radius: 30px;
            padding: 30px;
            box-shadow: 0 30px 60px rgba(3, 28, 70, 0.08);
            display: grid;
            gap: 20px;
        }
        .hero-card strong {
            display: block;
            font-size: 1rem;
            color: #0b1f3e;
        }
        .hero-card span {
            display: block;
            font-size: 2rem;
            font-weight: 700;
            color: #003d99;
        }
        .hero-visual {
            position: relative;
            display: grid;
            place-items: center;
            min-height: 520px;
        }
        .hero-visual .visual-panel {
            width: 100%;
            border-radius: 32px;
            background: linear-gradient(180deg, rgba(0,86,179,0.16), rgba(10,132,255,0.05));
            padding: 36px;
            box-shadow: 0 30px 70px rgba(3, 28, 70, 0.12);
            position: relative;
        }
        .hero-visual .visual-panel::before {
            content: '';
            position: absolute;
            width: 160px;
            height: 160px;
            background: rgba(0,86,179,0.16);
            border-radius: 50%;
            top: -40px;
            right: -40px;
            filter: blur(18px);
        }
        .hero-visual .visual-panel::after {
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,0.85);
            border-radius: 50%;
            bottom: -24px;
            left: -24px;
        }
        .hero-visual .visual-image {
            display: grid;
            place-items: center;
            width: 100%;
        }
        .hero-visual .visual-image svg {
            width: 100%;
            max-width: 420px;
        }
        .info-cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-top: 40px;
        }
        .info-card {
            background: #fff;
            border-radius: 24px;
            padding: 24px;
            border: 1px solid rgba(15, 52, 96, 0.1);
            box-shadow: 0 24px 48px rgba(3, 28, 70, 0.06);
            display: grid;
            gap: 12px;
        }
        .info-card .icon {
            font-size: 1.6rem;
        }
        .info-card strong {
            font-size: 0.95rem;
            color: #0b1f3e;
            display: block;
        }
        .info-card p {
            margin: 0;
            color: #475569;
        }
        .split-section {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr);
            gap: 32px;
            align-items: start;
        }
        .form-card {
            background: #fff;
            padding: 34px;
            border-radius: 30px;
            border: 1px solid rgba(15, 52, 96, 0.08);
            box-shadow: 0 28px 56px rgba(3, 28, 70, 0.05);
        }
        .form-card h2 {
            margin-top: 0;
            color: #08304e;
        }
        .form-grid {
            display: grid;
            gap: 18px;
        }
        .form-field {
            display: grid;
            gap: 10px;
        }
        .form-field label {
            font-weight: 600;
            color: #0b1f3e;
        }
        .form-field input,
        .form-field select,
        .form-field textarea {
            width: 100%;
            padding: 16px 18px;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            background: #fff;
            color: #0f172a;
            font-size: 1rem;
            outline: none;
        }
        .form-field textarea {
            min-height: 140px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }
        .form-row.one {
            grid-template-columns: 1fr;
        }
        .field-error {
            color: #b91c1c;
            font-size: 0.92rem;
            margin-top: 6px;
        }
        .badge-alert {
            background: #eeefff;
            color: #1e40af;
            padding: 18px 20px;
            border-radius: 18px;
            border: 1px solid rgba(59, 130, 246, 0.15);
        }
        .course-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
        }
        .course-card,
        .benefit-card,
        .timeline-step,
        .faq-item {
            background: #fff;
            border-radius: 26px;
            border: 1px solid rgba(15, 52, 96, 0.08);
            box-shadow: 0 24px 50px rgba(3, 28, 70, 0.05);
        }
        .course-card {
            padding: 26px;
            display: grid;
            gap: 14px;
        }
        .course-card h3 {
            margin: 0;
            color: #08304e;
        }
        .course-card .meta {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            color: #475569;
            font-size: 0.95rem;
        }
        .course-card .course-pill {
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(0, 86, 179, 0.08);
            color: #003d99;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .timeline {
            display: grid;
            gap: 20px;
            padding: 24px;
            background: rgba(0, 86, 179, 0.04);
            border-radius: 30px;
            border: 1px solid rgba(0, 86, 179, 0.08);
        }
        .timeline-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }
        .timeline-step {
            flex: 1 1 160px;
            padding: 24px;
            position: relative;
        }
        .timeline-step::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 100%;
            width: 2px;
            height: 20px;
            background: rgba(0, 86, 179, 0.16);
        }
        .timeline-step:last-child::after {
            display: none;
        }
        .timeline-step .step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #0056d6;
            color: #fff;
            font-weight: 700;
            margin-bottom: 14px;
        }
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 22px;
        }
        .benefit-card {
            padding: 26px;
            display: grid;
            gap: 14px;
        }
        .benefit-card .icon {
            font-size: 1.6rem;
        }
        .faq-grid {
            display: grid;
            gap: 18px;
        }
        .faq-item {
            overflow: hidden;
        }
        .faq-question {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 22px 24px;
            cursor: pointer;
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
            padding: 16px 24px 24px;
            max-height: 320px;
        }
        .faq-answer p {
            margin: 0;
            color: #475569;
            line-height: 1.8;
        }
        .cta-panel {
            padding: 42px 36px;
            border-radius: 28px;
            background: linear-gradient(135deg, #003d99, #0056d6);
            color: #fff;
            text-align: center;
            box-shadow: 0 34px 72px rgba(0, 61, 153, 0.2);
        }
        .cta-panel h2 {
            margin: 0 0 12px;
            font-size: clamp(2rem, 2.5vw, 2.6rem);
            line-height: 1.05;
        }
        .cta-panel p {
            margin: 0 0 24px;
            font-size: 1.05rem;
            color: rgba(255,255,255,0.9);
        }
        .footer-footer {
            padding: 40px 0 24px;
        }
        .footer-footer .footer-inner {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }
        .footer-footer a {
            color: #fff;
            text-decoration: none;
        }
        .footer-footer p,
        .footer-footer li {
            color: #dbeafe;
        }
        @media (max-width: 1100px) {
            .hero-grid,
            .split-section,
            .course-grid,
            .timeline-container,
            .benefits-grid,
            .footer-footer .footer-inner {
                grid-template-columns: 1fr;
            }
            .timeline-step::after {
                display: none;
            }
        }
        @media (max-width: 720px) {
            .hero-visual { min-height: auto; }
            .form-row { grid-template-columns: 1fr; }
            .info-cards { grid-template-columns: 1fr 1fr; }
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
                    <li><a class="active-link" href="/TCC-etec/vestibular">Vestibular</a></li>
                    <li><a href="/TCC-etec/biblioteca">Biblioteca</a></li>
                    <li><a href="/TCC-etec/contato">Contato</a></li>
                    <li><a href="/TCC-etec/login" class="btn ghost">Perfil</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero vestibular-hero" id="vestibular">
            <div class="container hero-grid">
                <div class="hero-copy">
                    <span class="hero-label"><strong>Inscrições abertas</strong> Vestibular FETEL 2026</span>
                    <h1>Vestibular FETEL 2026</h1>
                    <p class="lead">Inscrições abertas para cursos técnicos e profissionalizantes.</p>
                    <div class="hero-actions">
                        <a class="btn primary" href="#inscricao">Inscreva-se agora</a>
                        <a class="btn ghost" href="/TCC-etec/cursos">Ver cursos</a>
                    </div>
                    <div class="hero-card">
                        <strong>Encerramento em</strong>
                        <span><?= $daysLeft ?> dias</span>
                        <p>Garanta sua vaga no vestibular antes do prazo final.</p>
                    </div>
                    <div class="info-cards">
                        <article class="info-card">
                            <div class="icon">📅</div>
                            <strong>Data da prova</strong>
                            <p>15 de junho de 2026</p>
                        </article>
                        <article class="info-card">
                            <div class="icon">💻</div>
                            <strong>Modalidade</strong>
                            <p>Online e presencial</p>
                        </article>
                        <article class="info-card">
                            <div class="icon">💰</div>
                            <strong>Taxa</strong>
                            <p>Gratuito</p>
                        </article>
                        <article class="info-card">
                            <div class="icon">📍</div>
                            <strong>Unidade</strong>
                            <p>São Bernardo do Campo</p>
                        </article>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="visual-panel">
                        <div class="visual-image">
                            <svg viewBox="0 0 540 470" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <rect x="20" y="60" width="500" height="340" rx="40" fill="#EAF2FF"/>
                                <path d="M80 160h380" stroke="#0056D6" stroke-width="16" stroke-linecap="round"/>
                                <circle cx="130" cy="240" r="22" fill="#0056D6"/>
                                <circle cx="210" cy="240" r="22" fill="#0046B3"/>
                                <circle cx="290" cy="240" r="22" fill="#003D99"/>
                                <circle cx="370" cy="240" r="22" fill="#0056D6"/>
                                <rect x="92" y="280" width="356" height="24" rx="12" fill="#C5D9FF"/>
                                <rect x="110" y="320" width="300" height="14" rx="7" fill="#B8D0FF"/>
                                <rect x="110" y="350" width="200" height="14" rx="7" fill="#C5D9FF"/>
                                <path d="M130 120c24-20 70-30 124-30s102 10 126 30" stroke="#0056D6" stroke-width="14" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section split-section" id="inscricao">
            <div class="form-card">
                <h2>Pré-inscrição</h2>
                <p class="lead">Complete seus dados e dê o primeiro passo para estudar na FETEL.</p>
                <?php if (!empty($errors)): ?>
                    <div class="badge-alert">
                        <strong>Existem campos com erros.</strong> Revise o formulário abaixo.
                    </div>
                <?php endif; ?>
                <form action="/TCC-etec/vestibular/inscricao" method="post" class="form-grid" novalidate>
                    <div class="form-row">
                        <div class="form-field">
                            <label for="nome">Nome completo</label>
                            <input id="nome" name="nome" type="text" value="<?= oldValue('nome', $old) ?>" required>
                            <?php if ($error = fieldError($errors, 'nome')): ?><span class="field-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </div>
                        <div class="form-field">
                            <label for="cpf">CPF</label>
                            <input id="cpf" name="cpf" type="text" value="<?= oldValue('cpf', $old) ?>" placeholder="000.000.000-00" required>
                            <?php if ($error = fieldError($errors, 'cpf')): ?><span class="field-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <label for="email">E-mail</label>
                            <input id="email" name="email" type="email" value="<?= oldValue('email', $old) ?>" required>
                            <?php if ($error = fieldError($errors, 'email')): ?><span class="field-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </div>
                        <div class="form-field">
                            <label for="telefone">Telefone</label>
                            <input id="telefone" name="telefone" type="tel" value="<?= oldValue('telefone', $old) ?>" placeholder="(11) 90000-0000" required>
                            <?php if ($error = fieldError($errors, 'telefone')): ?><span class="field-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <label for="curso">Curso desejado</label>
                            <select id="curso" name="curso" required>
                                <option value="">Selecione o curso</option>
                                <?php foreach ($cursos as $curso): ?>
                                    <option value="<?= htmlspecialchars($curso, ENT_QUOTES, 'UTF-8') ?>" <?= $curso === ($old['curso'] ?? '') ? 'selected' : '' ?>><?= htmlspecialchars($curso, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($error = fieldError($errors, 'curso')): ?><span class="field-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </div>
                        <div class="form-field">
                            <label for="turno">Turno</label>
                            <select id="turno" name="turno" required>
                                <option value="">Selecione o turno</option>
                                <option value="Manhã" <?= ($old['turno'] ?? '') === 'Manhã' ? 'selected' : '' ?>>Manhã</option>
                                <option value="Tarde" <?= ($old['turno'] ?? '') === 'Tarde' ? 'selected' : '' ?>>Tarde</option>
                                <option value="Noite" <?= ($old['turno'] ?? '') === 'Noite' ? 'selected' : '' ?>>Noite</option>
                                <option value="Integral" <?= ($old['turno'] ?? '') === 'Integral' ? 'selected' : '' ?>>Integral</option>
                            </select>
                            <?php if ($error = fieldError($errors, 'turno')): ?><span class="field-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <label for="modalidade">Modalidade</label>
                            <select id="modalidade" name="modalidade" required>
                                <option value="">Selecione a modalidade</option>
                                <option value="Presencial" <?= ($old['modalidade'] ?? '') === 'Presencial' ? 'selected' : '' ?>>Presencial</option>
                                <option value="Online" <?= ($old['modalidade'] ?? '') === 'Online' ? 'selected' : '' ?>>Online</option>
                                <option value="Híbrido" <?= ($old['modalidade'] ?? '') === 'Híbrido' ? 'selected' : '' ?>>Híbrido</option>
                            </select>
                            <?php if ($error = fieldError($errors, 'modalidade')): ?><span class="field-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </div>
                        <div class="form-field">
                            <label for="unidade">Unidade</label>
                            <input id="unidade" name="unidade" type="text" value="São Bernardo do Campo" readonly>
                        </div>
                    </div>
                    <div class="form-row one">
                        <label class="form-field" style="gap: 12px; align-items: flex-start;">
                            <input type="checkbox" name="lgpd" id="lgpd" value="1" <?= ($old['lgpd'] ?? '') === '1' ? 'checked' : '' ?> required>
                            <span>Concordo com a política de privacidade e LGPD para uso dos dados.</span>
                        </label>
                        <?php if ($error = fieldError($errors, 'lgpd')): ?><span class="field-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                    </div>
                    <button type="submit" class="btn primary">Enviar inscrição</button>
                </form>
            </div>
            <aside class="hero-card">
                <h3>Por que escolher o Vestibular FETEL?</h3>
                <ul style="list-style:none; padding:0; margin:0; display:grid; gap:14px;">
                    <li>✅ Cursos alinhados ao mercado</li>
                    <li>✅ Avaliação ágil e totalmente online</li>
                    <li>✅ Preparação e certificação profissional</li>
                    <li>✅ Suporte exclusivo para candidatos</li>
                </ul>
            </aside>
        </section>

        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2>Cursos disponíveis</h2>
                    <p class="section-sub">Escolha entre as principais áreas de formação.</p>
                </div>
                <div class="course-grid">
                    <?php foreach ([
                        ['emoji' => '💻', 'title' => 'Desenvolvimento de Sistemas', 'duracao' => '2 anos', 'modalidade' => 'Presencial', 'vagas' => '120 vagas'],
                        ['emoji' => '📊', 'title' => 'Administração', 'duracao' => '1,5 anos', 'modalidade' => 'Híbrido', 'vagas' => '90 vagas'],
                        ['emoji' => '🩺', 'title' => 'Enfermagem', 'duracao' => '3 anos', 'modalidade' => 'Presencial', 'vagas' => '80 vagas'],
                        ['emoji' => '🌐', 'title' => 'Redes', 'duracao' => '2 anos', 'modalidade' => 'Online', 'vagas' => '100 vagas'],
                        ['emoji' => '🎨', 'title' => 'Design', 'duracao' => '2 anos', 'modalidade' => 'Híbrido', 'vagas' => '70 vagas'],
                        ['emoji' => '📱', 'title' => 'Mobile', 'duracao' => '2 anos', 'modalidade' => 'Online', 'vagas' => '90 vagas'],
                    ] as $course): ?>
                        <article class="course-card">
                            <div class="course-pill"><?= $course['emoji'] ?></div>
                            <h3><?= htmlspecialchars($course['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <div class="meta">
                                <span>Duração: <?= htmlspecialchars($course['duracao'], ENT_QUOTES, 'UTF-8') ?></span>
                                <span>Modalidade: <?= htmlspecialchars($course['modalidade'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <p style="color:#475569; line-height:1.7;">Vagas limitadas para turma 2026. Aproveite a seleção e garanta sua oportunidade de aprendizado prático.</p>
                            <a href="/TCC-etec/cursos" class="btn ghost">Ver detalhes</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section" style="padding-top: 0;">
            <div class="container">
                <div class="section-header">
                    <h2>Etapas do processo</h2>
                    <p class="section-sub">O caminho completo para garantir sua vaga.</p>
                </div>
                <div class="timeline-container">
                    <?php foreach ([
                        ['label' => 'Inscrição', 'description' => 'Envie seu pré-cadastro e confirme seus dados.'],
                        ['label' => 'Confirmação', 'description' => 'Receba a confirmação da inscrição por e-mail.'],
                        ['label' => 'Prova / análise', 'description' => 'Participe da prova ou envie sua análise de perfil.'],
                        ['label' => 'Resultado', 'description' => 'Veja sua classificação e aprovação.'],
                        ['label' => 'Matrícula', 'description' => 'Finalize matrícula e garanta sua vaga.' ],
                    ] as $index => $step): ?>
                        <article class="timeline-step">
                            <div class="step-badge"><?= $index + 1 ?></div>
                            <h3><?= htmlspecialchars($step['label'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p style="color:#475569; line-height:1.7; margin:8px 0 0;"><?= htmlspecialchars($step['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2>Bolsas e benefícios</h2>
                    <p class="section-sub">Recursos exclusivos para acelerar sua jornada acadêmica.</p>
                </div>
                <div class="benefits-grid">
                    <article class="benefit-card">
                        <div class="icon">🎓</div>
                        <h3>Bolsa parcial</h3>
                        <p>Descontos para estudantes com desempenho e perfil social comprovados.</p>
                    </article>
                    <article class="benefit-card">
                        <div class="icon">🏆</div>
                        <h3>Bolsa mérito</h3>
                        <p>Incentivo para candidatos com notas altas na seleção.</p>
                    </article>
                    <article class="benefit-card">
                        <div class="icon">💼</div>
                        <h3>Estágio integrado</h3>
                        <p>Vínculo direto com empresas parceiras desde o início do curso.</p>
                    </article>
                    <article class="benefit-card">
                        <div class="icon">📚</div>
                        <h3>Material de apoio</h3>
                        <p>Conteúdos digitais e bibliografia garantidos para todos os candidatos.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" style="padding-top: 0;">
            <div class="container">
                <div class="section-header">
                    <h2>Perguntas frequentes</h2>
                    <p class="section-sub">Tudo o que você precisa saber para se inscrever com segurança.</p>
                </div>
                <div class="faq-grid">
                    <?php foreach ([
                        ['question' => 'Como fazer inscrição?', 'answer' => 'Preencha o formulário acima, confirme seus dados e envie. Após o envio, você receberá uma confirmação por e-mail.'],
                        ['question' => 'Tem taxa?', 'answer' => 'Não. A inscrição para o Vestibular FETEL 2026 é totalmente gratuita.'],
                        ['question' => 'Como ver resultado?', 'answer' => 'O resultado será divulgado por e-mail e em nossa página oficial. Você também pode entrar em contato pela secretaria.'],
                        ['question' => 'Posso trocar curso?', 'answer' => 'Sim, trocas podem ser solicitadas antes do fechamento das inscrições, conforme disponibilidade de vagas. Entre em contato com a secretaria.' ],
                        ['question' => 'Quais documentos?', 'answer' => 'Leve RG, CPF, comprovante de endereço e certificado de conclusão do ensino médio ou técnico, conforme exigência do curso.'],
                    ] as $index => $faq): ?>
                        <article class="faq-item">
                            <button type="button" class="faq-question" data-target="faq-<?= $index ?>">
                                <h3><?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <span>+</span>
                            </button>
                            <div class="faq-answer" id="faq-<?= $index ?>">
                                <p><?= htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section alt">
            <div class="container cta-panel">
                <h2>Garanta sua vaga hoje mesmo</h2>
                <p>Inscreva-se no Vestibular FETEL 2026 e conquiste uma formação prática e conectada ao mercado.</p>
                <a href="#inscricao" class="btn primary">Inscrever agora</a>
            </div>
        </section>
    </main>

    <footer class="site-footer footer-footer" style="background:#031d44; color:#fff;">
        <div class="container footer-inner">
            <div>
                <h4>FETEL</h4>
                <p>Educação técnica e profissionalizante em São Bernardo do Campo.</p>
                <p>Rua Exemplo, 123 — Centro, São Bernardo do Campo, SP</p>
                <p>contato@fetel.edu.br | (11) 4000-0000</p>
            </div>
            <div>
                <h4>Links úteis</h4>
                <ul style="list-style:none; padding:0; margin:0; display:grid; gap:10px;">
                    <li><a href="/TCC-etec/">Início</a></li>
                    <li><a href="/TCC-etec/cursos">Cursos</a></li>
                    <li><a href="/TCC-etec/biblioteca">Biblioteca</a></li>
                    <li><a href="/TCC-etec/contato">Contato</a></li>
                </ul>
            </div>
        </div>
        <div class="container" style="padding-top:18px; text-align:center; color:rgba(255,255,255,0.72);">
            <p>© <?= date('Y') ?> FETEL — Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        document.querySelectorAll('.faq-question').forEach(function(button) {
            button.addEventListener('click', function() {
                var target = document.getElementById(this.dataset.target);
                var open = target.classList.contains('open');
                document.querySelectorAll('.faq-answer').forEach(function(answer) {
                    answer.classList.remove('open');
                });
                document.querySelectorAll('.faq-question span').forEach(function(span) {
                    span.textContent = '+';
                });
                if (!open) {
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
