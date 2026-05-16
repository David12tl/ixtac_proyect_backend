<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Reserva
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(int $userId, int $experienciaId, string $fecha, int $personas): int
    {
        $stmt = $this->db->prepare("INSERT INTO reservas (usuario_id, experiencia_id, fecha, personas, estado) VALUES (?, ?, ?, ?, 'pendiente')");
        $stmt->execute([$userId, $experienciaId, $fecha, $personas]);
        return (int)$this->db->lastInsertId();
    }

    public function getByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, e.nombre as experiencia_nombre 
            FROM reservas r 
            JOIN experiencias e ON r.experiencia_id = e.id 
            WHERE r.usuario_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}