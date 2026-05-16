<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Experiencia
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM experiencias");
        return $stmt->fetchAll();
    }

    public function getById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM experiencias WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}