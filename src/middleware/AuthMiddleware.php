<?php
namespace App\Middleware;

use App\Helpers\Response;
use App\Services\AuthService;

class AuthMiddleware
{
    /**
     * Verifica el token JWT del encabezado Authorization.
     * Si el token es válido, devuelve el payload decodificado.
     * En caso contrario, termina la ejecución con error 401.
     */
    public static function verifyToken(): object
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!preg_match('/^Bearer\s+(.+)$/', $authHeader, $matches)) {
            Response::error('Token no proporcionado', 401);
        }

        try {
            return AuthService::validateToken($matches[1]);
        } catch (\Exception $e) {
            Response::error('Token inválido o expirado', 401);
        }
    }
}