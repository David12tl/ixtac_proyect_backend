<?php
namespace App\Controllers;

use App\Models\Experiencia;
use App\Helpers\Response;

class ExperienciaController
{
    public static function index(): void
    {
        $model = new Experiencia();
        $experiencias = $model->getAll();
        Response::success($experiencias, 'Experiencias recuperadas');
    }

    public static function show(int $id): void
    {
        $model = new Experiencia();
        $experiencia = $model->getById($id);

        if (!$experiencia) {
            Response::error('Experiencia no encontrada', 404);
        }

        Response::success($experiencia, 'Detalle de experiencia');
    }
}