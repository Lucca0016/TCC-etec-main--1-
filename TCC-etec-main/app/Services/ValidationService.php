<?php

namespace App\Services;

/**
 * ✅ Serviço centralizado de validação
 * 
 * Centraliza regras de validação para reutilização em toda a aplicação
 * evitando duplicação de lógica de validação
 */
class ValidationService
{
    /**
     * Valida se o email tem formato válido
     * 
     * @param string $email Email a validar
     * @return bool True se válido, false caso contrário
     */
    public static function isValidEmail(string $email): bool
    {
        $email = trim($email);
        
        if (empty($email)) {
            return false;
        }

        // ✅ Usa filter_var com FILTER_VALIDATE_EMAIL para validação robusta
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Limite máximo de comprimento (RFC 5321)
        if (strlen($email) > 254) {
            return false;
        }

        return true;
    }

    /**
     * Valida se a senha atende aos requisitos mínimos
     * 
     * @param string $senha Senha a validar
     * @param int $minLength Comprimento mínimo (padrão: 8)
     * @return bool True se válida, false caso contrário
     */
    public static function isValidPassword(string $senha, int $minLength = 8): bool
    {
        // Verificar comprimento mínimo
        if (strlen($senha) < $minLength) {
            return false;
        }

        // Requer pelo menos: maiúscula, minúscula, número
        $hasUppercase = preg_match('/[A-Z]/', $senha);
        $hasLowercase = preg_match('/[a-z]/', $senha);
        $hasNumber = preg_match('/[0-9]/', $senha);

        return $hasUppercase && $hasLowercase && $hasNumber;
    }

    /**
     * Valida se a string é um nome válido
     * 
     * @param string $nome Nome a validar
     * @param int $minLength Comprimento mínimo (padrão: 3)
     * @param int $maxLength Comprimento máximo (padrão: 100)
     * @return bool True se válido, false caso contrário
     */
    public static function isValidName(string $nome, int $minLength = 3, int $maxLength = 100): bool
    {
        $nome = trim($nome);

        if (empty($nome)) {
            return false;
        }

        $length = strlen($nome);
        if ($length < $minLength || $length > $maxLength) {
            return false;
        }

        // ✅ Permite letras, espaços, hífens, apóstrofos
        $pattern = "/^[a-záàâãéèêíïóôõöúçñ\\s\\-']+$/ui";
        if (!preg_match($pattern, $nome)) {
            return false;
        }

        return true;
    }

    /**
     * Valida se a string é um URL válido
     * 
     * @param string $url URL a validar
     * @return bool True se válido, false caso contrário
     */
    public static function isValidUrl(string $url): bool
    {
        $url = trim($url);

        if (empty($url)) {
            return false;
        }

        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Valida se a string é um ISBN válido (ISBN-10 ou ISBN-13)
     * 
     * @param string $isbn ISBN a validar (com ou sem hífens)
     * @return bool True se válido, false caso contrário
     */
    public static function isValidIsbn(string $isbn): bool
    {
        // Remove hífens e espaços
        $isbn = preg_replace('/[\s\-]/', '', $isbn);

        // Apenas dígitos
        if (!preg_match('/^[0-9]{10}|[0-9]{13}$/', $isbn)) {
            return false;
        }

        return true;
    }

    /**
     * Valida se a string é um CPF válido
     * 
     * @param string $cpf CPF a validar (com ou sem formato)
     * @return bool True se válido, false caso contrário
     */
    public static function isValidCpf(string $cpf): bool
    {
        // Remove não-dígitos
        $cpf = preg_replace('/\D/', '', $cpf);

        // Deve ter exatamente 11 dígitos
        if (strlen($cpf) !== 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Calcula primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int)$cpf[$i] * (10 - $i);
        }
        $firstVerifier = 11 - ($sum % 11);
        if ($firstVerifier > 9) {
            $firstVerifier = 0;
        }
        if ((int)$cpf[9] !== $firstVerifier) {
            return false;
        }

        // Calcula segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int)$cpf[$i] * (11 - $i);
        }
        $secondVerifier = 11 - ($sum % 11);
        if ($secondVerifier > 9) {
            $secondVerifier = 0;
        }
        if ((int)$cpf[10] !== $secondVerifier) {
            return false;
        }

        return true;
    }

    /**
     * Sanitiza string removendo caracteres perigosos
     * 
     * @param string $string String a sanitizar
     * @param bool $strict Se true, remove caracteres especiais também
     * @return string String sanitizada
     */
    public static function sanitizeString(string $string, bool $strict = false): string
    {
        $string = trim($string);

        if ($strict) {
            // Remove tudo exceto letras, números e espaço
            $string = preg_replace('/[^a-záàâãéèêíïóôõöúçñ0-9\s]/ui', '', $string);
        } else {
            // Remove apenas caracteres HTML/XML perigosos
            $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        }

        return $string;
    }

    /**
     * Valida comprimento de string
     * 
     * @param string $string String a validar
     * @param int $minLength Comprimento mínimo (null = sem limite)
     * @param int $maxLength Comprimento máximo (null = sem limite)
     * @return bool True se válido, false caso contrário
     */
    public static function isValidLength(string $string, ?int $minLength = null, ?int $maxLength = null): bool
    {
        $length = strlen($string);

        if ($minLength !== null && $length < $minLength) {
            return false;
        }

        if ($maxLength !== null && $length > $maxLength) {
            return false;
        }

        return true;
    }
}
