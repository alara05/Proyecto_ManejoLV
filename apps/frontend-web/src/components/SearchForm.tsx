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
}

export default function SearchForm({ onSearch, isLoading = false }: SearchFormProps) {
  const [formData, setFormData] = useState<SearchFormData>({
    origen: '',
    destino: '',
    maxPrecio: '',
    maxDuracion: '',
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
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
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
          Encuentra la ruta perfecta con nuestros filtros avanzados
        </p>
      </div>

      <div className={styles.formContent}>
        <div className={styles.gridTwoColumns}>
          {/* Campo Origen */}
          <div className={styles.formGroup}>
            <label htmlFor="origen" className={styles.label}>
              Origen *
            </label>
            <input
              type="text"
              id="origen"
              name="origen"
              value={formData.origen}
              onChange={handleChange}
              placeholder="Ej: Bogotá"
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
              Destino *
            </label>
            <input
              type="text"
              id="destino"
              name="destino"
              value={formData.destino}
              onChange={handleChange}
              placeholder="Ej: Medellín"
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
              Precio Máximo (COP)
            </label>
            <input
              type="number"
              id="maxPrecio"
              name="maxPrecio"
              value={formData.maxPrecio}
              onChange={handleChange}
              placeholder="Ej: 50000"
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
