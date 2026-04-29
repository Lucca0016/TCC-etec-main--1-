<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrição realizada | Vestibular FETEL</title>
    <meta name="description" content="Sua inscrição no Vestibular FETEL 2026 foi realizada com sucesso.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/TCC-etec/css/style.css">
    <style>
        .success-hero {
            min-height: 100vh;
            display: grid;
            align-items: center;
            padding: 80px 0;
        }
        .success-card {
            background: #fff;
            border-radius: 30px;
            padding: 48px 38px;
            max-width: 860px;
            margin: 0 auto;
            border: 1px solid rgba(15, 52, 96, 0.08);
            box-shadow: 0 28px 64px rgba(3, 28, 70, 0.08);
        }
        .success-card h1 {
            margin: 0 0 18px;
            color: #08304e;
            font-size: clamp(2.5rem, 3vw, 3.5rem);
            line-height: 1.02;
        }
        .success-card p {
            color: #475569;
            line-height: 1.8;
            margin: 0 0 34px;
        }
        .success-actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }
        .footer-footer {
            padding: 40px 0 24px;
        }
        .footer-footer a {
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="logo" href="/TCC-etec/">
                <img src="/TCC-etec/img/fetel_sem_fundo.png" alt="FETEL" width="42" height="42">
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
                    <li><a href="/TCC-etec/contato">Contato</a></li>
                    <li><a href="/TCC-etec/login" class="btn ghost">Perfil</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="success-hero">
        <div class="container success-card">
            <h1>Inscrição realizada com sucesso</h1>
            <p>Obrigado por se inscrever no Vestibular FETEL 2026. Sua pré-inscrição foi registrada e em breve você receberá um e-mail de confirmação com os próximos passos.</p>
            <div class="success-actions">
                <a class="btn primary" href="/TCC-etec/cursos">Ver cursos</a>
                <a class="btn ghost" href="/TCC-etec/">Voltar ao início</a>
                <a class="btn ghost" href="/TCC-etec/contato">Ir para contato</a>
            </div>
        </div>
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
