import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

async function main() {
  console.log('Iniciando el sembrado de datos (Seed)...');

  // 1. Crear Cooperativa de prueba
  const coop = await prisma.cooperativa.upsert({
    where: { ruc: '1790000000001' },
    update: {},
    create: {
      nombre: 'Cooperativa TransAmazonas',
      ruc: '1790000000001',
      estado: 'ACTIVO',
    },
  });
  console.log('Cooperativa creada:', coop.nombre);

  // 2. Crear Dueño de prueba
  const dueno = await prisma.dueno.upsert({
    where: { cedula: '1720000000' },
    update: {},
    create: {
      nombre: 'Allen Developer',
      cedula: '1720000000',
      telefono: '0999999999',
      estado: 'ACTIVO',
    },
  });
  console.log('Dueño creado:', dueno.nombre);

  // 3. Crear Bus de prueba
  const bus = await prisma.bus.create({
    data: {
      placa: 'ABC-1234',
      marca: 'Hino',
      carroceria: 'Cepeda',
      modelo: 'AK8J',
      anio: 2023,
      capacidad: 45,
      color: 'Azul y Blanco',
      cooperativaId: coop.id,
      duenoId: dueno.id,
      estado: 'ACTIVO',
    },
  });
  console.log('Bus creado con placa:', bus.placa);

  // 4. Crear Ruta de prueba
  const ruta = await prisma.ruta.create({
    data: {
      nombre: 'Quito - Guayaquil (Directo)',
      origen: 'Quito',
      destino: 'Guayaquil',
      duracionMin: 480,
      precioPasaje: 15.50,
    },
  });
  console.log('Ruta creada:', ruta.nombre);

  console.log('¡Seed completado con éxito!');
}

main()
  .catch((e) => {
    console.error('Error en el Seed:', e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
