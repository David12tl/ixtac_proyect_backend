<?php
namespace App\Controllers;

use App\Models\Reserva;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Middleware\AuthMiddleware;

class ReservaController
{
    public static function store(): void
    {
        $userData = AuthMiddleware::verifyToken();
        $data = json_decode(file_get_contents('php://input'), true);

        $validator = (new Validator())
            ->required($data, ['experiencia_id', 'fecha', 'personas']);

        if ($validator->fails()) {
            Response::error('Datos inválidos', 422, $validator->getErrors());
        }

        $model = new Reserva();
        try {
            $reservaId = $model->create(
                $userData->data->id,
                $data['experiencia_id'],
                $data['fecha'],
                $data['personas']
            );
            Response::success(['id' => $reservaId], 'Reserva creada exitosamente', 201);
        } catch (\Exception $e) {
            Response::error('Error al crear la reserva: ' . $e->getMessage(), 500);
        }
    }

    public static function myReservations(): void
    {
        $userData = AuthMiddleware::verifyToken();
        $model = new Reserva();
        $reservas = $model->getByUserId($userData->data->id);
        Response::success($reservas, 'Tus reservas');
    }
}