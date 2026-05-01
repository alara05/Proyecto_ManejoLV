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
  try {
    const ruta = await prisma.ruta.create({
      data: req.body,
    });
    res.status(201).json(ruta);
  } catch (error) {
    res.status(500).json({ error: 'Error al crear la ruta' });
  }
};

export const updateRuta = async (req: Request, res: Response) => {
  const { id } = req.params;
  try {
    const ruta = await prisma.ruta.update({
      where: { id: Number(id) },
      data: req.body,
    });
    res.json(ruta);
  } catch (error) {
    res.status(500).json({ error: 'Error al actualizar la ruta' });
  }
};
export const searchRutas = async (req: Request, res: Response) => {
  const { origen, destino, maxPrecio, maxDuracion } = req.query;

  try {
    // Validar parámetros numéricos para evitar errores de Prisma
    const priceFilter = maxPrecio ? parseFloat(String(maxPrecio)) : undefined;
    const durationFilter = maxDuracion ? parseInt(String(maxDuracion)) : undefined;

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
      },
    });

    // Filtrar para asegurar que el origen esté antes que el destino en la ruta
    // Usamos : any para evitar errores de tipado si el cliente de Prisma no está inicializado
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

      // Validamos que ambos existan y que el origen esté antes que el destino
      return indexOrigen !== -1 && indexDestino !== -1 && indexOrigen < indexDestino;
    });

    res.json(rutasFiltradas);
  } catch (error) {
    console.error('Error en searchRutas:', error);
    res.status(500).json({ error: 'Error al buscar las rutas' });
  }
};
