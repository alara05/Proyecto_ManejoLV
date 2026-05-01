'use client';

import { useState } from 'react';
import SearchForm, { SearchFormData } from '@/components/SearchForm';
import RouteList, { Route } from '@/components/RouteList';
import styles from './page.module.css';

export default function Home() {
  const [routes, setRoutes] = useState<Route[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [searchParams, setSearchParams] = useState<{
    origen: string;
    destino: string;
  } | null>(null);
  const [hasSearched, setHasSearched] = useState(false);

  const handleSearch = async (formData: SearchFormData) => {
    setIsLoading(true);
    setError(null);
    setRoutes([]);
    setHasSearched(true);
    setSearchParams({
      origen: formData.origen,
      destino: formData.destino,
    });

    try {
      const queryParams = new URLSearchParams({
        origen: formData.origen,
        destino: formData.destino,
      });

      if (formData.maxPrecio) {
        queryParams.append('maxPrecio', formData.maxPrecio.toString());
      }

      if (formData.maxDuracion) {
        queryParams.append('maxDuracion', formData.maxDuracion.toString());
      }

      // Intentar con diferentes puertos/rutas
      const apiUrl = `http://localhost:3001/api/rutas/search?${queryParams}`;

      const response = await fetch(apiUrl);

      if (!response.ok) {
        if (response.status === 404) {
          setError('No se encontraron rutas con esos criterios');
        } else {
          throw new Error(
            `Error ${response.status}: ${response.statusText}`
          );
        }
      } else {
        const data = await response.json();
        setRoutes(Array.isArray(data) ? data : data.data || []);

        if (Array.isArray(data) && data.length === 0) {
          setError('No se encontraron rutas con esos criterios');
        }
      }
    } catch (err) {
      console.error('Error fetching routes:', err);
      setError(
        err instanceof Error
          ? err.message
          : 'Error al conectar con el servidor. Asegúrate de que el backend está corriendo en http://localhost:3001'
      );
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <main className={styles.main}>
      <div className={styles.mainWrapper}>
        {/* Sección de búsqueda */}
        <section className={styles.searchSection}>
          <SearchForm onSearch={handleSearch} isLoading={isLoading} />
        </section>

        {/* Sección de resultados */}
        {hasSearched && (
          <section className={styles.resultsSection}>
            <RouteList
              routes={routes}
              isLoading={isLoading}
              error={error || undefined}
              searchParams={searchParams || undefined}
            />
          </section>
        )}
      </div>
    </main>
  );
}
