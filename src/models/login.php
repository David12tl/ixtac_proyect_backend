<?php
// ============================================================================
// 1. BLINDAJE TOTAL DE CORS (Debe ir en la línea 1, antes de cualquier otra cosa)
// ============================================================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// ============================================================================
// 2. MANEJO DE PREFLIGHT (La petición OPTIONS que el navegador manda primero)
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ============================================================================
// 3. CAPTURA DE ERRORES CRÍTICOS (Si algo falla, responde en JSON con CORS activo)
// ============================================================================
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        echo json_encode([
            "error" => "Error interno en el servidor PHP",
            "detalles" => $error['message']
        ]);
    }
});

// Desactivar salida de errores crudos en pantalla para que no rompan el JSON del cliente
error_reporting(0);
ini_set('display_errors', 0);

// ============================================================================
// 4. INCLUSIÓN DE DEPENDENCIAS (Verifica que la ruta a 'vendor' sea correcta)
// ============================================================================
// Si 'login.php' está dentro de una carpeta llamada 'api', '__DIR__ . '/../vendor/...' es correcto.
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    http_response_code(500);
    echo json_encode(["error" => "No se encontró el autoloader de Composer. Verifica las rutas en el FTP."]);
    exit();
}

use Firebase\JWT\JWT;

// ============================================================================
// 5. CONEXIÓN A LA BASE DE DATOS (Verifica tus credenciales de Alwaysdata)
// ============================================================================
$host = 'mysql-ixtacproyect.alwaysdata.net'; 
$db   = 'ixtacproyect_2026';
$user = 'tu_usuario_always'; // Cambia por tu usuario real de Alwaysdata
$pass = 'tu_password_always'; // Cambia por tu contraseña real de Alwaysdata

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión a la base de datos"]);
    exit();
}

// ============================================================================
// 6. PROCESAMIENTO DEL LOGIN
// ============================================================================
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan credenciales obligatorias"]);
    exit();
}

// Buscar al usuario activo
$stmt = $pdo->prepare("SELECT id, password_hash, role_id FROM users WHERE email = ? AND is_active = 1");
$stmt->execute([$data['email']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($data['password'], $user['password_hash'])) {
    
    // Tus constantes de JWT configuradas previamente
    define('JWT_SECRET', 'KJZ6qN3QKV1fA1KJFsbY9FX2BZuLZbTgVetH3ioT+Q0=');
    define('JWT_ALGO', 'HS256');
    define('JWT_EXP', 3600 * 24); // 24 Horas

    $payload = [
        'iat' => time(),
        'exp' => time() + JWT_EXP,
        'data' => [
            'id' => $user['id'],
            'role' => $user['role_id']
        ]
    ];

    $jwt = JWT::encode($payload, JWT_SECRET, JWT_ALGO);

    http_response_code(200);
    echo json_encode([
        "mensaje" => "Login exitoso",
        "token" => $jwt,
        "usuario" => [
            "id" => $user['id'],
            "role" => $user['role_id']
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Email o contraseña incorrectos"]);
}