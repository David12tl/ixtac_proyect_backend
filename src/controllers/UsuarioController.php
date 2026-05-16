<?php
namespace App\Controllers;

use App\Models\User;
use App\Helpers\Response;
use App\Middleware\AuthMiddleware;

class UsuarioController
{
    public static function profile(): void
    {
        $userData = AuthMiddleware::verifyToken();
        $model = new User();
        $user = $model->findById($userData->data->id);

        if (!$user) {
            Response::error('Usuario no encontrado', 404);
        }

        Response::success($user, 'Perfil del usuario');
    }
}