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
