import { Request, Response } from 'express';
import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

/**
 * US08: Obtener todos los asientos de un turno con su estado actual.
 * GET /turnos/:turnoId/asientos
 * Responde con el mapa completo de asientos (DISPONIBLE, RESERVADO, OCUPADO).
 */
export const getAsientosPorTurno = async (req: Request, res: Response) => {
  const { turnoId } = req.params;

  try {
    // Verificar que el turno existe
    const turno = await prisma.turno.findUnique({
      where: { id: Number(turnoId) },
      include: {
        bus: { select: { capacidad: true, placa: true, marca: true } },
      },
    });

    if (!turno) {
      return res.status(404).json({ error: 'Turno no encontrado' });
    }

    // Traer asientos con su estado en este turno
    const asientosTurno = await prisma.asientoTurno.findMany({
      where: { turnoId: Number(turnoId) },
      include: {
        asiento: {
          select: {
            id: true,
            numero: true,
            fila: true,
            tipo: true,
          },
        },
      },
      orderBy: {
        asiento: { numero: 'asc' },
      },
    });

    // Formatear respuesta para que el frontend pinte el mapa fácilmente
    const mapa = asientosTurno.map((at) => ({
      asientoTurnoId: at.id,
      asientoId: at.asiento.id,
      numero: at.asiento.numero,
      fila: at.asiento.fila,
      tipo: at.asiento.tipo,
      estado: at.estado, // DISPONIBLE | RESERVADO | OCUPADO | VACIO
    }));

    res.json({
      turnoId: turno.id,
      bus: turno.bus,
      totalAsientos: mapa.length,
      disponibles: mapa.filter((a) => a.estado === 'DISPONIBLE').length,
      asientos: mapa,
    });
  } catch (error) {
    console.error('Error al obtener asientos del turno:', error);
    res.status(500).json({ error: 'Error interno al obtener el mapa de asientos' });
  }
};

/**
 * US08: Reservar (bloquear) un asiento específico en un turno.
 * POST /turnos/:turnoId/asientos/:asientoId/reservar
 * Regla: Solo se puede reservar si el estado actual es DISPONIBLE.
 */
export const reservarAsiento = async (req: Request, res: Response) => {
  const { turnoId, asientoId } = req.params;

  try {
    // Buscar el registro AsientoTurno específico
    const asientoTurno = await prisma.asientoTurno.findFirst({
      where: {
        turnoId: Number(turnoId),
        asientoId: Number(asientoId),
      },
      include: {
        asiento: { select: { numero: true } },
      },
    });

    if (!asientoTurno) {
      return res.status(404).json({ error: 'Asiento no encontrado en este turno' });
    }

    // Regla de negocio: solo se puede reservar si está DISPONIBLE
    if (asientoTurno.estado !== 'DISPONIBLE') {
      return res.status(409).json({
        error: 'El asiento no está disponible',
        estadoActual: asientoTurno.estado,
        asiento: asientoTurno.asiento.numero,
      });
    }

    // Marcar como RESERVADO
    const actualizado = await prisma.asientoTurno.update({
      where: { id: asientoTurno.id },
      data: { estado: 'RESERVADO' },
    });

    res.json({
      message: `Asiento ${asientoTurno.asiento.numero} reservado exitosamente`,
      asientoTurnoId: actualizado.id,
      estado: actualizado.estado,
    });
  } catch (error) {
    console.error('Error al reservar asiento:', error);
    res.status(500).json({ error: 'Error interno al reservar el asiento' });
  }
};

/**
 * US08: Liberar un asiento reservado (en caso de cancelación antes del pago).
 * POST /turnos/:turnoId/asientos/:asientoId/liberar
 */
export const liberarAsiento = async (req: Request, res: Response) => {
  const { turnoId, asientoId } = req.params;

  try {
    const asientoTurno = await prisma.asientoTurno.findFirst({
      where: {
        turnoId: Number(turnoId),
        asientoId: Number(asientoId),
      },
      include: {
        asiento: { select: { numero: true } },
      },
    });

    if (!asientoTurno) {
      return res.status(404).json({ error: 'Asiento no encontrado en este turno' });
    }

    // Solo se puede liberar un asiento RESERVADO (no uno OCUPADO con boleto)
    if (asientoTurno.estado !== 'RESERVADO') {
      return res.status(409).json({
        error: 'Solo se pueden liberar asientos en estado RESERVADO',
        estadoActual: asientoTurno.estado,
      });
    }

    const actualizado = await prisma.asientoTurno.update({
      where: { id: asientoTurno.id },
      data: { estado: 'DISPONIBLE' },
    });

    res.json({
      message: `Asiento ${asientoTurno.asiento.numero} liberado exitosamente`,
      asientoTurnoId: actualizado.id,
      estado: actualizado.estado,
    });
  } catch (error) {
    console.error('Error al liberar asiento:', error);
    res.status(500).json({ error: 'Error interno al liberar el asiento' });
  }
};
