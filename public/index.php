
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config/constans.php';

use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;
use App\Helpers\Response;


CorsMiddleware::handle();

// El resto del enrutamiento...
$requestUri = $_GET['url'] ?? '';
$requestUri = trim($requestUri, '/');
$method = $_SERVER['REQUEST_METHOD'];

switch (true) {
  // Rutas de autenticación
    case $requestUri === 'api/register' && $method === 'POST':
        AuthController::register();
        break;

    case $requestUri === 'api/login' && $method === 'POST':
        AuthController::login();
        break;

    // Rutas de experiencias
    case $requestUri === 'api/experiencias' && $method === 'GET':
        \App\Controllers\ExperienciaController::index();
        break;

    case preg_match('/^api\/experiencias\/(\d+)$/', $requestUri, $matches) && $method === 'GET':
        \App\Controllers\ExperienciaController::show((int)$matches[1]);
        break;

    // Rutas de reservas
    case $requestUri === 'api/reservas' && $method === 'POST':
        \App\Controllers\ReservaController::store();
        break;

    case $requestUri === 'api/mis-reservas' && $method === 'GET':
        \App\Controllers\ReservaController::myReservations();
        break;

    case $requestUri === 'api/perfil' && $method === 'GET':
        \App\Controllers\UsuarioController::profile();
        break;

    // Ruta de prueba protegida
    case $requestUri === 'api/check' && $method === 'GET':
        $userData = AuthMiddleware::verifyToken();
        Response::success(['user' => $userData], 'Token válido');
        break;

    default:
        Response::error('Ruta no encontrada', 404);
}