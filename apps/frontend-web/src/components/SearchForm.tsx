'use client';

import { useState } from 'react';
import styles from './SearchForm.module.css';

interface SearchFormProps {
  onSearch?: (formData: SearchFormData) => void;
  isLoading?: boolean;
}

export interface SearchFormData {
  origen: string;
  destino: string;
  maxPrecio: number | string;
  maxDuracion: number | string;
  fecha: string;
  horaInicio: string;
  horaFin: string;
  incluirParadas: boolean;
}

export default function SearchForm({ onSearch, isLoading = false }: SearchFormProps) {
  const [formData, setFormData] = useState<SearchFormData>({
    origen: '',
    destino: '',
    maxPrecio: '',
    maxDuracion: '',
    fecha: '',
    horaInicio: '',
    horaFin: '',
    incluirParadas: true,
  });

  const [errors, setErrors] = useState<Record<string, string>>({});

  const validateForm = (): boolean => {
    const newErrors: Record<string, string> = {};

    if (!formData.origen.trim()) {
      newErrors.origen = 'El origen es requerido';
    }

    if (!formData.destino.trim()) {
      newErrors.destino = 'El destino es requerido';
    }

    if (formData.origen.trim() === formData.destino.trim()) {
      newErrors.destino = 'El destino debe ser diferente al origen';
    }

    if (formData.maxPrecio && isNaN(Number(formData.maxPrecio))) {
      newErrors.maxPrecio = 'El precio debe ser un número válido';
    }

    if (formData.maxDuracion && isNaN(Number(formData.maxDuracion))) {
      newErrors.maxDuracion = 'La duración debe ser un número válido';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value, type, checked } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value,
    }));
    // Limpiar error del campo cuando el usuario empieza a escribir
    if (errors[name]) {
      setErrors((prev) => ({
        ...prev,
        [name]: '',
      }));
    }
  };

  const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    if (!validateForm()) {
      return;
    }

    onSearch?.(formData);
  };

  return (
    <form className={styles.searchForm} onSubmit={handleSubmit}>
      <div className={styles.formHeader}>
        <h2 className={styles.title}>Busca tu Ruta</h2>
        <p className={styles.subtitle}>
          Encuentra la ruta perfecta 
        </p>
      </div>

      <div className={styles.formContent}>
        <div className={styles.gridTwoColumns}>
          {/* Campo Origen */}
          <div className={styles.formGroup}>
            <label htmlFor="origen" className={styles.label}>
              Origen 
            </label>
            <input
              type="text"
              id="origen"
              name="origen"
              value={formData.origen}
              onChange={handleChange}
              placeholder="Ej: Quito"
              className={`${styles.input} ${errors.origen ? styles.inputError : ''}`}
              disabled={isLoading}
            />
            {errors.origen && (
              <span className={styles.errorMessage}>{errors.origen}</span>
            )}
          </div>

          {/* Campo Destino */}
          <div className={styles.formGroup}>
            <label htmlFor="destino" className={styles.label}>
              Destino 
            </label>
            <input
              type="text"
              id="destino"
              name="destino"
              value={formData.destino}
              onChange={handleChange}
              placeholder="Ej: Guayaquil"
              className={`${styles.input} ${errors.destino ? styles.inputError : ''}`}
              disabled={isLoading}
            />
            {errors.destino && (
              <span className={styles.errorMessage}>{errors.destino}</span>
            )}
          </div>
        </div>

        <div className={styles.gridTwoColumns}>
          {/* Campo Precio Máximo */}
          <div className={styles.formGroup}>
            <label htmlFor="maxPrecio" className={styles.label}>
              Precio Máximo ($USD)
            </label>
            <input
              type="number"
              id="maxPrecio"
              name="maxPrecio"
              value={formData.maxPrecio}
              onChange={handleChange}
              placeholder="Ej: 30"
              className={`${styles.input} ${errors.maxPrecio ? styles.inputError : ''}`}
              disabled={isLoading}
              min="0"
            />
            {errors.maxPrecio && (
              <span className={styles.errorMessage}>{errors.maxPrecio}</span>
            )}
          </div>

          {/* Campo Duración Máxima */}
          <div className={styles.formGroup}>
            <label htmlFor="maxDuracion" className={styles.label}>
              Duración Máxima (horas)
            </label>
            <input
              type="number"
              id="maxDuracion"
              name="maxDuracion"
              value={formData.maxDuracion}
              onChange={handleChange}
              placeholder="Ej: 8"
              className={`${styles.input} ${errors.maxDuracion ? styles.inputError : ''}`}
              disabled={isLoading}
              min="0"
            />
            {errors.maxDuracion && (
              <span className={styles.errorMessage}>{errors.maxDuracion}</span>
            )}
          </div>
        </div>

        <div className={styles.gridTwoColumns}>
          {/* Campo Fecha */}
          <div className={styles.formGroup}>
            <label htmlFor="fecha" className={styles.label}>
               Fecha de Salida
            </label>
            <input
              type="date"
              id="fecha"
              name="fecha"
              value={formData.fecha}
              onChange={handleChange}
              className={`${styles.input}`}
              disabled={isLoading}
            />
          </div>

          {/* Campo Rango Horario */}
          <div className={styles.formGroup}>
            <label className={styles.label}> Rango Horario</label>
            <div className={styles.timeRange}>
              <input
                type="time"
                id="horaInicio"
                name="horaInicio"
                value={formData.horaInicio}
                onChange={handleChange}
                className={styles.input}
                disabled={isLoading}
                placeholder="Desde"
              />
              <span className={styles.separator}>→</span>
              <input
                type="time"
                id="horaFin"
                name="horaFin"
                value={formData.horaFin}
                onChange={handleChange}
                className={styles.input}
                disabled={isLoading}
                placeholder="Hasta"
              />
            </div>
          </div>
        </div>

        {/* Checkbox Paradas Intermedias */}
        <div className={styles.checkboxGroup}>
          <label className={styles.checkboxLabel}>
            <input
              type="checkbox"
              name="incluirParadas"
              checked={formData.incluirParadas}
              onChange={handleChange}
              disabled={isLoading}
              className={styles.checkbox}
            />
            <span> Rutas con paradas intermedias</span>
          </label>
          <p className={styles.checkboxHint}>
            Desactiva para ver solo rutas directas
          </p>
        </div>
      </div>

      <div className={styles.formFooter}>
        <button
          type="submit"
          className={`${styles.submitButton} ${isLoading ? styles.buttonLoading : ''}`}
          disabled={isLoading}
        >
          {isLoading ? (
            <>
              <span className={styles.spinner}></span>
              Buscando...
            </>
          ) : (
            'Buscar Rutas'
          )}
        </button>
      </div>
    </form>
  );
}
