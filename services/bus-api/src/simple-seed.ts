import 'dotenv/config';
import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

async function main() {
  console.log('Insertando datos de prueba mínimos...');

  // 1. Cooperativa
  const coop = await prisma.cooperativa.upsert({
    where: { ruc: '1790000000001' },
    update: {},
    create: {
      nombre: 'Cooperativa Express',
      ruc: '1790000000001',
      estado: 'ACTIVO',
    },
  });

  // 2. Dueño
  const dueno = await prisma.dueno.upsert({
    where: { cedula: '1720000000' },
    update: {},
    create: {
      nombre: 'Allen Tech Lead',
      cedula: '1720000000',
      estado: 'ACTIVO',
    },
  });

  // 3. Bus
  const bus = await prisma.bus.upsert({
    where: { placa: 'ABC-1234' },
    update: {},
    create: {
      placa: 'ABC-1234',
      marca: 'Hino',
      carroceria: 'Cepeda',
      modelo: 'AK8J',
      anio: 2024,
      capacidad: 40,
      cooperativaId: coop.id,
      duenoId: dueno.id,
    },
  });

  // 4. Asientos para el bus
  for (let i = 1; i <= 40; i++) {
    await prisma.asiento.upsert({
      where: { busId_numero: { busId: bus.id, numero: i } },
      update: {},
      create: { busId: bus.id, numero: i },
    });
  }

  // 5. Chofer
  const chofer = await prisma.chofer.upsert({
    where: { cedula: '1710000000' },
    update: {},
    create: {
      nombre: 'Chofer de Prueba',
      cedula: '1710000000',
      licencia: '1710000000',
      tipoLicencia: 'E',
    },
  });

  // 6. Ruta
  const ruta = await prisma.ruta.create({
    data: {
      nombre: 'Quito - Ambato',
      origen: 'Quito',
      destino: 'Ambato',
      duracionMin: 180,
      precioPasaje: 5.50,
    },
  });

  // 7. Frecuencia para LUNES
  await prisma.frecuencia.create({
    data: {
      rutaId: ruta.id,
      busId: bus.id,
      diaSemana: 'LUN',
      horaSalida: '08:00',
      horaLlegada: '11:00',
    },
  });

  console.log('¡Datos insertados con éxito!');
}

main().catch(e => console.error(e)).finally(() => prisma.$disconnect());
