import { Request, Response } from 'express';
import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

/**
 * US09: Validar si una cédula puede aplicar a un descuento (3ra edad/discapacidad)
 * Regla: Solo un descuento cada 24 horas por cédula.
 */
export const validarDescuento = async (req: Request, res: Response) => {
  const { cedula, tipoDescuento } = req.body;

  if (!cedula || !tipoDescuento) {
    return res.status(400).json({ error: 'Cédula y tipo de descuento son obligatorios' });
  }

  try {
    // 1. Calcular el límite de 24 horas atrás
    const hace24Horas = new Date();
    hace24Horas.setHours(hace24Horas.getHours() - 24);

    // 2. Buscar si existe un boleto con descuento para esa cédula en las últimas 24h
    // TECH-DEBT: Usar Raw SQL porque `prisma generate` falla en Windows por bug del .wasm
    // TODO Sprint 2: Reemplazar por prisma.boleto.findFirst() cuando el entorno esté estabilizado
    const boletoReciente = await prisma.$queryRawUnsafe<Array<{creado_en: Date}>>(
      `SELECT creado_en FROM boletos_validacion 
       WHERE cedula_pasajero = $1 AND tipo_tarifa != 'NORMAL' AND creado_en >= $2 
       LIMIT 1`,
      cedula,
      hace24Horas
    );

    if (boletoReciente.length > 0) {
      return res.status(403).json({
        allowed: false,
        message: 'Esta cédula ya utilizó un beneficio de descuento en las últimas 24 horas.',
        ultimoUso: boletoReciente[0].creado_en,
      });
    }

    // 3. Si no hay uso reciente, se permite el descuento
    res.json({
      allowed: true,
      message: 'Descuento permitido.',
    });
  } catch (error) {
    console.error('Error al validar descuento:', error);
    res.status(500).json({ error: 'Error interno al validar el descuento' });
  }
};
