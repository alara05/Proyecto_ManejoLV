import { Router } from 'express';
import { validarDescuento } from '../controllers/descuento.controller';

const router = Router();

router.post('/validar', validarDescuento);

export default router;
