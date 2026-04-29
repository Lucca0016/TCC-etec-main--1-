package com.example.appetec

import android.content.Context
import android.content.SharedPreferences
import org.json.JSONObject
import java.io.OutputStreamWriter
import java.net.HttpURLConnection
import java.net.URL

/**
 * Cliente API para conectar ao backend da Etec
 * Uso: ApiClient(this).login(email, senha) { resultado -> ... }
 */
class ApiClient(private val context: Context) {

    // ⚠️ SUBSTITUA pelo IP do seu servidor (use IP local para teste)
    private val BASE_URL = "http://192.168.1.100/api/"

    private val prefs: SharedPreferences by lazy {
        context.getSharedPreferences("app_etec_prefs", Context.MODE_PRIVATE)
    }

    // ============ TOKEN ============

    var accessToken: String?
        get() = prefs.getString("access_token", null)
        set(value) = prefs.edit().putString("access_token", value).apply()

    var refreshToken: String?
        get() = prefs.getString("refresh_token", null)
        set(value) = prefs.edit().putString("refresh_token", value).apply()

    var userPapel: String?
        get() = prefs.getString("user_papel", null)
        set(value) = prefs.edit().putString("user_papel", value).apply()

    fun isLoggedIn(): Boolean = accessToken != null

    fun logout() {
        accessToken = null
        refreshToken = null
        userPapel = null
    }

    // ============ AUTH ============

    fun login(email: String, password: String, profile: String? = null): Result<JSONObject> {
        return try {
            val body = JSONObject().apply {
                put("email", email)
                put("password", password)
                profile?.let { put("profile", it) }
            }

            val response = post("login", body)

            if (response.getBoolean("ok")) {
                accessToken = response.getString("access_token")
                refreshToken = response.getString("refresh_token")

                val user = response.getJSONObject("user")
                userPapel = user.getString("papel")

                Result.success(response)
            } else {
                Result.failure(Exception(response.getString("message")))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    fun refresh(): Boolean {
        val token = refreshToken ?: return false

        return try {
            val params = "refresh_token=$token"
            val response = post("refresh", params, isFormUrlEncoded = true)

            if (response.getBoolean("ok")) {
                accessToken = response.getString("access_token")
                true
            } else {
                logout()
                false
            }
        } catch (e: Exception) {
            logout()
            false
        }
    }

    // ============ API CALLS ============

    /**
     * Faz requisição GET autenticada
     * Exemplo: api.get("admin/usuarios") { json -> ... }
     */
    fun get(endpoint: String): JSONObject {
        val url = URL("${BASE_URL}$endpoint")
        val conn = url.openConnection() as HttpURLConnection

        conn.requestMethod = "GET"
        conn.setRequestProperty("Authorization", "Bearer $accessToken")
        conn.setRequestProperty("Content-Type", "application/json")

        val responseCode = conn.responseCode
        val response = conn.inputStream.bufferedReader().readText()

        if (responseCode == 401) {
            // Token expirado, tenta refresh
            if (refresh()) {
                return get(endpoint) // Retry
            }
            throw Exception("Unauthorized")
        }

        return JSONObject(response)
    }

    /**
     * Faz requisição POST autenticada
     * Exemplo: api.post("admin/livros", JSONObject().put("titulo", "Livro X"))
     */
    fun post(endpoint: String, body: Any, isFormUrlEncoded: Boolean = false): JSONObject {
        val url = URL("${BASE_URL}$endpoint")
        val conn = url.openConnection() as HttpURLConnection

        conn.requestMethod = "POST"
        conn.doOutput = true

        if (isFormUrlEncoded) {
            conn.setRequestProperty("Content-Type", "application/x-www-form-urlencoded")
            OutputStreamWriter(conn.outputStream).use { writer ->
                writer.write(body.toString())
                writer.flush()
            }
        } else {
            conn.setRequestProperty("Content-Type", "application/json")
            OutputStreamWriter(conn.outputStream).use { writer ->
                writer.write(body.toString())
                writer.flush()
            }
        }

        accessToken?.let {
            conn.setRequestProperty("Authorization", "Bearer $it")
        }

        val responseCode = conn.responseCode
        val response = conn.inputStream.bufferedReader().readText()

        if (responseCode == 401) {
            if (refresh()) {
                return post(endpoint, body, isFormUrlEncoded)
            }
            throw Exception("Unauthorized")
        }

        return JSONObject(response)
    }

    // ============ EXEMPLOS DE USO ============

    /*
    // No seu Activity/Fragment:
    val api = ApiClient(this)

    // Login
    val resultado = api.login("aluno@etec.com", "senha123")
    resultado.onSuccess { json ->
        val nome = json.getJSONObject("user").getString("nome")
        Toast.makeText(this, "Olá, $nome!", Toast.LENGTH_SHORT).show()
    }.onFailure { erro ->
        Toast.makeText(this, erro.message, Toast.LENGTH_SHORT).show()
    }

    // Listar usuários (requer token)
    try {
        val usuarios = api.get("admin/usuarios")
        // processar resposta
    } catch (e: Exception) {
        // erro
    }

    // Logout
    api.logout()
    */
}