import { Router } from 'express';
import { generarTurnosDia, listarTurnos } from '../controllers/turno.controller';

const router = Router();

router.post('/generar', generarTurnosDia);
router.get('/', listarTurnos);

export default router;
