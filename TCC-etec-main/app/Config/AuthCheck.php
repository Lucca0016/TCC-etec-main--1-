<?php
/**
 * app/Config/AuthCheck.php
 * Verifica se usuário está autenticado
 */

namespace App\Config;

class AuthCheck {
    
    public static function verificarAutenticacao() {
        // Inicia sessão se não estiver iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verifica se usuário está logado
        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['email'])) {
            return false;
        }
        
        return true;
    }
    
    public static function redirecionar($destino = '/TCC-etec/login') {
        if (!self::verificarAutenticacao()) {
            header("Location: $destino");
            exit;
        }
    }
    
    public static function obterUsuarioLogado() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (self::verificarAutenticacao()) {
            return [
                'id' => $_SESSION['usuario_id'],
                'email' => $_SESSION['email'],
                'nome' => $_SESSION['nome'] ?? null,
                'perfil' => $_SESSION['perfil'] ?? null
            ];
        }
        
        return null;
    }
    
    public static function verificarPerfil($perfisPermitidos = []) {
        $usuario = self::obterUsuarioLogado();
        
        if (!$usuario) {
            return false;
        }
        
        if (empty($perfisPermitidos)) {
            return true;
        }
        
        return in_array($usuario['perfil'], $perfisPermitidos);
    }
}
?>
