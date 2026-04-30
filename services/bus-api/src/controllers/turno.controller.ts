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

    // 1. Buscar frecuencias para ese día
    const frecuencias = await prisma.frecuencia.findMany({
      where: { diaSemana },
      include: { ruta: true, bus: true },
    });

    if (frecuencias.length === 0) {
      return res.status(404).json({ message: `No hay frecuencias configuradas para el día ${diaSemana}` });
    }

    const turnosCreados = [];

    // 2. Generar un Turno por cada frecuencia
    for (const freq of frecuencias) {
      // Verificar si ya existe el turno para evitar duplicados
      const existe = await prisma.turno.findFirst({
        where: {
          fecha: fechaObj,
          horaInicio: freq.horaSalida,
          rutaId: freq.rutaId,
        },
      });

      if (!existe) {
        // Suponemos un chofer por defecto por ahora (o el primero activo)
        const chofer = await prisma.chofer.findFirst({ where: { estado: 'ACTIVO' } });

        if (!chofer) {
          return res.status(500).json({ error: 'No hay choferes activos para asignar' });
        }

        const nuevoTurno = await prisma.turno.create({
          data: {
            fecha: fechaObj,
            horaInicio: freq.horaSalida,
            horaFin: freq.horaLlegada,
            busId: freq.busId,
            rutaId: freq.rutaId,
            choferId: chofer.id,
            estado: 'PENDIENTE',
          },
        });

        // 3. Inicializar Asientos para este Turno (US08)
        const asientosBus = await prisma.asiento.findMany({
          where: { busId: freq.busId },
        });

        const asientosTurnoData = asientosBus.map((asiento) => ({
          turnoId: nuevoTurno.id,
          asientoId: asiento.id,
          estado: 'DISPONIBLE' as any,
        }));

        await prisma.asientoTurno.createMany({
          data: asientosTurnoData,
        });

        turnosCreados.push(nuevoTurno);
      }
    }

    res.status(201).json({
      message: `Proceso completado para el día ${diaSemana}`,
      generados: turnosCreados.length,
      turnos: turnosCreados,
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
