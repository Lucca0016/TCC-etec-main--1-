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
    <!-- VIEW_MARKER: app_resources/views/auth/login.php -->
    <div id="view_marker" style="display:none">app_resources</div>
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

            <form method="POST" class="login-form form-stack" novalidate id="login-form">
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
        // Inline performLogin: define globally so button onclick works
        const API_URL = '/TCC-etec/public/api';

        async function performLogin(e) {
            if (e && e.preventDefault) e.preventDefault();

            const form = document.getElementById('login-form') || document.querySelector('form.login-form');
            if (!form) return console.error('Formulário de login não encontrado');

            const emailEl = form.querySelector('#email');
            const senhaEl = form.querySelector('#senha');
            const perfilEl = form.querySelector('#perfil');
            const redirectEl = form.querySelector('input[name="redirect"]');
            const csrfEl = form.querySelector('input[name="_csrf"]');

            const payload = {
                email: emailEl ? emailEl.value : '',
                senha: senhaEl ? senhaEl.value : '',
                perfil: perfilEl ? perfilEl.value : '',
                _csrf: csrfEl ? csrfEl.value : ''
            };

            try {
                const res = await fetch(`${API_URL}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const ct = res.headers.get('content-type') || '';
                if (ct.includes('application/json')) {
                    const dados = await res.json();
                    if (dados.access_token) localStorage.setItem('access_token', dados.access_token);
                    if (dados.refresh_token) localStorage.setItem('refresh_token', dados.refresh_token);
                    const destino = (redirectEl && redirectEl.value) ? redirectEl.value : '/TCC-etec/';
                    window.location.href = destino;
                    return;
                }

                if (res.redirected) {
                    window.location.href = res.url;
                    return;
                }

                const texto = await res.text();
                console.error('Login falhou — resposta inesperada:', texto);
                showApiError('Falha ao autenticar. Veja console para detalhes.');
            } catch (err) {
                console.error('Erro ao chamar API de login', err);
                showApiError('Erro de rede ao tentar autenticar.');
            }
        }

        function showApiError(msg) {
            let errEl = document.getElementById('api-error');
            if (!errEl) {
                errEl = document.createElement('div');
                errEl.id = 'api-error';
                errEl.className = 'message message-error';
                const form = document.getElementById('login-form');
                form.parentNode.insertBefore(errEl, form);
            }
            errEl.textContent = msg;
        }

        // Expor explicitamente e logar para diagnóstico
        try {
            window.performLogin = performLogin;
            console.log('performLogin assigned to window:', typeof window.performLogin);
        } catch (e) {
            console.error('Erro ao expor performLogin no window', e);
        }
    </script>

    <script>

            const profileButtons = document.querySelectorAll('.profile-pill');
        const perfilInput = document.getElementById('perfil');
        const descricao = document.getElementById('perfilDescricao');
        const submitButton = document.getElementById('submitButton');
        const profileData = <?php echo json_encode($perfis, JSON_UNESCAPED_UNICODE); ?>;

        profileButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const slug = button.dataset.profile;
                perfilInput.value = slug;
                descricao.textContent = profileData[slug].descricao;
                submitButton.textContent = profileData[slug].botao;
                profileButtons.forEach((btn) => btn.classList.toggle('active', btn === button));
            });
        });
    </script>
</body>
</html>
