// js/auth.js
const API_URL = '/TCC-etec/public/api';

(function(){
    console.log('auth.js loaded');
    window.__auth_loaded = true;
})();

// Captura tokens injetados pelo PHP após login e salva no localStorage
(function capturarTokensDoLogin() {
    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : null;
    }

    const access = getCookie('_jwt_access');
    const refresh = getCookie('_jwt_refresh');

    if (access && refresh) {
        localStorage.setItem('access_token', access);
        localStorage.setItem('refresh_token', refresh);

        // Apaga os cookies após salvar
        document.cookie = '_jwt_access=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/TCC-etec/';
        document.cookie = '_jwt_refresh=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/TCC-etec/';
    }
})();

// LOGOUT
async function logout() {
    const token = localStorage.getItem('access_token');

    await fetch(`${API_URL}/logout`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` }
    });

    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
    window.location.href = '/TCC-etec/login';
}

// RENOVAR TOKEN (access expirou após 15 min)
async function renovarToken() {
    const refresh = localStorage.getItem('refresh_token');
    if (!refresh) return null;

    const res = await fetch(`${API_URL}/refresh`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ refresh_token: refresh })
    });

    if (!res.ok) {
        localStorage.removeItem('access_token');
        localStorage.removeItem('refresh_token');
        window.location.href = '/TCC-etec/login';
        return null;
    }

    const dados = await res.json();
    localStorage.setItem('access_token', dados.access_token);
    return dados.access_token;
}

// FETCH AUTENTICADO (renova token automaticamente se expirado)
async function apiFetch(endpoint, opcoes = {}) {
    let token = localStorage.getItem('access_token');

    // Verifica token antes de chamar
    const check = await fetch(`${API_URL}/check`, {
        headers: { 'Authorization': `Bearer ${token}` }
    });

    if (!check.ok) {
        token = await renovarToken();
        if (!token) return;
    }

    const res = await fetch(`${API_URL}${endpoint}`, {
        ...opcoes,
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
            ...opcoes.headers
        }
    });

    return res.json();
}

// Função chamada pelo botão na página de login quando o botão é do tipo "button"
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

        // Se o endpoint retornar JSON com tokens
        const ct = res.headers.get('content-type') || '';
        if (ct.includes('application/json')) {
            const dados = await res.json();
            if (dados.access_token) localStorage.setItem('access_token', dados.access_token);
            if (dados.refresh_token) localStorage.setItem('refresh_token', dados.refresh_token);
            const destino = (redirectEl && redirectEl.value) ? redirectEl.value : '/TCC-etec/';
            window.location.href = destino;
            return;
        }

        // Se o servidor redirecionou (res.redirected) ou retornou HTML, seguir o redirect
        if (res.redirected) {
            window.location.href = res.url;
            return;
        }

        // fallback: tentar ler texto e mostrar no console
        const texto = await res.text();
        console.error('Login falhou — resposta inesperada:', texto);
        alert('Falha ao autenticar. Veja o console para mais detalhes.');
    } catch (err) {
        console.error('Erro ao chamar API de login', err);
        alert('Erro de rede ao tentar autenticar.');
    }
}

// Expor globalmente para o onclick inline funcionar
window.performLogin = performLogin;
