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
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create(string $name, string $email, string $password): int
    {
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt->execute([$name, $email, $hashedPassword]);
        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id)
    {
        $stmt = $this->db->prepare("SELECT id, name, email, avatar FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}