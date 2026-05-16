<?php
namespace App\Helpers;

class Response
{
    public static function success($data = null, string $message = 'Operación exitosa', int $code = 200): void
    {
        http_response_code($code);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    public static function error(string $message = 'Error en la operación', int $code = 400, array $errors = []): void
    {
        http_response_code($code);
        $response = [
            'status' => 'error',
            'message' => $message
        ];
        
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        echo json_encode($response);
        exit;
    }
}