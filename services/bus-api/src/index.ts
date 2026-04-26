import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import { PrismaClient } from '@prisma/client';

import busRoutes from './routes/bus.routes';
import rutaRoutes from './routes/ruta.routes';

dotenv.config();

const app = express();
const prisma = new PrismaClient();
const port = process.env.PORT || 3002;

app.use(cors());
app.use(express.json());

// Routes
app.use('/buses', busRoutes);
app.use('/rutas', rutaRoutes);

// Health Check
app.get('/health', (req, res) => {
  res.json({ status: 'ok', service: 'bus-api' });
});

app.listen(port, () => {
  console.log(`Bus API corriendo en el puerto ${port}`);
});
