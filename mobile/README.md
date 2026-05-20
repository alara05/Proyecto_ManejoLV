# Cuchao Mobile

App movil para usuarios/clientes. Solo incluye flujos de comprar boletos, pagar, ver boletos, ver encomiendas y perfil.

## Configuracion

1. Instala dependencias:

```bash
cd mobile
npm install
```

2. Crea `.env` o cambia `API_BASE_URL` en `App.js`.

Para emulador Android usa normalmente:

```text
http://10.0.2.2:8000/api/mobile
```

Para dispositivo fisico usa la IP de tu PC en la red:

```text
http://192.168.1.X:8000/api/mobile
```

3. Ejecuta Laravel:

```bash
php artisan migrate
php artisan serve --host=0.0.0.0 --port=8000
```

4. Ejecuta la app:

```bash
npm run android
```

## API usada

- `POST /api/mobile/register`
- `POST /api/mobile/login`
- `GET /api/mobile/profile`
- `PUT /api/mobile/profile`
- `GET /api/mobile/travels`
- `GET /api/mobile/travels/{id}`
- `POST /api/mobile/tickets`
- `GET /api/mobile/tickets`
- `GET /api/mobile/tickets/{id}`
- `POST /api/mobile/tickets/{id}/payments`
- `GET /api/mobile/encomiendas`
