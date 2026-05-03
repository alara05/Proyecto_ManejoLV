'use client';

import styles from './RouteList.module.css';

export interface Route {
  id: string;
  origen: string;
  destino: string;
  precio: number;
  duracion: number;
  paradas?: string[];
  horaSalida?: string;
  horaLlegada?: string;
  turnos?: Array<{
    id: number;
    fecha: string;
    horaInicio: string;
    horaFin?: string;
    estado: string;
    bus?: {
      placa: string;
      marca: string;
    };
  }>;
}

interface RouteListProps {
  routes: Route[];
  isLoading?: boolean;
  error?: string;
  searchParams?: {
    origen: string;
    destino: string;
  };
}

export default function RouteList({
  routes,
  isLoading = false,
  error,
  searchParams,
}: RouteListProps) {
  const isIntermediateStop = (ruta: Route): boolean => {
    if (!searchParams) return false;
    const { origen: searchOrigen, destino: searchDestino } = searchParams;
    return !!(
      (ruta.origen !== searchOrigen || ruta.destino !== searchDestino) &&
      ruta.paradas &&
      ruta.paradas.includes(searchOrigen) &&
      ruta.paradas.includes(searchDestino)
    );
  };

  if (isLoading) {
    return (
      <div className={styles.loadingContainer}>
        <div className={styles.spinner}></div>
        <p className={styles.loadingText}>Buscando rutas disponibles...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className={styles.errorContainer}>
        <div className={styles.errorIcon}>⚠️</div>
        <h3 className={styles.errorTitle}>Error en la búsqueda</h3>
        <p className={styles.errorMessage}>{error}</p>
      </div>
    );
  }

  if (routes.length === 0) {
    return (
      <div className={styles.emptyContainer}>
        <div className={styles.emptyIcon}>🔍</div>
        <h3 className={styles.emptyTitle}>No se encontraron rutas</h3>
        <p className={styles.emptyMessage}>
          Intenta con diferentes criterios de búsqueda
        </p>
      </div>
    );
  }

  return (
    <div className={styles.routeList}>
      <div className={styles.listHeader}>
        <h2 className={styles.listTitle}>
          Rutas Disponibles ({routes.length})
        </h2>
        <p className={styles.listSubtitle}>
          Haz clic en una ruta para más detalles
        </p>
      </div>

      <div className={styles.routesGrid}>
        {routes.map((route) => {
          const isIntermediate = isIntermediateStop(route);
          return (
            <div key={route.id} className={styles.routeCard}>
              <div className={styles.cardHeader}>
                <div className={styles.routeInfo}>
                  <div className={styles.routePath}>
                    <span className={styles.city}>{route.origen}</span>
                    <span className={styles.arrow}>→</span>
                    <span className={styles.city}>{route.destino}</span>
                  </div>
                  {isIntermediate && (
                    <span className={styles.badge}>Parada Intermedia</span>
                  )}
                </div>
              </div>

              <div className={styles.cardContent}>
                <div className={styles.detailsGrid}>
                  <div className={styles.detail}>
                    <span className={styles.detailLabel}>💵 Precio</span>
                    <span className={styles.detailValue}>
                      ${route.precio.toLocaleString('es-CO')}
                    </span>
                  </div>

                  <div className={styles.detail}>
                    <span className={styles.detailLabel}>⏱️ Duración</span>
                    <span className={styles.detailValue}>
                      {route.duracion}h
                    </span>
                  </div>

                  {route.horaSalida && (
                    <div className={styles.detail}>
                      <span className={styles.detailLabel}>🕐 Salida</span>
                      <span className={styles.detailValue}>
                        {route.horaSalida}
                      </span>
                    </div>
                  )}

                  {route.horaLlegada && (
                    <div className={styles.detail}>
                      <span className={styles.detailLabel}>🕑 Llegada</span>
                      <span className={styles.detailValue}>
                        {route.horaLlegada}
                      </span>
                    </div>
                  )}
                </div>

                {route.paradas && route.paradas.length > 0 && (
                  <div className={styles.paradasSection}>
                    <p className={styles.paradasLabel}>📍 Paradas intermedias:</p>
                    <div className={styles.paradasList}>
                      {route.paradas.map((parada, index) => (
                        <span key={index} className={styles.paradaBadge}>
                          {parada}
                        </span>
                      ))}
                    </div>
                  </div>
                )}

                {route.turnos && route.turnos.length > 0 && (
                  <div className={styles.turnosSection}>
                    <p className={styles.turnosLabel}>🚌 Turnos Disponibles:</p>
                    <div className={styles.turnosList}>
                      {route.turnos.slice(0, 3).map((turno) => (
                        <div key={turno.id} className={styles.turnoBadge}>
                          <div className={styles.turnoTime}>
                            {turno.horaInicio}
                            {turno.horaFin && ` - ${turno.horaFin}`}
                          </div>
                          {turno.bus && (
                            <div className={styles.turnoBus}>
                              🚐 {turno.bus.placa}
                            </div>
                          )}
                        </div>
                      ))}
                      {route.turnos.length > 3 && (
                        <span className={styles.moreIndicator}>
                          +{route.turnos.length - 3} más
                        </span>
                      )}
                    </div>
                  </div>
                )}
              </div>

              <div className={styles.cardFooter}>
                <button className={styles.selectButton}>
                  Seleccionar Ruta
                </button>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
