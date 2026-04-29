<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login — FETEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/TCC-etec/public/css/login.css" />
</head>
<body class="login-body">
    <main class="login-page">
        <section class="login-card" aria-labelledby="loginTitle">
            <div class="brand-stamp">
                <img class="brand-logo" src="/TCC-etec/public/img/fetel_sem_fundo.png" alt="FETEL" loading="lazy" />
            </div>

            <header class="card-head">
                <p class="card-eyebrow">Acesso restrito</p>
                <h2 id="loginTitle">Entrar</h2>
                <p class="card-subhead" id="perfilDescricao">
                    <?php echo htmlspecialchars($perfis[$perfilAtivo]['descricao'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
            </header>

            <div class="profile-toggle" role="tablist" aria-label="Selecionar perfil de acesso">
                <?php foreach ($perfis as $slug => $info): ?>
                    <button
                        type="button"
                        class="profile-pill <?php echo $perfilAtivo === $slug ? 'active' : ''; ?>"
                        data-profile="<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>"
                        aria-pressed="<?php echo $perfilAtivo === $slug ? 'true' : 'false'; ?>"
                    >
                        <?php echo htmlspecialchars($info['label'], ENT_QUOTES, 'UTF-8'); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($erro)): ?>
                <div class="message message-error" role="alert">
                    <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($mensagem)): ?>
                <div class="message message-success" role="status">
                    <?php echo htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form id="login-form" method="POST" class="login-form form-stack" novalidate>
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>" />
                <input type="hidden" name="perfil" id="perfil" value="<?php echo htmlspecialchars($perfilAtivo, ENT_QUOTES, 'UTF-8'); ?>" />
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8'); ?>" />

                <div class="field">
                    <label for="email">E-mail institucional</label>
                
                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        placeholder="<?php echo htmlspecialchars($perfis[$perfilAtivo]['placeholder'], ENT_QUOTES, 'UTF-8'); ?>"
                        autocomplete="username"
                    />
                </div>

                <div class="field">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required minlength="6" autocomplete="current-password" />
                </div>

                <div id="api-error" class="message message-error" role="alert" style="display:none;"></div>

                <button type="button" class="btn primary full" id="submitButton" onclick="performLogin(event)">
                    <?php echo htmlspecialchars($perfis[$perfilAtivo]['botao'], ENT_QUOTES, 'UTF-8'); ?>
                </button>

                <div class="login-footer">
                    <a href="/TCC-etec/recuperar_senha" class="forgot-link">Esqueceu a senha?</a>
                </div>
            </form>
        </section>
    </main>

    <script>
    // Global performLogin function exposed for onclick handler
    const API_URL = '/TCC-etec/public/api';
    const profileData = <?php echo json_encode($perfis, JSON_UNESCAPED_UNICODE); ?>;
    let submitButton = null;
    let apiError = null;

    async function performLogin(e) {
        if (e && e.preventDefault) e.preventDefault();

        const form = document.getElementById('login-form');
        if (!form) return console.error('Formulário de login não encontrado');

        submitButton = document.getElementById('submitButton');
        apiError = document.getElementById('api-error');

        const email = document.getElementById('email').value.trim();
        const senha = document.getElementById('senha').value;
        const perfil = document.getElementById('perfil').value;

        submitButton.disabled = true;
        submitButton.textContent = 'Entrando...';
        apiError.style.display = 'none';

        try {
            const res = await fetch(`${API_URL}/login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password: senha, profile: perfil })
            });

            if (!res.ok) {
                const text = await res.text();
                console.error('API responded with non-OK status', res.status, text);
                apiError.textContent = 'Erro na autenticação (status ' + res.status + ')';
                apiError.style.display = 'block';
                submitButton.disabled = false;
                submitButton.textContent = profileData[perfil]?.botao || 'Entrar';
                return;
            }

            const dados = await res.json();

            if (!dados.ok) {
                apiError.textContent = dados.message || 'Email ou senha incorretos.';
                apiError.style.display = 'block';
                submitButton.disabled = false;
                submitButton.textContent = profileData[perfil]?.botao || 'Entrar';
                return;
            }

            // Salva tokens
            localStorage.setItem('access_token', dados.access_token);
            localStorage.setItem('refresh_token', dados.refresh_token);

            // Redireciona pelo papel
            const papel = (dados.user?.papel || '').toLowerCase();
            const rotas = {
                admin: '/TCC-etec/admin',
                administrador: '/TCC-etec/admin',
                adm: '/TCC-etec/admin',
                secretaria: '/TCC-etec/secretaria',
                professor: '/TCC-etec/professor',
                aluno: '/TCC-etec/aluno',
                estudante: '/TCC-etec/aluno',
            };
            window.location.href = rotas[papel] || '/TCC-etec/';
        } catch (err) {
            console.error('Login error', err);
            apiError.textContent = 'Erro ao conectar com o servidor. Tente novamente.';
            apiError.style.display = 'block';
            submitButton.disabled = false;
            submitButton.textContent = profileData[perfil]?.botao || 'Entrar';
        }
    }

    (function () {

        function init() {
            // Redireciona se já estiver logado
            if (localStorage.getItem('access_token')) {
                window.location.href = '/TCC-etec/';
                return;
            }

            // Troca de perfil
            const profileButtons = document.querySelectorAll('.profile-pill');
            const perfilInput = document.getElementById('perfil');
            const descricao = document.getElementById('perfilDescricao');
            submitButton = document.getElementById('submitButton');

            profileButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const slug = button.dataset.profile;
                    perfilInput.value = slug;
                    descricao.textContent = profileData[slug].descricao;
                    submitButton.textContent = profileData[slug].botao;
                    profileButtons.forEach((btn) => btn.classList.toggle('active', btn === button));
                });
            });

            const form = document.getElementById('login-form');
            apiError = document.getElementById('api-error');

            // performLogin já está acessível globalmente para o onclick
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
    console.log('performLogin function loaded:', typeof performLogin);
    </script>
</body>
</html>
