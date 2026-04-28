import { Request, Response } from 'express';
import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

export const getAllBuses = async (req: Request, res: Response) => {
  try {
    const buses = await prisma.bus.findMany();
    res.json(buses);
  } catch (error) {
    res.status(500).json({ error: 'Error al obtener los buses' });
  }
};

export const createBus = async (req: Request, res: Response) => {
  try {
    const bus = await prisma.bus.create({
      data: req.body,
    });
    res.status(201).json(bus);
  } catch (error) {
    res.status(500).json({ error: 'Error al crear el bus' });
  }
};

export const updateBus = async (req: Request, res: Response) => {
  const { id } = req.params;
  try {
    const bus = await prisma.bus.update({
      where: { id: Number(id) },
      data: req.body,
    });
    res.json(bus);
  } catch (error) {
    res.status(500).json({ error: 'Error al actualizar el bus' });
  }
};
