
# Integración con Backend PHP

Este proyecto utiliza una arquitectura de servicios para la comunicación con el backend PHP.

## Configuración

### Variables de Entorno
Crea o edita el archivo `.env.local` en la raíz del proyecto y añade la URL de tu API:

```env
NEXT_PUBLIC_API_URL=http://tu-backend.com/api
```

## Estructura de Archivos

- `src/lib/api-client.ts`: Utilidad base para peticiones HTTP (fetch). Maneja automáticamente:
  - Headers JSON.
  - Token de autenticación (Bearer) si existe en `localStorage`.
  - Manejo de errores común.
- `src/lib/constants.ts`: Constantes globales, incluyendo la URL base de la API.
- `src/types/`: Definiciones de TypeScript para los datos.
- `src/services/`: Capa de servicios que utiliza el `apiClient` para comunicarse con endpoints específicos.

## Cómo usar el API Client

```typescript
import { apiClient } from '@/lib/api-client';

// Ejemplo GET
const data = await apiClient.get<MyDataType>('/endpoint');

// Ejemplo POST
const result = await apiClient.post<ResponseType>('/endpoint', {
  key: 'value'
});
```

## Autenticación

El `authService` maneja el login, registro y persistencia del token.

```typescript
import { authService } from '@/services/auth.service';

// Login
try {
  const response = await authService.login('email@example.com', 'password');
  console.log('Usuario logueado:', response.user);
} catch (error) {
  console.error('Error de login:', error.message);
}

// Logout
authService.logout();
```

## Manejo de Errores

Las peticiones fallidas lanzan un objeto con el siguiente formato:

```typescript
{
  status: number;
  message: string;
  errors?: Record<string, string[]>; // Para errores de validación de Laravel/PHP
}
```
