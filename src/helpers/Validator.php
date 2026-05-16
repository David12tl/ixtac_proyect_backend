<?php
namespace App\Helpers;

class Validator
{
    private array $errors = [];

    public function required(array $data, array $fields): self
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $this->errors[$field][] = "El campo {$field} es requerido.";
            }
        }
        return $this;
    }

    public function email(string $email): self
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'][] = "El formato del correo electrónico es inválido.";
        }
        return $this;
    }

    public function minLength(string $value, int $min, string $field): self
    {
        if (strlen($value) < $min) {
            $this->errors[$field][] = "El campo {$field} debe tener al menos {$min} caracteres.";
        }
        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}