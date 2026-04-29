<?php

namespace App\Support;

use App\Core\Csrf;
use App\Models\Noticia;

class Frontend
{
    public static function renderLanding(): string
    {
        $csrfToken = Csrf::getToken();
        $latestNews = self::loadLatestNews();

        $newsCards = '';
        foreach ($latestNews as $news) {
            $title = htmlspecialchars($news['titulo'] ?? 'Novidade FETEL', ENT_QUOTES, 'UTF-8');
            $summary = htmlspecialchars(self::truncateText($news['conteudo'] ?? $news['resumo'] ?? '', 120), ENT_QUOTES, 'UTF-8');
            $date = isset($news['data_publicacao']) ? date('d/m/Y', strtotime($news['data_publicacao'])) : ($news['data'] ?? 'Atualização FETEL');
            
            $imgTag = '';
            if (!empty($news['imagem_capa'])) {
                $imgTag = "<img src=\"/TCC-etec" . htmlspecialchars($news['imagem_capa']) . "\" alt=\"Capa\" style=\"width:100%;height:150px;object-fit:cover;border-radius:14px;margin-bottom:15px;\">";
            }

            $newsCards .= "<article class=\"card card-news\">
                        $imgTag
                        <div class=\"news-meta\"><span>Notícia</span><time datetime=\"{$news['data_publicacao']}\">{$date}</time></div>
                        <h3>{$title}</h3>
                        <p>{$summary}</p>
                        <a href=\"/TCC-etec/contato\" class=\"btn btn-secondary\">Ler mais</a>
                    </article>";
        }

        $html = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FETEL - Educação Tecnológica</title>
    <meta name="description" content="FETEL transforma o futuro com cursos tecnológicos, vestibular e apoio ao aluno." />
    <script>window.__CSRF_TOKEN = '%CSRF_TOKEN%';</script>
    <link rel="stylesheet" href="/TCC-etec/public/dist/css/main-aaYBaw7H.css" />
    <style>
        :root {
            color-scheme: dark light;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: #0f2035;
            background: #eef3f8;
            line-height: 1.6;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(circle at top left, rgba(6, 62, 128, 0.08), transparent 28%),
                        linear-gradient(180deg, #eff3f7 0%, #f8fbff 100%);
        }

        img {
            max-width: 100%;
            display: block;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .landing-header {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(14px);
            background: rgba(255, 255, 255, 0.82);
            border-bottom: 1px solid rgba(15, 32, 53, 0.08);
            padding: 20px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 14px;
        }

        .brand img {
            width: 56px;
            height: auto;
            display: block;
        }

        .brand strong {
            font-size: 1.15rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #0b2d50;
        }

        .top-nav {
            display: flex;
            gap: 24px;
            align-items: center;
            flex-wrap: wrap;
        }

        .top-nav a {
            font-size: 0.95rem;
            color: #243a56;
            transition: color 180ms ease;
        }

        .top-nav a:hover {
            color: #0a2345;
        }

        .hero {
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 32px 48px;
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
            gap: 48px;
            align-items: center;
        }

        .hero-copy {
            max-width: 620px;
        }

        .hero .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 22px;
            font-size: 0.92rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            color: #2053a7;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(3rem, 4vw, 4.5rem);
            line-height: 0.96;
            color: #0c203d;
            letter-spacing: -0.04em;
        }

        .hero p {
            margin: 28px 0 0;
            max-width: 640px;
            font-size: 1.1rem;
            color: #41536f;
        }

        .hero-actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 32px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: none;
            padding: 16px 26px;
            font-weight: 700;
            transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
            cursor: pointer;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e57d0 0%, #1da4ff 100%);
            color: #fff;
            box-shadow: 0 18px 40px rgba(30, 87, 208, 0.18);
        }

        .btn-secondary {
            background: #ffffff;
            color: #0b2d50;
            border: 1px solid rgba(15, 32, 53, 0.14);
        }

        .btn-ghost {
            background: transparent;
            color: #0b2d50;
            border: 1px solid rgba(15, 32, 53, 0.12);
        }

        .hero-visual {
            position: relative;
            min-height: 480px;
            border-radius: 36px;
            background: linear-gradient(180deg, #1e57d0 0%, #0c2e6d 100%);
            overflow: hidden;
            box-shadow: 0 28px 80px rgba(15, 32, 53, 0.18);
            color: #fff;
            display: grid;
            place-items: center;
            padding: 40px;
        }

        .hero-visual::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.24), transparent 30%);
            pointer-events: none;
        }

        .hero-visual-content {
            position: relative;
            text-align: left;
            z-index: 1;
            max-width: 420px;
        }

        .hero-visual h2 {
            margin: 0 0 16px;
            font-size: 1.85rem;
            line-height: 1.05;
        }

        .hero-visual p {
            margin: 0;
            color: rgba(255,255,255,0.88);
            font-size: 1rem;
            line-height: 1.7;
        }

        .hero-visual .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
            padding: 12px 18px;
            border-radius: 999px;
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(10px);
            font-size: 0.93rem;
        }

        .section {
            padding: 64px 32px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }

        .section-header h2 {
            margin: 0;
            font-size: 2rem;
            color: #0b2d50;
        }

        .section-header p {
            margin: 0;
            color: #5b6e86;
            max-width: 620px;
            font-size: 1rem;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 24px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
        }

        .card {
            padding: 28px;
            border-radius: 28px;
            background: #ffffff;
            border: 1px solid rgba(15, 32, 53, 0.08);
            box-shadow: 0 18px 40px rgba(15, 32, 53, 0.06);
        }

        .card-news h3,
        .card-event h3,
        .card-course h3,
        .card-testimonial h3 {
            margin: 20px 0 14px;
            font-size: 1.15rem;
            color: #0b2d50;
        }

        .card-news p,
        .card-event p,
        .card-course p,
        .card-testimonial p {
            margin: 0;
            color: #56687b;
            font-size: 0.97rem;
        }

        .news-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: 0.85rem;
            color: #6f7e94;
        }

        .news-meta span {
            font-weight: 700;
            color: #2053a7;
        }

        .card-event {
            display: grid;
            gap: 18px;
        }

        .event-date {
            display: inline-flex;
            border-radius: 18px;
            padding: 12px 16px;
            background: rgba(30, 87, 208, 0.1);
            color: #11469f;
            font-weight: 700;
            width: fit-content;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }

        .feature-block {
            display: flex;
            gap: 18px;
            align-items: flex-start;
            background: linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
            border-radius: 26px;
            padding: 26px;
            border: 1px solid rgba(15, 32, 53, 0.08);
            box-shadow: 0 24px 45px rgba(15, 32, 53, 0.05);
        }

        .feature-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            font-size: 1.45rem;
            background: rgba(30, 87, 208, 0.12);
            color: #174da8;
            flex-shrink: 0;
        }

        .feature-content h3 {
            margin: 0 0 8px;
            font-size: 1.05rem;
            color: #0b2d50;
        }

        .feature-content p {
            margin: 0;
            color: #5d6f86;
        }

        .course-card {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .course-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: rgba(30, 87, 208, 0.1);
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 700;
            color: #11469f;
            width: fit-content;
        }

        .course-card h3 {
            margin: 0;
            font-size: 1.2rem;
        }

        .course-card .course-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
        }

        .cta-banner {
            margin: 0 auto;
            max-width: 1120px;
            border-radius: 32px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            background: linear-gradient(135deg, #1e57d0 0%, #1da4ff 100%);
            color: #fff;
            box-shadow: 0 30px 80px rgba(30, 87, 208, 0.22);
        }

        .cta-banner .cta-copy {
            padding: 44px 42px;
        }

        .cta-banner h2 {
            margin: 0 0 18px;
            font-size: 2.25rem;
        }

        .cta-banner p {
            margin: 0 0 28px;
            color: rgba(255,255,255,0.92);
            max-width: 560px;
        }

        .cta-banner .btn {
            min-width: 220px;
        }

        .cta-image {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
            background: rgba(255,255,255,0.08);
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
        }

        .card-testimonial {
            position: relative;
            overflow: hidden;
            min-height: 240px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .testimonial-quote {
            margin: 0;
            color: #1a2e4d;
            font-size: 1.03rem;
            line-height: 1.8;
        }

        .testimonial-author {
            margin-top: 22px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .testimonial-author strong {
            display: block;
            color: #0a2345;
        }

        .footer {
            background: #102141;
            color: #d8e4ff;
            padding: 60px 32px 40px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 28px;
            max-width: 1200px;
            margin: 0 auto 32px;
        }

        .footer h3 {
            margin: 0 0 18px;
            color: #ffffff;
            font-size: 1.05rem;
        }

        .footer p,
        .footer li,
        .footer a {
            color: rgba(216, 228, 255, 0.85);
            font-size: 0.95rem;
            line-height: 1.8;
        }

        .footer a:hover {
            color: #ffffff;
        }

        .footer-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 12px;
        }

        .footer-bottom {
            text-align: center;
            color: rgba(216, 228, 255, 0.7);
            font-size: 0.9rem;
        }

        @media (max-width: 980px) {
            .hero,
            .cta-banner,
            .testimonial-grid,
            .footer-grid,
            .grid-4,
            .grid-3,
            .benefits-grid {
                grid-template-columns: 1fr;
            }

            .landing-header {
                padding: 20px;
            }

            .hero {
                padding-top: 60px;
            }

            .hero-visual {
                min-height: 360px;
            }

            .cta-image {
                padding: 24px;
            }
        }

        @media (max-width: 620px) {
            .top-nav {
                gap: 14px;
                justify-content: center;
            }

            .hero {
                padding: 44px 20px 28px;
            }

            .hero-actions {
                flex-direction: column;
            }

            .hero-visual {
                border-radius: 24px;
                padding: 28px;
            }

            .hero-visual h2 {
                font-size: 1.65rem;
            }

            .section {
                padding: 44px 20px;
            }

            .footer {
                padding: 40px 20px 24px;
            }
        }
    </style>
</head>
<body>
    <header class="landing-header">
        <a class="brand" href="/TCC-etec/">
            <img src="/TCC-etec/img/fetel_sem_fundo.png" alt="FETEL" />
            <strong>FETEL</strong>
        </a>
        <nav class="top-nav" aria-label="Menu principal">
            <a href="/TCC-etec/cursos">Cursos</a>
            <a href="/TCC-etec/vestibular">Vestibular</a>
            <a href="/TCC-etec/biblioteca">Biblioteca</a>
            <a href="/TCC-etec/contato">Contato</a>
            <a class="btn btn-ghost" href="/TCC-etec/login">Área do aluno</a>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-copy">
                <span class="eyebrow">Educação tecnológica</span>
                <h1>Transformando o futuro através da educação tecnológica</h1>
                <p>Na FETEL, unimos professores experientes, laboratórios de ponta e cursos alinhados ao mercado para formar profissionais prontos para vencer.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="/TCC-etec/cursos">Conheça os cursos</a>
                    <a class="btn btn-secondary" href="/TCC-etec/vestibular">Inscreva-se no vestibular</a>
                    <a class="btn btn-ghost" href="/TCC-etec/login">Área do aluno</a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-visual-content">
                    <h2>Aprenda com propósito e ganhe destaque no mercado.</h2>
                    <p>Descubra projetos reais, mentorias com profissionais do setor e uma jornada prática que prepara você para carreiras em tecnologia, gestão e inovação.</p>
                    <div class="hero-pill">+90% de empregabilidade para alunos formados</div>
                </div>
            </div>
        </section>

        <section class="section" id="noticias">
            <div class="section-header">
                <div>
                    <h2>Últimas notícias e novidades</h2>
                    <p>Fique por dentro das principais novidades da FETEL, com atualizações sobre vestibular, parcerias, eventos e lançamento de cursos.</p>
                </div>
                <a class="btn btn-secondary" href="/TCC-etec/contato">Veja mais novidades</a>
            </div>
            <div class="grid-3">%LATEST_NEWS%</div>
        </section>

        <section class="section" id="eventos">
            <div class="section-header">
                <h2>Próximos eventos</h2>
                <p>Participe de encontros conectados ao mercado, eventos presenciais e experiências que ampliam sua formação.</p>
            </div>
            <div class="grid-4">
                <article class="card card-event">
                    <span class="event-date">08 Jun</span>
                    <h3>Vestibular 2026</h3>
                    <p>Processo seletivo com vagas para cursos técnicos, atestando o compromisso da FETEL com o futuro.</p>
                    <p><strong>Local:</strong> Campus FETEL · <strong>Horário:</strong> 09h às 17h</p>
                </article>
                <article class="card card-event">
                    <span class="event-date">15 Jun</span>
                    <h3>Feira de Profissões</h3>
                    <p>Encontro com empresas parceiras, stands de carreiras e apresentação dos cursos mais procurados.</p>
                    <p><strong>Local:</strong> Auditório Principal · <strong>Horário:</strong> 10h às 16h</p>
                </article>
                <article class="card card-event">
                    <span class="event-date">22 Jun</span>
                    <h3>Semana de Tecnologia</h3>
                    <p>Palestras, workshops e cases de inovação para conectar você às tendências digitais.</p>
                    <p><strong>Local:</strong> Laboratórios FETEL · <strong>Horário:</strong> 08h às 15h</p>
                </article>
                <article class="card card-event">
                    <span class="event-date">29 Jun</span>
                    <h3>Palestras com especialistas</h3>
                    <p>Debates sobre carreira, tecnologias emergentes e oportunidades de mercado para jovens talentos.</p>
                    <p><strong>Local:</strong> Sala Multiuso · <strong>Horário:</strong> 14h às 18h</p>
                </article>
            </div>
        </section>

        <section class="section" id="beneficios">
            <div class="section-header">
                <h2>Por que escolher a FETEL?</h2>
                <p>Uma formação moderna, com estrutura premium e foco em empregabilidade, para quem quer avançar na carreira.</p>
            </div>
            <div class="benefits-grid">
                <article class="feature-block">
                    <div class="feature-icon">🎓</div>
                    <div class="feature-content">
                        <h3>Professores qualificados</h3>
                        <p>Equipe de docentes com experiência no mercado e em projetos reais.</p>
                    </div>
                </article>
                <article class="feature-block">
                    <div class="feature-icon">🧪</div>
                    <div class="feature-content">
                        <h3>Laboratórios modernos</h3>
                        <p>Espaços equipados para prática, prototipagem e experiências tecnológicas.</p>
                    </div>
                </article>
                <article class="feature-block">
                    <div class="feature-icon">🚀</div>
                    <div class="feature-content">
                        <h3>Alta empregabilidade</h3>
                        <p>Conexão com empresas e formação orientada para as demandas do mercado.</p>
                    </div>
                </article>
                <article class="feature-block">
                    <div class="feature-icon">✅</div>
                    <div class="feature-content">
                        <h3>Certificação reconhecida</h3>
                        <p>Diplomas e certificados válidos para ingresso profissional e continuidade acadêmica.</p>
                    </div>
                </article>
            </div>
        </section>

        <section class="section" id="cursos">
            <div class="section-header">
                <h2>Cursos em destaque</h2>
                <p>Conheça algumas formações estratégicas da FETEL e veja porque cada curso está preparado para o mercado.</p>
            </div>
            <div class="grid-4">
                <article class="card card-course">
                    <span class="course-badge">Automação Industrial</span>
                    <h3>Curso técnico em Automação</h3>
                    <p>Formação prática para controlar e integrar processos industriais com tecnologia de ponta.</p>
                    <div class="course-footer">
                        <a class="btn btn-secondary" href="/TCC-etec/cursos">Ver detalhes</a>
                        <span>Híbrido</span>
                    </div>
                </article>
                <article class="card card-course">
                    <span class="course-badge">Redes de Computadores</span>
                    <h3>Curso técnico em Redes</h3>
                    <p>Capacitação para projetar, instalar e manter infraestruturas seguras e conectadas.</p>
                    <div class="course-footer">
                        <a class="btn btn-secondary" href="/TCC-etec/cursos">Ver detalhes</a>
                        <span>Presencial</span>
                    </div>
                </article>
                <article class="card card-course">
                    <span class="course-badge">Desenvolvimento</span>
                    <h3>Curso técnico em Informática</h3>
                    <p>Conteúdo focado em programação, banco de dados e soluções digitais para empresas.</p>
                    <div class="course-footer">
                        <a class="btn btn-secondary" href="/TCC-etec/cursos">Ver detalhes</a>
                        <span>Presencial</span>
                    </div>
                </article>
                <article class="card card-course">
                    <span class="course-badge">Gestão e Inovação</span>
                    <h3>Gestão Empresarial</h3>
                    <p>Formação para liderar equipes, projetos e iniciativas com visão estratégica.</p>
                    <div class="course-footer">
                        <a class="btn btn-secondary" href="/TCC-etec/cursos">Ver detalhes</a>
                        <span>Semipresencial</span>
                    </div>
                </article>
            </div>
            <div style="margin-top: 28px; text-align: center;">
                <a class="btn btn-primary" href="/TCC-etec/cursos">Ver todos os cursos</a>
            </div>
        </section>

        <section class="section">
            <div class="cta-banner">
                <div class="cta-copy">
                    <h2>Garanta seu futuro profissional hoje</h2>
                    <p>Matrículas e vestibular abertos para quem busca qualificação com foco em tecnologia, carreira e empregabilidade.</p>
                    <a class="btn btn-primary" href="/TCC-etec/vestibular">Inscreva-se no vestibular</a>
                </div>
                <div class="cta-image">
                    <img src="/TCC-etec/img/fetel_sem_fundo.png" alt="FETEL" aria-hidden="true" />
                </div>
            </div>
        </section>

        <section class="section" id="depoimentos">
            <div class="section-header">
                <h2>Depoimentos de alunos</h2>
                <p>Veja como a trajetória FETEL impactou a carreira de quem escolheu estudar com a gente.</p>
            </div>
            <div class="testimonial-grid">
                <article class="card card-testimonial">
                    <p class="testimonial-quote">“A FETEL mudou minha carreira com um ensino prático e professores que realmente entendem do mercado.”</p>
                    <div class="testimonial-author">
                        <strong>Mariana Lima</strong>
                        <span>Ex-aluna de Informática</span>
                    </div>
                </article>
                <article class="card card-testimonial">
                    <p class="testimonial-quote">“Os laboratórios são excelentes e me deram confiança para estagiar em uma grande empresa.”</p>
                    <div class="testimonial-author">
                        <strong>Rafael Souza</strong>
                        <span>Aluno de Automação</span>
                    </div>
                </article>
                <article class="card card-testimonial">
                    <p class="testimonial-quote">“A FETEL me conectou a experiências reais e abriu portas para uma carreira tecnológica de sucesso.”</p>
                    <div class="testimonial-author">
                        <strong>Luana Dias</strong>
                        <span>Formada em Gestão</span>
                    </div>
                </article>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-grid">
            <div>
                <h3>Contato</h3>
                <p>secretaria@fetel.edu.br<br />Telefone: (11) 4000-1234</p>
            </div>
            <div>
                <h3>Links rápidos</h3>
                <ul class="footer-list">
                    <li><a href="/TCC-etec/cursos">Cursos</a></li>
                    <li><a href="/TCC-etec/vestibular">Vestibular</a></li>
                    <li><a href="/TCC-etec/biblioteca">Biblioteca</a></li>
                    <li><a href="/TCC-etec/contato">Contato</a></li>
                </ul>
            </div>
            <div>
                <h3>Redes sociais</h3>
                <ul class="footer-list">
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">LinkedIn</a></li>
                    <li><a href="#">Facebook</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">© 2026 FETEL. Educação tecnológica para profissionais preparados.</div>
    </footer>
</body>
</html>
HTML;

        return str_replace(
            ['%CSRF_TOKEN%', '%LATEST_NEWS%'],
            [$csrfToken, $newsCards],
            $html
        );
    }

    private static function loadLatestNews(): array
    {
        try {
            $noticiaModel = new \App\Models\Noticia();
            $news = $noticiaModel->listarParaCarrossel(3);
            if (is_array($news) && count($news) > 0) {
                return $news;
            }
        } catch (\Throwable $exception) {
            error_log('Erro ao carregar notícias da landing page: ' . $exception->getMessage());
        }

        return [
            [
                'titulo' => 'Inscrições abertas para o Vestibular FETEL',
                'conteudo' => 'Garanta sua vaga e esteja preparado para ingressar em cursos tecnológicos com alta empregabilidade.',
                'data_publicacao' => date('Y-m-d'),
            ],
            [
                'titulo' => 'Feira de Profissões na FETEL',
                'conteudo' => 'Participe de workshops e conheça as opções de cursos que preparam você para o mercado.',
                'data_publicacao' => date('Y-m-d', strtotime('-3 days')),
            ],
            [
                'titulo' => 'Semana de Tecnologia com palestras gratuitas',
                'conteudo' => 'Conecte-se com profissionais do setor e aprenda sobre inovação, automação e redes.',
                'data_publicacao' => date('Y-m-d', strtotime('-7 days')),
            ],
            [
                'titulo' => 'Novas parcerias com empresas do setor',
                'conteudo' => 'A FETEL amplia oportunidades para estágios e projetos reais com grandes empresas.',
                'data_publicacao' => date('Y-m-d', strtotime('-12 days')),
            ],
        ];
    }

    private static function truncateText(string $text, int $limit = 120): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', strip_tags($text)));
        if (mb_strlen($clean) <= $limit) {
            return $clean;
        }
        return mb_substr($clean, 0, $limit - 1) . '…';
    }
}
