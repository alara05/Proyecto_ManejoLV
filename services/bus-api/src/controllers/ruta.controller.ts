import { Request, Response } from 'express';
import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

export const getAllRutas = async (req: Request, res: Response) => {
  try {
    const rutas = await prisma.ruta.findMany();
    res.json(rutas);
  } catch (error) {
    res.status(500).json({ error: 'Error al obtener las rutas' });
  }
};

export const createRuta = async (req: Request, res: Response) => {
  const { nombre, origen, destino, duracionMin, precioPasaje, paradas } = req.body;
  
  try {
    const ruta = await prisma.ruta.create({
      data: {
        nombre,
        origen,
        destino,
        duracionMin,
        precioPasaje,
        paradas: paradas ? {
          create: paradas.map((p: any) => ({
            nombre: p.nombre,
            latitud: p.latitud,
            longitud: p.longitud,
            orden: p.orden
          }))
        } : undefined
      },
      include: { paradas: true }
    });
    res.status(201).json(ruta);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Error al crear la ruta con sus paradas' });
  }
};

export const updateRuta = async (req: Request, res: Response) => {
  const { id } = req.params;
  const { nombre, origen, destino, duracionMin, precioPasaje, paradas } = req.body;

  try {
    const ruta = await prisma.ruta.update({
      where: { id: Number(id) },
      data: {
        nombre,
        origen,
        destino,
        duracionMin,
        precioPasaje,
        // Para simplificar, borramos las paradas viejas y creamos las nuevas si vienen en el body
        paradas: paradas ? {
          deleteMany: {},
          create: paradas.map((p: any) => ({
            nombre: p.nombre,
            latitud: p.latitud,
            longitud: p.longitud,
            orden: p.orden
          }))
        } : undefined
      },
      include: { paradas: true }
    });
    res.json(ruta);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Error al actualizar la ruta' });
  }
};
export const searchRutas = async (req: Request, res: Response) => {
  const { origen, destino, maxPrecio, maxDuracion, fecha, horaInicio, horaFin, incluirParadas } = req.query;

  try {
    // Validar parámetros numéricos para evitar errores de Prisma
    const priceFilter = maxPrecio ? parseFloat(String(maxPrecio)) : undefined;
    const durationFilter = maxDuracion ? parseInt(String(maxDuracion)) : undefined;
    const incluirParadasIntermedias = incluirParadas !== 'false'; // Default true

    const rutas = await prisma.ruta.findMany({
      where: {
        AND: [
          origen
            ? {
                OR: [
                  { origen: { contains: String(origen), mode: 'insensitive' } },
                  { paradas: { some: { nombre: { contains: String(origen), mode: 'insensitive' } } } },
                ],
              }
            : {},
          destino
            ? {
                OR: [
                  { destino: { contains: String(destino), mode: 'insensitive' } },
                  { paradas: { some: { nombre: { contains: String(destino), mode: 'insensitive' } } } },
                ],
              }
            : {},
          priceFilter && !isNaN(priceFilter) ? { precioPasaje: { lte: priceFilter } } : {},
          durationFilter && !isNaN(durationFilter) ? { duracionMin: { lte: durationFilter } } : {},
        ],
      },
      include: {
        paradas: {
          orderBy: {
            orden: 'asc',
          },
        },
        turnos: fecha
          ? {
              where: {
                fecha: new Date(String(fecha)),
                ...(horaInicio && { horaInicio: { gte: String(horaInicio) } }),
                ...(horaFin && { horaInicio: { lte: String(horaFin) } }),
              },
              include: {
                bus: true,
              },
            }
          : {
              include: {
                bus: true,
              },
            },
      },
    });

    // Filtrar para asegurar que el origen esté antes que el destino en la ruta
    const rutasFiltradas = rutas.filter((ruta: any) => {
      if (!origen || !destino) return true;

      const lowerOrigen = String(origen).toLowerCase();
      const lowerDestino = String(destino).toLowerCase();

      // Origen principal es posición 0
      let indexOrigen = ruta.origen.toLowerCase().includes(lowerOrigen) ? 0 : -1;
      // Destino final es posición muy alta (999)
      let indexDestino = ruta.destino.toLowerCase().includes(lowerDestino) ? 999 : -1;

      // Buscar en las paradas intermedias
      ruta.paradas.forEach((p: any) => {
        if (indexOrigen === -1 && p.nombre.toLowerCase().includes(lowerOrigen)) {
          indexOrigen = p.orden;
        }
        if (p.nombre.toLowerCase().includes(lowerDestino)) {
          indexDestino = p.orden;
        }
      });

      // Si no incluye paradas intermedias, solo rutas directas
      if (!incluirParadasIntermedias && (indexOrigen > 0 || indexDestino < 999)) {
        return false;
      }

      // Validamos que ambos existan y que el origen esté antes que el destino
      return indexOrigen !== -1 && indexDestino !== -1 && indexOrigen < indexDestino;
    });

    res.json(rutasFiltradas);
  } catch (error) {
    console.error('Error en searchRutas:', error);
    res.status(500).json({ error: 'Error al buscar las rutas' });
  }
};
