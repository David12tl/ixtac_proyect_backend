<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email)
    {
        // 🚀 SOLUCIÓN: Renombramos 'password_hash' a 'password' y 'avatar_url' a 'avatar' para que AuthService los entienda
        $stmt = $this->db->prepare("SELECT id, name, email, password_hash AS password, avatar_url AS avatar FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(string $name, string $email, string $password): int
    {
        // 🚀 SOLUCIÓN: Cambiamos 'password' por 'password_hash' que es tu columna real
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt->execute([$name, $email, $hashedPassword]);
        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id)
    {
        // 🚀 SOLUCIÓN: Traemos los datos correctos usando el alias para el avatar
        $stmt = $this->db->prepare("SELECT id, name, email, avatar_url AS avatar FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}