<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Ciudad;
use App\Models\Cooperativa;
use App\Models\Frecuencia;
use App\Models\Ruta;
use App\Models\TipoAsiento;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusquedaViajeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $filters = $request->validate([
            'ciudad_origen_id' => ['nullable', 'exists:ciudades,id'],
            'ciudad_destino_id' => ['nullable', 'exists:ciudades,id'],
            'cooperativa_id' => ['nullable', 'exists:cooperativas,id'],
            'tipo_asiento_id' => ['nullable', 'exists:tipo_asientos,id'],
            'marca_chasis' => ['nullable', 'string', 'max:255'],
            'marca_carroceria' => ['nullable', 'string', 'max:255'],
            'tipo_viaje' => ['nullable', 'in:directo,con_paradas'],
        ]);

        $viajes = Ruta::query()
            ->with(['cooperativa', 'bus.asientos.tipoAsiento', 'origen.provincia', 'destino.provincia'])
            ->where('activa', true)
            ->whereHas('cooperativa', fn (Builder $query) => $query->where('activa', true))
            ->whereHas('bus', fn (Builder $query) => $query->where('activo', true))
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('frecuencias')
                    ->whereColumn('frecuencias.cooperativa_id', 'rutas.cooperativa_id')
                    ->whereColumn('frecuencias.ciudad_origen_id', 'rutas.ciudad_origen_id')
                    ->whereColumn('frecuencias.ciudad_destino_id', 'rutas.ciudad_destino_id')
                    ->where('frecuencias.activa', true);
            })
            ->when($filters['ciudad_origen_id'] ?? null, fn (Builder $query, string $value) => $query->where('ciudad_origen_id', $value))
            ->when($filters['ciudad_destino_id'] ?? null, fn (Builder $query, string $value) => $query->where('ciudad_destino_id', $value))
            ->when($filters['cooperativa_id'] ?? null, fn (Builder $query, string $value) => $query->where('cooperativa_id', $value))
            ->when($filters['tipo_viaje'] ?? null, fn (Builder $query, string $value) => $query->where('tipo_viaje', $value))
            ->when($filters['marca_chasis'] ?? null, function (Builder $query, string $value) {
                $query->whereHas('bus', fn (Builder $busQuery) => $busQuery->where('marca_chasis', $value));
            })
            ->when($filters['marca_carroceria'] ?? null, function (Builder $query, string $value) {
                $query->whereHas('bus', fn (Builder $busQuery) => $busQuery->where('marca_carroceria', $value));
            })
            ->when($filters['tipo_asiento_id'] ?? null, function (Builder $query, string $value) {
                $query->whereHas('bus.asientos', function (Builder $asientoQuery) use ($value) {
                    $asientoQuery->where('activo', true)
                        ->where('tipo_asiento_id', $value);
                });
            })
            ->orderBy('nombre')
            ->paginate(9)
            ->withQueryString();

        $frecuenciasPorRuta = $this->frecuenciasPorRuta($viajes->getCollection());

        return view('viajes.busqueda', [
            'viajes' => $viajes,
            'frecuenciasPorRuta' => $frecuenciasPorRuta,
            'cooperativas' => Cooperativa::where('activa', true)->orderBy('nombre')->get(),
            'ciudades' => Ciudad::where('activa', true)->with('provincia')->orderBy('nombre')->get(),
            'tiposAsiento' => TipoAsiento::where('activo', true)->orderBy('nombre')->get(),
            'marcasChasis' => Bus::where('activo', true)->whereNotNull('marca_chasis')->distinct()->orderBy('marca_chasis')->pluck('marca_chasis'),
            'marcasCarroceria' => Bus::where('activo', true)->whereNotNull('marca_carroceria')->distinct()->orderBy('marca_carroceria')->pluck('marca_carroceria'),
            'filters' => $filters,
        ]);
    }

    private function frecuenciasPorRuta($rutas)
    {
        return $rutas->mapWithKeys(function (Ruta $ruta) {
            $frecuencias = Frecuencia::where('activa', true)
                ->where('cooperativa_id', $ruta->cooperativa_id)
                ->where('ciudad_origen_id', $ruta->ciudad_origen_id)
                ->where('ciudad_destino_id', $ruta->ciudad_destino_id)
                ->orderBy('hora_salida')
                ->get();

            return [$ruta->id => $frecuencias];
        });
    }
}
