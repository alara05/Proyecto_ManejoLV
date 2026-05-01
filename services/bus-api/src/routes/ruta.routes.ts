import { Router } from 'express';
import * as rutaController from '../controllers/ruta.controller';

const router = Router();

router.get('/', rutaController.getAllRutas);
router.get('/search', rutaController.searchRutas);
router.post('/', rutaController.createRuta);
router.put('/:id', rutaController.updateRuta);

export default router;
