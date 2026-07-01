<?php

namespace Itech\Core;

/**
 * Manejo de token CSRF para proteger formularios.
 *
 * Nota: actualmente no se detectan referencias a esta clase en el proyecto.
 * Antes de eliminar, confirme que no la requiere alguna ruta o vista.
 */
class Csrf
{
    public static function token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validate(?string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return isset($_SESSION['csrf_token'])
            && is_string($token)
            && hash_equals($_SESSION['csrf_token'], $token);
    }
}
