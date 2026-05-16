<?php
namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthService
{
    public static function register(string $name, string $email, string $password): array
    {
        $userModel = new User();
        
        if ($userModel->findByEmail($email)) {
            throw new Exception("El correo ya está registrado.", 422);
        }

        $userId = $userModel->create($name, $email, $password);
        $user = $userModel->findById($userId);

        $token = self::generateToken($user);

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public static function login(string $email, string $password): array
    {
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Credenciales incorrectas.", 401);
        }

        $userData = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'avatar' => $user['avatar']
        ];

        $token = self::generateToken($userData);

        return [
            'user' => $userData,
            'token' => $token
        ];
    }

    public static function validateToken(string $token): object
    {
        try {
            return JWT::decode($token, new Key(JWT_SECRET, JWT_ALGO));
        } catch (Exception $e) {
            throw new Exception("Token inválido: " . $e->getMessage(), 401);
        }
    }

    private static function generateToken(array $user): string
    {
        $payload = [
            'iat' => time(),
            'exp' => time() + JWT_EXP,
            'data' => [
                'id' => $user['id'],
                'email' => $user['email']
            ]
        ];

        return JWT::encode($payload, JWT_SECRET, JWT_ALGO);
    }
}