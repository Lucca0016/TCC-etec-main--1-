package com.example.appetec

import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import org.json.JSONObject
import java.io.OutputStreamWriter
import java.net.HttpURLConnection
import java.net.URL

class MainActivity : AppCompatActivity() {

    // Substitua pelo IP do seu servidor
    private val BASE_URL = "http://SEU_IP_SERVIDOR/api/"

    private lateinit var etEmail: EditText
    private lateinit var etSenha: EditText
    private lateinit var btnLogin: Button
    private lateinit var tvResultado: TextView

    // Tokens (guarde em SharedPreferences em produção)
    private var accessToken: String? = null
    private var refreshToken: String? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        etEmail = findViewById(R.id.etEmail)
        etSenha = findViewById(R.id.etSenha)
        btnLogin = findViewById(R.id.btnLogin)
        tvResultado = findViewById(R.id.tvResultado)

        btnLogin.setOnClickListener {
            val email = etEmail.text.toString().trim()
            val senha = etSenha.text.toString()

            if (email.isEmpty() || senha.isEmpty()) {
                Toast.makeText(this, "Preencha email e senha", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            login(email, senha)
        }
    }

    private fun login(email: String, senha: String) {
        CoroutineScope(Dispatchers.Main).launch {
            try {
                val resultado = withContext(Dispatchers.IO) {
                    fazerLogin(email, senha)
                }

                if (resultado["ok"] == true) {
                    accessToken = resultado.getString("access_token")
                    refreshToken = resultado.getString("refresh_token")

                    val user = resultado.getJSONObject("user")
                    tvResultado.text = "Login OK!\nBem-vindo: ${user.getString("nome")}\nPapel: ${user.getString("papel")}"

                    Toast.makeText(this@MainActivity, "Login realizado!", Toast.LENGTH_SHORT).show()
                } else {
                    val msg = resultado.getString("message")
                    tvResultado.text = "Erro: $msg"
                    Toast.makeText(this@MainActivity, msg, Toast.LENGTH_SHORT).show()
                }
            } catch (e: Exception) {
                tvResultado.text = "Erro: ${e.message}"
                Toast.makeText(this@MainActivity, "Erro de conexão", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun fazerLogin(email: String, senha: String): JSONObject {
        val url = URL("${BASE_URL}login")
        val conexao = url.openConnection() as HttpURLConnection

        conexao.requestMethod = "POST"
        conexao.setRequestProperty("Content-Type", "application/json")
        conexao.doOutput = true

        // Corpo da requisição
        val jsonBody = """
            {
                "email": "$email",
                "password": "$senha"
            }
        """.trimIndent()

        OutputStreamWriter(conexao.outputStream).use { writer ->
            writer.write(jsonBody)
            writer.flush()
        }

        val responseCode = conexao.responseCode
        val response = conexao.inputStream.bufferedReader().readText()

        return JSONObject(response)
    }

    // Exemplo: fazer uma requisição autenticada
    private fun fazerRequisicaoAutenticada(endpoint: String): JSONObject {
        val url = URL("${BASE_URL}$endpoint")
        val conexao = url.openConnection() as HttpURLConnection

        conexao.requestMethod = "GET"
        conexao.setRequestProperty("Authorization", "Bearer $accessToken")
        conexao.setRequestProperty("Content-Type", "application/json")

        val responseCode = conexao.responseCode
        val response = conexao.inputStream.bufferedReader().readText()

        return JSONObject(response)
    }

    // Exemplo: refresh do token
    private fun refreshToken(): Boolean {
        if (refreshToken == null) return false

        return try {
            val url = URL("${BASE_URL}refresh")
            val conexao = url.openConnection() as HttpURLConnection

            conexao.requestMethod = "POST"
            conexao.setRequestProperty("Content-Type", "application/x-www-form-urlencoded")
            conexao.doOutput = true

            val params = "refresh_token=$refreshToken"
            OutputStreamWriter(conexao.outputStream).use { writer ->
                writer.write(params)
                writer.flush()
            }

            val response = JSONObject(conexao.inputStream.bufferedReader().readText())

            if (response.getBoolean("ok")) {
                accessToken = response.getString("access_token")
                true
            } else {
                false
            }
        } catch (e: Exception) {
            false
        }
    }
}