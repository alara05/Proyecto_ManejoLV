import { Request, Response } from 'express';
import { PrismaClient, DiaSemana } from '@prisma/client';

const prisma = new PrismaClient();

const diasMapeo: Record<number, DiaSemana> = {
  0: 'DOM',
  1: 'LUN',
  2: 'MAR',
  3: 'MIE',
  4: 'JUE',
  5: 'VIE',
  6: 'SAB',
};

export const generarTurnosDia = async (req: Request, res: Response) => {
  const { fecha } = req.body; // Formato YYYY-MM-DD

  if (!fecha) {
    return res.status(400).json({ error: 'La fecha es obligatoria' });
  }

  try {
    const fechaObj = new Date(fecha);
    const diaSemana = diasMapeo[fechaObj.getUTCDay()];
    const diaDelAnio = Math.floor((fechaObj.getTime() - new Date(fechaObj.getUTCFullYear(), 0, 0).getTime()) / 1000 / 60 / 60 / 24);

    // 1. Obtener todas las cooperativas para procesar por separado
    const cooperativas = await prisma.cooperativa.findMany({
      where: { estado: 'ACTIVO' }
    });

    const turnosCreadosGlobal = [];

    for (const coop of cooperativas) {
      // 2. Buscar frecuencias de esta cooperativa para el día
      const frecuencias = await prisma.frecuencia.findMany({
        where: { 
          diaSemana,
          bus: { cooperativaId: coop.id } // Solo frecuencias de esta coop
        },
        orderBy: { horaSalida: 'asc' },
        include: { ruta: true }
      });

      if (frecuencias.length === 0) continue;

      // 3. Obtener buses activos de la cooperativa para rotación
      const busesDisponibles = await prisma.bus.findMany({
        where: { 
          cooperativaId: coop.id,
          estado: 'ACTIVO'
        },
        orderBy: { id: 'asc' }
      });

      if (busesDisponibles.length === 0) continue;

      // 4. Lógica de Rotación (Round-Robin basado en el día del año)
      // Esto asegura que si hay más buses que frecuencias, los buses roten cada día.
      const totalBuses = busesDisponibles.length;
      const startIndex = diaDelAnio % totalBuses;

      for (let i = 0; i < frecuencias.length; i++) {
        const freq = frecuencias[i];
        
        // Seleccionamos el bus usando el índice rotado
        // Si hay menos buses que frecuencias (error de config), se repiten.
        // Si hay más buses que frecuencias, los que sobran tienen "Día de Parada".
        const busAsignado = busesDisponibles[(startIndex + i) % totalBuses];

        // Verificar duplicados: Si ya hay UN turno para esta frecuencia y fecha, no creamos otro.
        // Quitamos busId de aquí para que no cree duplicados con buses distintos.
        const existe = await prisma.turno.findFirst({
          where: {
            fecha: fechaObj,
            horaInicio: freq.horaSalida,
            rutaId: freq.rutaId,
          },
        });

        if (!existe) {
          const chofer = await prisma.chofer.findFirst({ where: { estado: 'ACTIVO' } });
          if (!chofer) continue;

          const nuevoTurno = await prisma.turno.create({
            data: {
              fecha: fechaObj,
              horaInicio: freq.horaSalida,
              horaFin: freq.horaLlegada,
              busId: busAsignado.id,
              rutaId: freq.rutaId,
              choferId: chofer.id,
              estado: 'PENDIENTE',
            },
          });

          // 5. Inicializar Asientos para este Turno (US08)
          const asientosBus = await prisma.asiento.findMany({
            where: { busId: busAsignado.id },
          });

          await (prisma as any).asientoTurno.createMany({
            data: asientosBus.map((asiento: any) => ({
              turnoId: nuevoTurno.id,
              asientoId: asiento.id,
              estado: 'DISPONIBLE',
            })),
          });

          turnosCreadosGlobal.push(nuevoTurno);
        }
      }
    }

    res.status(201).json({
      message: `Proceso de rotación completado para el día ${diaSemana}`,
      generados: turnosCreadosGlobal.length,
      cooperativasProcesadas: cooperativas.length
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Error interno al generar turnos' });
  }
};

export const listarTurnos = async (req: Request, res: Response) => {
  try {
    const turnos = await prisma.turno.findMany({
      include: {
        ruta: true,
        bus: true,
        chofer: true,
      },
      orderBy: { fecha: 'asc' },
    });
    res.json(turnos);
  } catch (error) {
    res.status(500).json({ error: 'Error al listar turnos' });
  }
};
