<?php
// 🚀 1. Configuración limpia de cabeceras CORS
namespace App\Middleware {
    class CorsMiddleware {
        public static function handle() {
            header("Access-Control-Allow-Origin: https://ixtac-proyect.vercel.app");
            header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
            header("Access-Control-Allow-Credentials: true");
            header("Content-Type: application/json; charset=UTF-8");
        }
    }
}

// 📦 2. Flujo Principal del Sistema
namespace {
    \App\Middleware\CorsMiddleware::handle();

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';
    }

    require_once __DIR__ . '/../src/config/database.php';
    require_once __DIR__ . '/../src/helpers/validator.php';
    require_once __DIR__ . '/../src/services/authservice.php';
    require_once __DIR__ . '/../src/helpers/response.php';

    if (file_exists(__DIR__ . '/../src/models/user.php')) {
        require_once __DIR__ . '/../src/models/user.php';
    }

    require_once __DIR__ . '/../src/controllers/authcontroller.php';

    if (file_exists(__DIR__ . '/../src/config/constans.php')) {
        require_once __DIR__ . '/../src/config/constans.php';
    }

    // 🚀 LIMPIADOR DE RUTAS INTELIGENTE
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $requestUri = str_replace('index.php/', '', $requestUri); // Remueve index.php si aparece
    $requestUri = trim($requestUri, '/'); 
    $method = $_SERVER['REQUEST_METHOD'];

    // 🧭 ENRUTADOR PRINCIPAL ABSOLUTO (Tolera métodos cruzados por redirecciones)
    switch (true) {
        case $requestUri === 'api/ping':
            echo json_encode(["status" => "ok", "mensaje" => "¡Backend respondiendo correctamente!"]);
            break;

        case $requestUri === 'api/login':
            $authController = new \App\Controllers\AuthController();
            $authController->login();
            break;

        case $requestUri === 'api/register':
            $authController = new \App\Controllers\AuthController();
            $authController->register();
            break;

        default:
            http_response_code(404);
            echo json_encode([
                "error" => "Ruta no encontrada", 
                "uri" => $requestUri,
                "metodo" => $method
            ]);
            break;
    }
}