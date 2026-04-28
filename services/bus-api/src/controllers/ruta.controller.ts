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
