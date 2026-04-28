import { Router } from 'express';
import * as busController from '../controllers/bus.controller';

const router = Router();

router.get('/', busController.getAllBuses);
router.post('/', busController.createBus);
router.put('/:id', busController.updateBus);

export default router;
