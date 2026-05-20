@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Notificaciones</h1>
                <p class="text-sm text-slate-600">Avisos del proceso de compra, pago y validacion de boletos.</p>
            </div>
            <a href="{{ route('notificaciones.pdf') }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Descargar PDF
            </a>
        </div>

        @include('partials.flash')

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            @forelse ($notificaciones as $notificacion)
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 last:border-b-0 {{ $notificacion->read_at ? 'bg-white' : 'bg-amber-50' }}">
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-semibold text-slate-900">{{ $notificacion->data['titulo'] ?? 'Notificacion' }}</h2>
                            @unless ($notificacion->read_at)
                                <span class="rounded-full bg-amber-500 px-2 py-0.5 text-xs font-bold text-white">Nueva</span>
                            @endunless
                        </div>
                        <p class="text-sm text-slate-700">{{ $notificacion->data['mensaje'] ?? 'Tienes una actualizacion pendiente.' }}</p>
                        <p class="text-xs text-slate-500">{{ $notificacion->created_at->diffForHumans() }}</p>
                    </div>

                    <form method="POST" action="{{ route('notificaciones.leer', $notificacion->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                            Abrir
                        </button>
                    </form>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-slate-600">
                    No tienes notificaciones registradas.
                </div>
            @endforelse
        </div>

        {{ $notificaciones->links() }}
    </div>
@endsection
