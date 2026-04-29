# API Client para App Android - Etec

Este projeto contém um cliente API pronto para conectar seu aplicativo Android ao backend da Etec.

## Arquivos

| Arquivo | Descrição |
|---------|-----------|
| `ApiClient.kt` | Cliente HTTP completo com autenticação JWT |
| `MainActivity.kt` | Exemplo de uso com tela de login |
| `res/layout/activity_main.xml` | Layout da tela de login |

---

## Como usar

### 1. Configure o IP do servidor

No arquivo `ApiClient.kt`, linha 16, substitua pelo IP do seu computador:

```kotlin
private val BASE_URL = "http://192.168.1.100/api/"
```

> **Dica:** Para descobrir seu IP no Windows, execute `ipconfig` no CMD. Use o IPv4 da sua rede WiFi.

### 2. Adicione permissões no AndroidManifest.xml

```xml
<uses-permission android:name="android.permission.INTERNET" />
```

### 3. Exemplo de uso no seu app

```kotlin
// Criar instância (passe o contexto)
val api = ApiClient(this)

// Login
val resultado = api.login("aluno@etec.com", "senha123")

resultado.onSuccess { response ->
    val user = response.getJSONObject("user")
    Toast.makeText(this, "Olá, ${user.getString("nome")}!", Toast.LENGTH_SHORT).show()
    
    // Verificar perfil
    when (api.userPapel) {
        "admin" -> irParaTelaAdmin()
        "aluno" -> irParaTelaAluno()
        "professor" -> irParaTelaProfessor()
    }
}.onFailure { erro ->
    Toast.makeText(this, erro.message, Toast.LENGTH_SHORT).show()
}

// Verificar se está logado
if (api.isLoggedIn()) {
    // Usuário já tem token válido
}

// Fazer requisição autenticada
try {
    val usuarios = api.get("admin/usuarios")
    // processar resposta...
} catch (e: Exception) {
    // erro ou token expirado
}

// Logout
api.logout()
```

---

## Endpoints disponíveis

| Método | Endpoint | Descrição | Auth |
|--------|----------|-----------|------|
| POST | `/api/login` | Login | ❌ |
| POST | `/api/refresh` | Renovar token | ❌ |
| POST | `/api/logout` | Logout | ❌ |
| GET | `/api/check` | Verificar token | ✅ |
| GET | `/api/admin/usuarios` | Listar usuários | ✅ |
| POST | `/api/admin/usuarios` | Criar usuário | ✅ |
| POST | `/api/admin/usuarios/deletar` | Deletar usuário | ✅ |
| GET | `/api/admin/livros` | Listar livros | ✅ |
| POST | `/api/admin/livros` | Criar livro | ✅ |
| POST | `/api/admin/livros/deletar` | Deletar livro | ✅ |
| GET | `/api/admin/noticias` | Listar notícias | ✅ |
| POST | `/api/admin/noticias` | Criar notícia | ✅ |
| POST | `/api/admin/noticias/deletar` | Deletar notícia | ✅ |

---

## Dicas importantes

### Para testar no emulador
- Use `10.0.2.2` para acessar o localhost do computador host
- Exemplo: `http://10.0.2.2/api/login`

### Para testar no celular físico
- Celular e computador devem estar na **mesma rede WiFi**
- Use o IP local do computador (não use localhost)

### Para produção
- Use HTTPS (configure SSL no servidor)
- Armazene tokens de forma segura (SharedPreferences ou EncryptedSharedPreferences)

---

## Problemas comuns

| Erro | Solução |
|------|---------|
| `Connection refused` | Verifique o IP e se o servidor está rodando |
| `Network on main thread` | Use coroutines (já incluso no exemplo) |
| `Unauthorized` (401) | Token expirou, chame `api.refresh()` |
| `Cleartext traffic not permitted` | Adicione `android:usesCleartextTraffic="true"` no manifest |