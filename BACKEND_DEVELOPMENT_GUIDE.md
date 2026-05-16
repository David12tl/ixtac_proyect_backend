# Guía de Desarrollo Backend para EcoIxtac

Este documento detalla los requisitos técnicos que debe cumplir el backend PHP para ser compatible con el servicio de API de Next.js.

## 1. Configuración del Servidor

### CORS (Cross-Origin Resource Sharing)
Es **indispensable** que el backend permita peticiones desde el origen del frontend (ej. `http://localhost:3000`).

- **Headers requeridos:**
  - `Access-Control-Allow-Origin: http://localhost:3000` (o `*` para desarrollo)
  - `Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS`
  - `Access-Control-Allow-Headers: Content-Type, Authorization, Accept`

### Formato de Respuesta
El `apiClient` espera respuestas en formato **JSON**.

## 2. Endpoints Requeridos (Base: `/api`)

### Autenticación

#### POST `/login`
- **Payload:** `{ "email": "...", "password": "..." }`
- **Respuesta Exitosa (200 OK):**
```json
{
  "user": {
    "id": 1,
    "name": "Nombre Usuario",
    "email": "usuario@ejemplo.com",
    "avatar": "url_opcional"
  },
  "token": "string_jwt_token_aqui"
}
```

#### POST `/register`
- **Payload:** `{ "name": "...", "email": "...", "password": "..." }`
- **Respuesta Exitosa (201 Created):**
```json
{
  "user": { "id": 2, "name": "...", "email": "..." },
  "token": "string_jwt_token_aqui"
}
```

## 3. Seguridad y Tokens

El frontend enviará el token en cada petición protegida mediante el header:
`Authorization: Bearer <token>`

El backend debe:
1. Validar que el token sea vigente.
2. Identificar al usuario a través del token.

## 4. Manejo de Errores (Estándar esperado)

Cuando algo sale mal, el backend debe responder con un código de estado HTTP adecuado (4xx o 5xx) y un JSON con este formato:

```json
{
  "message": "Descripción legible del error",
  "errors": {
    "campo_especifico": ["Mensaje de validación 1", "Mensaje 2"]
  }
}
```

### Códigos de estado sugeridos:
- `401 Unauthorized`: Token inválido o credenciales incorrectas.
- `422 Unprocessable Entity`: Errores de validación (ej. email ya registrado).
- `404 Not Found`: Recurso no encontrado.
- `500 Internal Server Error`: Errores inesperados en el código PHP.

## 5. Ejemplo de Base de Datos Sugerida (SQL)

Para soportar lo actual, la tabla de usuarios debería ser:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 6. Recomendación de Tecnologías PHP

Si estás construyendo desde cero, se recomienda:
- **Laravel:** El framework más compatible y fácil para crear APIs JSON.
- **Slim Framework:** Si prefieres algo minimalista.
- **JWT-auth:** Librería estándar para manejar tokens en PHP.
