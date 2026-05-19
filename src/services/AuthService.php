<?php
namespace App\Services;

use App\Config\Database;
use Exception;

class AuthService 
{
    /**
     * Inicio de Sesión adaptado a la tabla 'users'
     */
    public static function login(string $email, string $password): array 
    {
        $db = Database::getConnection();
        
        // 🚀 CORREGIDO: Tabla 'users'
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        // 🚀 CORREGIDO: Columna 'password_hash'
        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new Exception("Credenciales incorrectas.");
        }

        // Verificar si el usuario está activo antes de dejarlo entrar
        if (isset($user['is_active']) && !$user['is_active']) {
            throw new Exception("Esta cuenta se encuentra desactivada.");
        }

        // Actualizar último inicio de sesión (Opcional, guarda la fecha actual)
        $update = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
        $update->execute(['id' => $user['id']]);

        $token = bin2hex(random_bytes(32));

        return [
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role_id' => $user['role_id'] ?? 2
            ]
        ];
    }
    /**
     * Registro adaptado para generar un ID autogestionado de manera segura
     */
    public static function register(array $data): array 
    {
        $db = Database::getConnection();

        // 1. Validar si el correo ya existe
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $data['email']]);
        if ($stmt->fetch()) {
            throw new Exception("El correo electrónico ya está registrado.");
        }

        // 🚀 SOLUCIÓN AL ERROR #1833: Obtenemos el último ID manual y le sumamos 1
        $idQuery = $db->query("SELECT MAX(id) as max_id FROM users");
        $idResult = $idQuery->fetch(\PDO::FETCH_ASSOC);
        $nextId = ($idResult && $idResult['max_id']) ? intval($idResult['max_id']) + 1 : 1;

        // Encriptar la contraseña para 'password_hash'
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        // 2. Insertar mapeando el ID generado manualmente
        $query = "INSERT INTO users (
                    id, email, password_hash, name, phone, location, 
                    role_id, is_verified, is_active, rating, 
                    total_reviews, total_services, created_at, updated_at
                  ) VALUES (
                    :id, :email, :password_hash, :name, :phone, :location, 
                    :role_id, :is_verified, :is_active, :rating, 
                    :total_reviews, :total_services, NOW(), NOW()
                  )";

        $stmt = $db->prepare($query);
        $stmt->execute([
            'id'             => $nextId, // Forzamos el ID correlativo secuencial
            'email'          => $data['email'],
            'password_hash'  => $hashedPassword,
            'name'           => $data['name'],
            'phone'          => $data['phone'] ?? null,
            'location'       => $data['location'] ?? null,
            'role_id'        => 2,
            'is_verified'    => 0,
            'is_active'      => 1,
            'rating'         => 0.0,
            'total_reviews'  => 0,
            'total_services' => 0
        ]);

        $userId = $nextId;
        $token = bin2hex(random_bytes(32));

        return [
            'token' => $token,
            'user' => [
                'id'    => $userId,
                'name'  => $data['name'],
                'email' => $data['email'],
                'role_id' => 2
            ]
        ];
    }
}