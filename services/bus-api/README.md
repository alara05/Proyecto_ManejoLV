# Bus API - Microservicio de Gestión de Buses

Este microservicio se encarga de la gestión de rutas, horarios, flotas y disponibilidad de asientos.

## Requisitos
- Docker Desktop (corriendo)
- Node.js v18+

## Instalación y Configuración
1. Asegúrate de que la base de datos esté arriba desde la raíz:
   ```bash
   docker compose up -d
   ```
2. Instala las dependencias (desde la raíz):
   ```bash
   npm install
   ```
3. Genera el cliente de Prisma:
   ```bash
   npx prisma generate --schema=../../packages/database/prisma/bus-schema.prisma
   ```

## Desarrollo
Para encender el servicio en modo desarrollo:
```bash
npm run dev
```

## Base de Datos (Sembrado)
Para poblar la base de datos local con datos de prueba (Cooperativas, Dueños, Buses y Rutas):
```bash
npm run seed
```
