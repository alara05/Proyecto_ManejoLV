@extends('layouts.app')

@section('content')
    <div class="space-y-6 text-slate-100">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-white">Notificaciones</h1>
                <p class="text-sm text-slate-300">Avisos enviados por compras, pagos y boletos emitidos.</p>
            </div>

            <a href="{{ route('notificaciones.pdf') }}" class="rounded-lg border border-sky-400 bg-sky-400 px-4 py-2 text-sm font-black text-slate-950 hover:border-sky-300 hover:bg-sky-300">
                Descargar PDF
            </a>
        </div>

        @include('partials.flash')

        <div class="overflow-hidden rounded-xl border border-white/10 bg-slate-900/80 shadow-xl shadow-black/20">
            @forelse ($notificaciones as $notificacion)
                @php
                    $data = $notificacion->data ?? [];
                    $titulo = $data['codigo'] ?? class_basename($notificacion->type);
                    $mensaje = $data['mensaje'] ?? 'Tienes una actualizacion pendiente.';
                @endphp

                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 px-5 py-4 last:border-b-0 {{ $notificacion->read_at ? 'bg-slate-900/40' : 'bg-sky-500/10' }}">
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-black text-white">{{ $titulo }}</h2>
                            @unless ($notificacion->read_at)
                                <span class="rounded-full bg-orange-500 px-2 py-0.5 text-xs font-black text-white">Nueva</span>
                            @endunless
                        </div>
                        <p class="text-sm text-slate-300">{{ $mensaje }}</p>
                        <p class="text-xs text-slate-500">{{ $notificacion->created_at->diffForHumans() }}</p>
                    </div>

                    <form method="POST" action="{{ route('notificaciones.leer', $notificacion->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-lg bg-white px-4 py-2 text-sm font-black text-slate-950 hover:bg-slate-200">
                            Abrir
                        </button>
                    </form>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-slate-300">
                    No tienes notificaciones registradas.
                </div>
            @endforelse
        </div>

        {{ $notificaciones->links() }}
    </div>
@endsection
