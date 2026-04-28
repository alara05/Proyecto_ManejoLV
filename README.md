# Proyecto SaaS - Sistema de Tickets de Bus

Bienvenido al repositorio central del proyecto. Esta arquitectura utiliza **NPM Workspaces** y **Turborepo** para gestionar microservicios de forma independiente y eficiente.

## Estructura del Monorepo
- `apps/`: Frontends (Web y PWA).
- `services/`: Microservicios (auth, bus, ticket).
- `packages/database/`: Esquemas compartidos de Prisma y scripts SQL.

## Configuración Inicial (Setup)

1. **Instalar Dependencias:**
   Desde la raíz, ejecuta:
   ```bash
   npm install
   ```

2. **Levantar Base de Datos:**
   Asegúrate de tener Docker Desktop corriendo y ejecuta:
   ```bash
   docker compose up -d
   ```

3. **Sincronizar Esquemas:**
   Cada vez que el esquema de base de datos cambie, ejecuta (dentro del microservicio correspondiente):
   ```bash
   npx prisma generate --schema=../../packages/database/prisma/bus-schema.prisma
   ```

## Desarrollo
Para arrancar todos los servicios en modo desarrollo simultáneamente:
```bash
npx turbo run dev
```

## Reglas de Oro
1. **Aislamiento:** No instales dependencias en la raíz. Hazlo siempre dentro de la carpeta del microservicio.
2. **Pull antes de Push:** Siempre haz pull de `develop` antes de subir cambios en el esquema.
3. **Commits:** Usa el estándar `tipo(servicio): descripcion` (ej: `feat(bus-api): agregar crud de buses`).

porfavor haran bien 
