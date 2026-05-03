import { Router } from 'express';
import {
  getAsientosPorTurno,
  reservarAsiento,
  liberarAsiento,
} from '../controllers/asiento.controller';

const router = Router();

// GET /turnos/:turnoId/asientos → mapa completo de asientos
router.get('/:turnoId/asientos', getAsientosPorTurno);

// POST /turnos/:turnoId/asientos/:asientoId/reservar → bloquea un asiento
router.post('/:turnoId/asientos/:asientoId/reservar', reservarAsiento);

// POST /turnos/:turnoId/asientos/:asientoId/liberar → libera un asiento
router.post('/:turnoId/asientos/:asientoId/liberar', liberarAsiento);

export default router;
