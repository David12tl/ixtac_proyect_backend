<?php
namespace App\Middleware;

class CorsMiddleware {
    public static function handle() {
        // Cambiamos el * por tu dominio real de Vercel
        header("Access-Control-Allow-Origin: https://ixtac-proyect.vercel.app");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Credentials: true");
        header("Content-Type: application/json; charset=UTF-8");

        // Si el navegador pregunta mediante OPTIONS, respondemos 200 y cortamos la ejecución
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }
}