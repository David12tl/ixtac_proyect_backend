<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Helpers\Response;
use App\Helpers\Validator;

class AuthController 
{
    /**
     * Procesa el inicio de sesión de usuarios
     */
    public function login() 
    {
        $data = [];

        // 1. Intentamos leer si vinieron datos por JSON crudo (POST nativo)
        $json = file_get_contents('php://input');
        if (!empty($json)) {
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        // 🚀 RESPALDO CRÍTICO: Si el cuerpo llegó vacío por la redirección GET de Alwaysdata, barremos $_POST y $_GET
        if (empty($data['email']) || empty($data['password'])) {
            $data['email']    = $data['email'] ?? $_POST['email'] ?? $_GET['email'] ?? '';
            $data['password'] = $data['password'] ?? $_POST['password'] ?? $_GET['password'] ?? '';
        }

        // 2. Instanciamos y ejecutamos tu Validador Nativo
        $validator = new Validator();
        $validator->required($data, ['email', 'password']);
        
        if (!empty($data['email'])) {
            $validator->email($data['email']);
        }

        // Si la validación falla, respondemos un 400 con los errores estructurados
        if ($validator->fails()) {
            http_response_code(400);
            echo json_encode([
                "status"  => "error",
                "message" => "Datos inválidos",
                "errors"  => $validator->getErrors()
            ]);
            return;
        }

        // 3. Flujo de autenticación hacia el servicio de base de datos
        try {
            $result = AuthService::login($data['email'], $data['password']);
            http_response_code(200);
            echo json_encode([
                "status"  => "success",
                "mensaje" => "Inicio de sesión exitoso",
                "token"   => $result['token'] ?? null,
                "user"    => $result['user'] ?? null
            ]);
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode([
                "status"  => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    /**
     * Procesa el registro de nuevos usuarios en la tabla 'users'
     */
    public function register() 
    {
        $data = [];

        // 1. Intentamos leer los parámetros si vinieron por JSON crudo desde Next.js
        $json = file_get_contents('php://input');
        if (!empty($json)) {
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        // 🚀 RESPALDO CRÍTICO: Recolectamos todos los nuevos parámetros (incluyendo teléfono y ubicación) 
        // ante cualquier posible redirección HTTP del servidor
        if (empty($data['email']) || empty($data['password'])) {
            $data['name']     = $data['name'] ?? $_POST['name'] ?? $_GET['name'] ?? '';
            $data['email']    = $data['email'] ?? $_POST['email'] ?? $_GET['email'] ?? '';
            $data['password'] = $data['password'] ?? $_POST['password'] ?? $_GET['password'] ?? '';
            $data['phone']    = $data['phone'] ?? $_POST['phone'] ?? $_GET['phone'] ?? '';
            $data['location'] = $data['location'] ?? $_POST['location'] ?? $_GET['location'] ?? '';
        }

        // 2. Validación con tu validador nativo
        $validator = new Validator();
        // Campos estrictamente obligatorios para la cuenta
        $validator->required($data, ['name', 'email', 'password']);
        
        if (!empty($data['email'])) {
            $validator->email($data['email']);
        }
        if (!empty($data['password'])) {
            $validator->minLength($data['password'], 6, 'password');
        }

        if ($validator->fails()) {
            http_response_code(400);
            echo json_encode([
                "status"  => "error",
                "message" => "Datos inválidos",
                "errors"  => $validator->getErrors()
            ]);
            return;
        }

        // 3. Envío del paquete completo al servicio para impactar la tabla 'users'
        try {
            $result = AuthService::register($data);
            http_response_code(201);
            echo json_encode([
                "status"  => "success",
                "mensaje" => "Usuario registrado con éxito",
                "token"   => $result['token'] ?? null,
                "user"    => $result['user'] ?? null
            ]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                "status"  => "error",
                "message" => $e->getMessage()
            ]);
        }
    }
}