@php
    $active = $active ?? false;
    $trueLabel = $trueLabel ?? 'Activo';
    $falseLabel = $falseLabel ?? 'Inactivo';
@endphp

<span class="inline-flex rounded px-2.5 py-1 text-xs font-semibold {{ $active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
    {{ $active ? $trueLabel : $falseLabel }}
</span>
