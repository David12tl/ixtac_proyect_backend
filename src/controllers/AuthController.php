<?php
namespace App\Controllers;

use App\Helpers\Response;
use App\Helpers\Validator;
use App\Services\AuthService;

class AuthController
{
    public static function register(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $validator = (new Validator())
            ->required($data, ['name', 'email', 'password'])
            ->email($data['email'] ?? '')
            ->minLength($data['password'] ?? '', 6, 'password');

        if ($validator->fails()) {
            Response::error('Datos inválidos', 422, $validator->getErrors());
        }

        try {
            $result = AuthService::register(
                $data['name'],
                $data['email'],
                $data['password']
            );
            Response::success($result, 'Registro exitoso', 201);
        } catch (\Exception $e) {
            $code = $e->getCode();
            Response::error($e->getMessage(), ($code >= 400 && $code < 600) ? $code : 500);
        }
    }

    public static function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $validator = (new Validator())
            ->required($data, ['email', 'password'])
            ->email($data['email'] ?? '');

        if ($validator->fails()) {
            Response::error('Datos inválidos', 422, $validator->getErrors());
        }

        try {
            $result = AuthService::login($data['email'], $data['password']);
            Response::success($result, 'Inicio de sesión exitoso');
        } catch (\Exception $e) {
            $code = $e->getCode();
            Response::error($e->getMessage(), ($code >= 400 && $code < 600) ? $code : 500);
        }
    }
}