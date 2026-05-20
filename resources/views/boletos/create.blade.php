@extends('layouts.principal')

@section('title', 'Vender boleto | ' . config('app.name', 'Manejo Buses'))

@push('styles')
    <style>
        .ticket-sale-page {
            min-height: calc(100vh - var(--header-height));
            padding: 30px;
            background:
                radial-gradient(circle at top left, rgba(236, 117, 25, 0.14), transparent 30%),
                radial-gradient(circle at top right, rgba(36, 168, 255, 0.14), transparent 30%),
                linear-gradient(180deg, #090d15 0%, #05070b 100%);
        }

        .ticket-sale-shell {
            width: min(1540px, 100%);
            margin: 0 auto;
        }

        .sale-path,
        .sale-heading,
        .sale-card {
            border: 1px solid var(--line-soft);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.04)),
                rgba(13, 17, 26, 0.84);
            box-shadow: var(--shadow-soft);
            backdrop-filter: blur(18px);
        }

        .sale-path {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            align-items: center;
            margin-bottom: 16px;
            padding: 15px 18px;
            border-radius: 14px;
            color: var(--text-muted);
            font-weight: 850;
        }

        .sale-path strong {
            color: #ffffff;
        }

        .sale-heading {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            padding: 18px;
            border-radius: 18px;
        }

        .sale-heading-main {
            display: flex;
            gap: 14px;
            align-items: center;
        }

        .sale-heading-icon {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            background: var(--accent);
            color: #04111d;
            font-size: 1.55rem;
            font-weight: 950;
        }

        .sale-kicker,
        .sale-label,
        .sale-field span,
        .sale-total-row span {
            color: var(--accent-hover);
            font-size: 0.76rem;
            font-weight: 950;
            letter-spacing: 0.09em;
            text-transform: uppercase;
        }

        .sale-heading h1 {
            margin: 3px 0 0;
            color: #ffffff;
            font-size: clamp(1.35rem, 2.3vw, 2rem);
            line-height: 1.1;
        }

        .sale-count-pill {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 999px;
            padding: 0 16px;
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            font-weight: 950;
        }

        .sale-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.36fr) minmax(430px, 0.84fr);
            gap: 18px;
            align-items: start;
        }

        .sale-card {
            border-radius: 18px;
            overflow: hidden;
        }

        .sale-card-inner {
            padding: 20px;
        }

        .sale-card-title {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 18px;
        }

        .sale-card-title span {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-weight: 950;
        }

        .sale-card-title h2 {
            margin: 0;
            color: #ffffff;
            font-size: 1.35rem;
            line-height: 1.1;
        }

        .sale-layout.payment-in-modal {
            grid-template-columns: minmax(0, 1fr);
        }

        .sale-card-title-between {
            justify-content: space-between;
            align-items: center;
        }

        .sale-card-title-main {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .payment-open-button {
            min-height: 42px;
            border: 1px solid var(--accent);
            border-radius: 999px;
            background: var(--accent);
            color: #04111d;
            font: inherit;
            font-weight: 950;
            padding: 0 18px;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease, opacity 0.2s ease;
        }

        .payment-open-button:hover {
            transform: translateY(-1px);
            background: var(--accent-hover);
        }

        .payment-open-button:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            transform: none;
        }

        .payment-modal-panel {
            width: min(860px, 100%);
        }

        .payment-modal-body {
            padding: 0;
        }

        .payment-modal-card {
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }

        .sale-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .sale-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .sale-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .sale-field span {
            display: block;
            margin-bottom: 8px;
        }

        .sale-field input,
        .sale-field select,
        .sale-field textarea {
            width: 100%;
            min-height: 48px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 10px;
            background: rgba(3, 7, 14, 0.72);
            color: #ffffff;
            font: inherit;
            font-weight: 760;
            padding: 0 13px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .sale-field textarea {
            min-height: 92px;
            padding-top: 12px;
            resize: vertical;
        }

        .sale-field input:focus,
        .sale-field select:focus,
        .sale-field textarea:focus {
            border-color: var(--accent-hover);
            background: rgba(3, 7, 14, 0.9);
            box-shadow: 0 0 0 4px rgba(36, 168, 255, 0.16);
        }

        .sale-field input[readonly] {
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.82);
        }

        .sale-hidden-select {
            display: none;
        }

        .sale-picker-button {
            width: 100%;
            min-height: 48px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 12px;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 10px;
            background: rgba(3, 7, 14, 0.72);
            color: #ffffff;
            font: inherit;
            font-weight: 850;
            padding: 0 13px;
            text-align: left;
            cursor: pointer;
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .sale-picker-button:hover {
            transform: translateY(-1px);
            border-color: rgba(36, 168, 255, 0.62);
            background: rgba(3, 7, 14, 0.88);
        }

        .sale-picker-button:focus-visible {
            border-color: var(--accent-hover);
            box-shadow: 0 0 0 4px rgba(36, 168, 255, 0.16);
            outline: none;
        }

        .sale-picker-button small {
            display: block;
            margin-top: 3px;
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .sale-picker-chevron {
            color: var(--accent-hover);
            font-size: 1.15rem;
            font-weight: 950;
        }

        .sale-divider {
            margin: 22px 0;
            border: 0;
            border-top: 1px solid var(--line-soft);
        }

        .seat-head {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .seat-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .seat-legend span {
            min-height: 28px;
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0 10px;
            background: rgba(255, 255, 255, 0.09);
            color: #ffffff;
            font-size: 0.76rem;
            font-weight: 900;
        }

        .seat-legend .legend-selected {
            background: rgba(36, 168, 255, 0.22);
            color: #d9f1ff;
        }

        .seat-legend .legend-busy {
            background: rgba(220, 38, 38, 0.22);
            color: #ffd3d3;
        }

        .seat-grid {
            display: grid;
            grid-template-columns: repeat(9, minmax(74px, 1fr));
            gap: 10px;
        }

        .seat-button {
            min-height: 76px;
            border-radius: 10px;
            padding: 10px 7px;
            font: inherit;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        .seat-button:not(:disabled):hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .seat-button strong,
        .seat-button small {
            display: block;
        }

        .seat-icon {
            width: 28px;
            height: 24px;
            display: grid;
            place-items: center;
            margin: 0 auto 5px;
            border: 2px solid currentColor;
            border-radius: 7px 7px 4px 4px;
            font-size: 0;
        }

        .seat-available {
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.13), rgba(255, 255, 255, 0.06));
            color: #ffffff;
        }

        .seat-selected {
            border: 1px solid rgba(36, 168, 255, 0.72);
            background: linear-gradient(180deg, rgba(36, 168, 255, 0.42), rgba(36, 168, 255, 0.18));
            color: #ffffff;
            box-shadow: 0 16px 34px rgba(36, 168, 255, 0.16);
        }

        .seat-busy {
            border: 1px solid rgba(248, 113, 113, 0.42);
            background: linear-gradient(180deg, rgba(244, 63, 94, 0.34), rgba(127, 29, 29, 0.34));
            color: #ffe3e3;
            cursor: not-allowed;
        }

        .selected-table-wrap {
            margin-top: 18px;
            overflow-x: auto;
            border: 1px solid var(--line-soft);
            border-radius: 14px;
            background: rgba(3, 7, 14, 0.34);
        }

        .selected-table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
            color: #ffffff;
        }

        .selected-table thead {
            background:
                linear-gradient(90deg, rgba(36, 168, 255, 0.16), rgba(236, 117, 25, 0.1)),
                rgba(13, 17, 26, 0.9);
        }

        .selected-table th,
        .selected-table td {
            padding: 13px 14px;
            text-align: left;
        }

        .selected-table th {
            color: #d9f1ff;
            font-size: 0.76rem;
            font-weight: 950;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .selected-table tbody tr + tr {
            border-top: 1px solid rgba(255, 255, 255, 0.09);
        }

        .selected-table-empty {
            color: var(--text-muted);
            text-align: center;
        }

        .remove-seat {
            min-width: 34px;
            min-height: 34px;
            border: 1px solid rgba(248, 113, 113, 0.45);
            border-radius: 9px;
            background: rgba(127, 29, 29, 0.28);
            color: #ffd3d3;
            font: inherit;
            font-weight: 950;
            cursor: pointer;
        }

        .payment-panel {
            margin-top: 16px;
        }

        .payment-panel.is-hidden {
            display: none;
        }

        .sale-side-footer {
            border-top: 1px solid var(--line-soft);
            padding: 20px;
            background: rgba(3, 7, 14, 0.22);
        }

        .sale-total-box {
            padding: 15px;
            border: 1px solid var(--line-soft);
            border-radius: 14px;
            background: rgba(3, 7, 14, 0.4);
        }

        .sale-total-row {
            display: grid;
            grid-template-columns: minmax(0, 0.8fr) minmax(0, 1.2fr);
            gap: 12px;
            align-items: center;
        }

        .sale-total-row + .sale-total-row {
            margin-top: 10px;
        }

        .sale-submit {
            width: 100%;
            min-height: 52px;
            margin-top: 16px;
            border: 1px solid var(--accent);
            border-radius: 11px;
            background: var(--accent);
            color: #04111d;
            font: inherit;
            font-weight: 950;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease, opacity 0.2s ease;
        }

        .sale-submit:hover {
            transform: translateY(-1px);
            background: var(--accent-hover);
        }

        .sale-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .sale-alert {
            margin-bottom: 18px;
            padding: 16px 18px;
            border: 1px solid rgba(248, 113, 113, 0.38);
            border-radius: 14px;
            background: rgba(127, 29, 29, 0.24);
            color: #ffe3e3;
            font-weight: 760;
        }

        .sale-alert ul {
            margin: 10px 0 0;
            padding-left: 20px;
        }

        .sale-empty {
            grid-column: 1 / -1;
            margin: 0;
            padding: 15px;
            border: 1px solid rgba(255, 214, 102, 0.3);
            border-radius: 12px;
            background: rgba(140, 96, 20, 0.18);
            color: #ffe5a8;
            font-weight: 850;
        }

        .sale-modal {
            position: fixed;
            inset: 0;
            z-index: 80;
            display: grid;
            place-items: center;
            padding: 24px;
            background: rgba(3, 7, 14, 0.72);
            backdrop-filter: blur(14px);
        }

        .sale-modal[hidden] {
            display: none;
        }

        .sale-modal-panel {
            width: min(980px, 100%);
            max-height: min(760px, calc(100vh - 48px));
            display: flex;
            flex-direction: column;
            border: 1px solid var(--line-soft);
            border-radius: 18px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.11), rgba(255, 255, 255, 0.045)),
                #0d111a;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .sale-modal-head {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            justify-content: space-between;
            padding: 20px;
            border-bottom: 1px solid var(--line-soft);
        }

        .sale-modal-head h3 {
            margin: 3px 0 0;
            color: #ffffff;
            font-size: 1.35rem;
            line-height: 1.1;
        }

        .sale-modal-close {
            width: 40px;
            height: 40px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.09);
            color: #ffffff;
            font: inherit;
            font-size: 1.3rem;
            font-weight: 950;
            cursor: pointer;
        }

        .sale-modal-tools {
            display: grid;
            grid-template-columns: minmax(240px, 1fr) repeat(2, minmax(180px, 0.45fr));
            gap: 12px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--line-soft);
            background: rgba(3, 7, 14, 0.22);
        }

        .sale-modal-body {
            overflow-y: auto;
            padding: 18px 20px 20px;
        }

        .sale-option-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .sale-option-card {
            min-height: 106px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 14px;
            background:
                linear-gradient(135deg, rgba(36, 168, 255, 0.08), transparent 58%),
                rgba(3, 7, 14, 0.54);
            color: #ffffff;
            font: inherit;
            padding: 14px;
            text-align: left;
            cursor: pointer;
            transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease;
        }

        .sale-option-card:hover,
        .sale-option-card.is-selected {
            transform: translateY(-2px);
            border-color: rgba(36, 168, 255, 0.68);
            background:
                linear-gradient(135deg, rgba(36, 168, 255, 0.2), rgba(236, 117, 25, 0.08)),
                rgba(3, 7, 14, 0.76);
        }

        .sale-option-card strong,
        .sale-option-card span,
        .sale-option-card small {
            display: block;
        }

        .sale-option-card strong {
            font-size: 1rem;
            line-height: 1.22;
        }

        .sale-option-card span {
            margin-top: 8px;
            color: var(--text-muted);
            font-size: 0.86rem;
            font-weight: 800;
        }

        .sale-option-card small {
            margin-top: 8px;
            color: #d9f1ff;
            font-weight: 900;
        }

        @media (max-width: 1180px) {
            .sale-layout {
                grid-template-columns: 1fr;
            }

            .seat-grid {
                grid-template-columns: repeat(6, minmax(74px, 1fr));
            }
        }

        @media (max-width: 760px) {
            .ticket-sale-page {
                padding: 22px 18px 56px;
            }

            .sale-grid,
            .sale-grid-2,
            .sale-grid-3,
            .sale-total-row {
                grid-template-columns: 1fr;
            }

            .seat-grid {
                grid-template-columns: repeat(3, minmax(74px, 1fr));
            }

            .sale-modal-tools,
            .sale-option-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 460px) {
            .seat-grid {
                grid-template-columns: repeat(2, minmax(74px, 1fr));
            }
        }
    </style>
@endpush

@section('content')
    <section class="ticket-sale-page">
        <div class="ticket-sale-shell">
            <div class="sale-path">
                <span>Panel</span>
                <span>/</span>
                <strong>Vender boleto</strong>
            </div>

            <div class="sale-heading">
                <div class="sale-heading-main">
                    <span class="sale-heading-icon">+</span>
                    <div>
                        <div class="sale-kicker">Registro de venta</div>
                        <h1>Nuevo registro de una venta</h1>
                    </div>
                </div>
                <div class="sale-count-pill"><span id="available-count">0</span>&nbsp;asientos disponibles</div>
            </div>

            @if ($errors->any())
                <div class="sale-alert">
                    <strong>Revisa los datos de la venta.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('cliente.boletos.store') }}" id="ticket-sale-form" class="sale-layout payment-in-modal">
                @csrf
                <input type="hidden" name="salida_id" id="salida_id" value="{{ old('salida_id', $salida?->id) }}">
                <input type="hidden" name="tipo_asiento_id" id="tipo_asiento_id" value="{{ old('tipo_asiento_id') }}">
                <div id="selected-seat-inputs"></div>

                <section class="sale-card">
                    <div class="sale-card-inner">
                        <div class="sale-card-title sale-card-title-between">
                            <div class="sale-card-title-main">
                                <span>B</span>
                                <h2>Detalle de boletos</h2>
                            </div>

                            <button type="button" id="open-payment-modal" class="payment-open-button" disabled>
                                Pago
                            </button>
                        </div>

                        <div class="sale-grid">
                            <label class="sale-field">
                                <span>Destino</span>
                                <select id="destination-select" class="sale-hidden-select" tabindex="-1" aria-hidden="true"></select>
                                <button type="button" id="destination-picker-button" class="sale-picker-button">
                                    <span>
                                        <strong id="destination-picker-label">Seleccione un destino</strong>
                                        <small id="destination-picker-meta">Buscar por origen, destino o fecha</small>
                                    </span>
                                    <span class="sale-picker-chevron">v</span>
                                </button>
                            </label>

                            <label class="sale-field">
                                <span>Bus</span>
                                <select id="bus-select" class="sale-hidden-select" tabindex="-1" aria-hidden="true"></select>
                                <button type="button" id="bus-picker-button" class="sale-picker-button">
                                    <span>
                                        <strong id="bus-picker-label">Seleccione un bus</strong>
                                        <small id="bus-picker-meta">Buscar por bus, placa o cooperativa</small>
                                    </span>
                                    <span class="sale-picker-chevron">v</span>
                                </button>
                            </label>

                            <label class="sale-field">
                                <span>H.Salida / Precio</span>
                                <select id="departure-select"></select>
                            </label>
                        </div>

                        <hr class="sale-divider">

                        <div class="seat-head">
                            <div class="sale-label">Seleccione asiento(s)</div>
                            <div class="seat-legend" aria-label="Leyenda de asientos">
                                <span>Disponible</span>
                                <span class="legend-selected">Seleccionado</span>
                                <span class="legend-busy">Ocupado</span>
                            </div>
                        </div>

                        <div id="seat-grid" class="seat-grid"></div>

                        <div class="selected-table-wrap">
                            <table class="selected-table">
                                <thead>
                                    <tr>
                                        <th>Destino</th>
                                        <th>Bus</th>
                                        <th>H.Salida</th>
                                        <th>Anden</th>
                                        <th>Asiento</th>
                                        <th>Precio</th>
                                        <th>Opcion</th>
                                    </tr>
                                </thead>
                                <tbody id="selected-seat-table">
                                    <tr>
                                        <td colspan="7" class="selected-table-empty">Seleccione uno o mas asientos para vender.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <div id="payment-modal" class="sale-modal" hidden>
                    <div class="sale-modal-panel payment-modal-panel" role="dialog" aria-modal="true" aria-labelledby="payment-modal-title">
                        <div class="sale-modal-head">
                            <div>
                                <div class="sale-kicker">Formulario de pago</div>
                                <h3 id="payment-modal-title">Registrar pago de la venta</h3>
                            </div>
                            <button type="button" class="sale-modal-close" data-close-modal="payment-modal" aria-label="Cerrar">x</button>
                        </div>

                        <div class="sale-modal-body payment-modal-body">
                <aside class="sale-card payment-modal-card">
                    <div class="sale-card-inner">
                        <label class="sale-field">
                            <span>Cliente</span>
                            <input name="pasajero_nombre" type="text" value="{{ old('pasajero_nombre', 'Consumidor Final') }}" required>
                        </label>

                        <div class="sale-grid-2" style="margin-top: 14px;">
                            <label class="sale-field">
                                <span>Cedula</span>
                                <input name="pasajero_cedula" type="text" value="{{ old('pasajero_cedula', '9999999999') }}" maxlength="10" required>
                            </label>

                            <label class="sale-field">
                                <span>Correo de contacto</span>
                                <input name="cliente_email" type="email" value="{{ old('cliente_email', auth()->user()?->email) }}" @guest required @endguest>
                            </label>
                        </div>

                        <div class="sale-grid-3" style="margin-top: 16px;">
                            <label class="sale-field">
                                <span>Metodo de pagos</span>
                                <select id="payment-method" name="metodo_pago" required>
                                    @foreach ($metodosPago as $value => $label)
                                        <option value="{{ $value }}" @selected(old('metodo_pago', 'efectivo') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="sale-field">
                                <span>Comprobante</span>
                                <select name="comprobante_tipo" required>
                                    <option value="ticket" @selected(old('comprobante_tipo', 'ticket') === 'ticket')>Ticket</option>
                                    <option value="factura" @selected(old('comprobante_tipo') === 'factura')>Factura</option>
                                </select>
                            </label>

                            <label class="sale-field">
                                <span>Total a pagar</span>
                                <input id="total-display-input" type="text" value="$0.00" readonly>
                            </label>
                        </div>

                        <div class="sale-grid-2" style="margin-top: 16px;">
                            <label class="sale-field">
                                <span>Descuento</span>
                                <select id="discount-select" name="tipo_descuento" required>
                                    @foreach ($descuentos as $value => $label)
                                        <option value="{{ $value }}" data-percent="{{ in_array($value, ['menor_edad', 'discapacidad', 'tercera_edad'], true) ? 50 : 0 }}" @selected(old('tipo_descuento', 'ninguno') === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        <div class="payment-panel" data-method-panel="efectivo">
                            <div class="sale-grid-2">
                                <label class="sale-field">
                                    <span>Efectivo</span>
                                    <input id="cash-received" name="efectivo_recibido" type="number" step="0.01" min="0" value="{{ old('efectivo_recibido') }}">
                                </label>
                                <label class="sale-field">
                                    <span>Cambio</span>
                                    <input id="cash-change" type="text" value="$0.00" readonly>
                                </label>
                            </div>
                        </div>

                        <div class="payment-panel is-hidden" data-method-panel="tarjeta">
                            <div class="sale-grid-2">
                                <label class="sale-field" style="grid-column: 1 / -1;">
                                    <span>Titular de la tarjeta</span>
                                    <input name="tarjeta_titular" type="text" value="{{ old('tarjeta_titular') }}">
                                </label>
                                <label class="sale-field">
                                    <span>Marca</span>
                                    <select name="tarjeta_marca">
                                        <option value="">Seleccione</option>
                                        <option value="Visa" @selected(old('tarjeta_marca') === 'Visa')>Visa</option>
                                        <option value="Mastercard" @selected(old('tarjeta_marca') === 'Mastercard')>Mastercard</option>
                                        <option value="American Express" @selected(old('tarjeta_marca') === 'American Express')>American Express</option>
                                    </select>
                                </label>
                                <label class="sale-field">
                                    <span>Ultimos 4 digitos</span>
                                    <input name="tarjeta_ultimos4" type="text" inputmode="numeric" maxlength="4" value="{{ old('tarjeta_ultimos4') }}">
                                </label>
                                <label class="sale-field" style="grid-column: 1 / -1;">
                                    <span>Codigo de autorizacion</span>
                                    <input name="tarjeta_autorizacion" type="text" value="{{ old('tarjeta_autorizacion') }}">
                                </label>
                            </div>
                        </div>

                        <div class="payment-panel is-hidden" data-method-panel="transferencia">
                            <div class="sale-grid-2">
                                <label class="sale-field">
                                    <span>Banco emisor</span>
                                    <input name="transferencia_banco" type="text" value="{{ old('transferencia_banco') }}">
                                </label>
                                <label class="sale-field">
                                    <span>Titular de la cuenta</span>
                                    <input name="transferencia_titular" type="text" value="{{ old('transferencia_titular') }}">
                                </label>
                                <label class="sale-field" style="grid-column: 1 / -1;">
                                    <span>Referencia bancaria</span>
                                    <input name="transferencia_referencia" type="text" value="{{ old('transferencia_referencia') }}">
                                </label>
                            </div>
                        </div>

                        <div class="payment-panel is-hidden" data-method-panel="deposito">
                            <div class="sale-grid-2">
                                <label class="sale-field">
                                    <span>Banco receptor</span>
                                    <input name="deposito_banco" type="text" value="{{ old('deposito_banco') }}">
                                </label>
                                <label class="sale-field">
                                    <span>Depositante</span>
                                    <input name="deposito_titular" type="text" value="{{ old('deposito_titular') }}">
                                </label>
                                <label class="sale-field" style="grid-column: 1 / -1;">
                                    <span>Numero de deposito</span>
                                    <input name="deposito_numero" type="text" value="{{ old('deposito_numero') }}">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="sale-side-footer">
                        <div class="sale-total-box">
                            <label class="sale-field sale-total-row">
                                <span>SUBTOTAL</span>
                                <input id="subtotal-input" type="text" value="$0.00" readonly>
                            </label>
                            <label class="sale-field sale-total-row">
                                <span>IVA %</span>
                                <input type="text" value="12%" readonly>
                            </label>
                            <label class="sale-field sale-total-row">
                                <span>IVA</span>
                                <input id="iva-input" type="text" value="$0.00" readonly>
                            </label>
                            <label class="sale-field sale-total-row">
                                <span>TOTAL</span>
                                <input id="total-input" type="text" value="$0.00" readonly>
                            </label>
                        </div>

                        <label class="sale-field" style="display: block; margin-top: 14px;">
                            <span>Observacion</span>
                            <textarea name="observacion_pago">{{ old('observacion_pago') }}</textarea>
                        </label>

                        <button id="submit-sale" type="submit" class="sale-submit" disabled>Confirmar venta</button>
                    </div>
                </aside>
                        </div>
                    </div>
                </div>
            </form>

            <div id="destination-modal" class="sale-modal" hidden>
                <div class="sale-modal-panel" role="dialog" aria-modal="true" aria-labelledby="destination-modal-title">
                    <div class="sale-modal-head">
                        <div>
                            <div class="sale-kicker">Seleccionar destino</div>
                            <h3 id="destination-modal-title">Destinos disponibles</h3>
                        </div>
                        <button type="button" class="sale-modal-close" data-close-modal="destination-modal" aria-label="Cerrar">x</button>
                    </div>
                    <div class="sale-modal-tools">
                        <label class="sale-field">
                            <span>Buscador</span>
                            <input id="destination-search" type="search" placeholder="Origen, destino o fecha">
                        </label>
                        <label class="sale-field">
                            <span>Origen</span>
                            <select id="destination-origin-filter"></select>
                        </label>
                        <label class="sale-field">
                            <span>Destino</span>
                            <select id="destination-end-filter"></select>
                        </label>
                    </div>
                    <div class="sale-modal-body">
                        <div id="destination-option-list" class="sale-option-grid"></div>
                    </div>
                </div>
            </div>

            <div id="bus-modal" class="sale-modal" hidden>
                <div class="sale-modal-panel" role="dialog" aria-modal="true" aria-labelledby="bus-modal-title">
                    <div class="sale-modal-head">
                        <div>
                            <div class="sale-kicker">Seleccionar bus</div>
                            <h3 id="bus-modal-title">Buses para el destino</h3>
                        </div>
                        <button type="button" class="sale-modal-close" data-close-modal="bus-modal" aria-label="Cerrar">x</button>
                    </div>
                    <div class="sale-modal-tools">
                        <label class="sale-field">
                            <span>Buscador</span>
                            <input id="bus-search" type="search" placeholder="Bus, placa o cooperativa">
                        </label>
                        <label class="sale-field">
                            <span>Cooperativa</span>
                            <select id="bus-cooperative-filter"></select>
                        </label>
                        <label class="sale-field">
                            <span>Disponibilidad</span>
                            <select id="bus-availability-filter">
                                <option value="">Todos</option>
                                <option value="available">Con asientos libres</option>
                                <option value="full">Sin asientos libres</option>
                            </select>
                        </label>
                    </div>
                    <div class="sale-modal-body">
                        <div id="bus-option-list" class="sale-option-grid"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const sales = @json($salesData);
            const oldSalidaId = Number(@json((int) old('salida_id', $salida?->id ?? 0)));
            const oldSeatIds = new Set((@json((array) old('asiento_ids', []))).map((id) => Number(id)));
            const shouldOpenPaymentModal = @json($errors->any());
            const destinationSelect = document.getElementById('destination-select');
            const busSelect = document.getElementById('bus-select');
            const departureSelect = document.getElementById('departure-select');
            const destinationPickerButton = document.getElementById('destination-picker-button');
            const destinationPickerLabel = document.getElementById('destination-picker-label');
            const destinationPickerMeta = document.getElementById('destination-picker-meta');
            const busPickerButton = document.getElementById('bus-picker-button');
            const busPickerLabel = document.getElementById('bus-picker-label');
            const busPickerMeta = document.getElementById('bus-picker-meta');
            const destinationModal = document.getElementById('destination-modal');
            const busModal = document.getElementById('bus-modal');
            const paymentModal = document.getElementById('payment-modal');
            const openPaymentModalButton = document.getElementById('open-payment-modal');
            const destinationSearch = document.getElementById('destination-search');
            const destinationOriginFilter = document.getElementById('destination-origin-filter');
            const destinationEndFilter = document.getElementById('destination-end-filter');
            const destinationOptionList = document.getElementById('destination-option-list');
            const busSearch = document.getElementById('bus-search');
            const busCooperativeFilter = document.getElementById('bus-cooperative-filter');
            const busAvailabilityFilter = document.getElementById('bus-availability-filter');
            const busOptionList = document.getElementById('bus-option-list');
            const salidaInput = document.getElementById('salida_id');
            const seatGrid = document.getElementById('seat-grid');
            const selectedInputs = document.getElementById('selected-seat-inputs');
            const selectedTable = document.getElementById('selected-seat-table');
            const discountSelect = document.getElementById('discount-select');
            const paymentMethod = document.getElementById('payment-method');
            const cashReceived = document.getElementById('cash-received');
            const cashChange = document.getElementById('cash-change');
            const submitButton = document.getElementById('submit-sale');
            const availableCount = document.getElementById('available-count');
            const subtotalInput = document.getElementById('subtotal-input');
            const ivaInput = document.getElementById('iva-input');
            const totalInput = document.getElementById('total-input');
            const totalDisplayInput = document.getElementById('total-display-input');
            const selectedSeats = new Set([...oldSeatIds]);
            let currentSale = null;

            const money = (value) => `$${Number(value || 0).toFixed(2)}`;
            const discountPercent = () => Number(discountSelect.selectedOptions[0]?.dataset.percent || 0);
            const seatPrice = (seat) => Number((seat.precio - (seat.precio * (discountPercent() / 100))).toFixed(2));
            const normalize = (value) => String(value || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            function uniqueDestinations() {
                return [...new Map(sales.map((sale) => [sale.destinoKey, sale])).values()];
            }

            function destinationSales() {
                return sales.filter((sale) => sale.destinoKey === destinationSelect.value);
            }

            function busSales() {
                return destinationSales().filter((sale) => String(sale.busId) === busSelect.value);
            }

            function availableSeats(saleList) {
                return saleList.reduce((total, sale) => total + sale.asientos.filter((seat) => ! seat.ocupado).length, 0);
            }

            function selectedDestination() {
                return uniqueDestinations().find((sale) => sale.destinoKey === destinationSelect.value) || null;
            }

            function selectedBus() {
                return [...new Map(destinationSales().map((sale) => [sale.busId, sale])).values()]
                    .find((sale) => String(sale.busId) === String(busSelect.value)) || null;
            }

            function selectOptions(select, options, currentValue = null) {
                select.innerHTML = '';
                options.forEach((option) => {
                    const element = document.createElement('option');
                    element.value = option.value;
                    element.textContent = option.label;
                    select.appendChild(element);
                });

                if (currentValue && options.some((option) => String(option.value) === String(currentValue))) {
                    select.value = currentValue;
                }
            }

            function selectFilterOptions(select, values, placeholder) {
                const current = select.value;
                select.innerHTML = '';
                const all = document.createElement('option');
                all.value = '';
                all.textContent = placeholder;
                select.appendChild(all);

                [...new Set(values.filter(Boolean))].sort().forEach((value) => {
                    const option = document.createElement('option');
                    option.value = value;
                    option.textContent = value;
                    select.appendChild(option);
                });

                if ([...select.options].some((option) => option.value === current)) {
                    select.value = current;
                }
            }

            function renderPickerButtons() {
                const destination = selectedDestination();
                const bus = selectedBus();

                if (destination) {
                    const grouped = sales.filter((sale) => sale.destinoKey === destination.destinoKey);
                    destinationPickerLabel.textContent = destination.destinoLabel;
                    destinationPickerMeta.textContent = `${grouped.length} salidas / ${availableSeats(grouped)} asientos libres`;
                } else {
                    destinationPickerLabel.textContent = 'Seleccione un destino';
                    destinationPickerMeta.textContent = 'Buscar por origen, destino o fecha';
                }

                if (bus) {
                    const grouped = destinationSales().filter((sale) => sale.busId === bus.busId);
                    busPickerLabel.textContent = bus.busLabel;
                    busPickerMeta.textContent = `${bus.cooperativa} / ${grouped.length} salidas / ${availableSeats(grouped)} libres`;
                } else {
                    busPickerLabel.textContent = 'Seleccione un bus';
                    busPickerMeta.textContent = 'Buscar por bus, placa o cooperativa';
                }
            }

            function openModal(modal, focusTarget) {
                modal.hidden = false;
                setTimeout(() => focusTarget?.focus(), 0);
            }

            function closeModal(modal) {
                modal.hidden = true;
            }

            function chooseDestination(destinoKey) {
                destinationSelect.value = destinoKey;
                selectedSeats.clear();
                renderBuses();
                renderDepartures();
                renderPickerButtons();
                renderSeats();
                closeModal(destinationModal);
                busPickerButton.focus();
            }

            function chooseBus(busId) {
                busSelect.value = String(busId);
                selectedSeats.clear();
                renderDepartures();
                renderPickerButtons();
                renderSeats();
                closeModal(busModal);
                departureSelect.focus();
            }

            function renderDestinationFilters() {
                selectFilterOptions(destinationOriginFilter, sales.map((sale) => sale.origen), 'Todos');
                selectFilterOptions(destinationEndFilter, sales.map((sale) => sale.destino), 'Todos');
            }

            function renderBusFilters() {
                selectFilterOptions(busCooperativeFilter, destinationSales().map((sale) => sale.cooperativa), 'Todas');
            }

            function renderDestinationOptions() {
                const query = normalize(destinationSearch.value);
                const origin = destinationOriginFilter.value;
                const destination = destinationEndFilter.value;

                const options = uniqueDestinations().filter((sale) => {
                    const grouped = sales.filter((item) => item.destinoKey === sale.destinoKey);
                    const searchable = normalize([
                        sale.destinoLabel,
                        sale.origen,
                        sale.destino,
                        grouped.map((item) => `${item.fecha} ${item.hora}`).join(' '),
                    ].join(' '));

                    return (! query || searchable.includes(query))
                        && (! origin || sale.origen === origin)
                        && (! destination || sale.destino === destination);
                });

                destinationOptionList.innerHTML = '';

                options.forEach((sale) => {
                    const grouped = sales.filter((item) => item.destinoKey === sale.destinoKey);
                    const buses = new Set(grouped.map((item) => item.busId)).size;
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = `sale-option-card${sale.destinoKey === destinationSelect.value ? ' is-selected' : ''}`;
                    button.innerHTML = `
                        <strong>${sale.destinoLabel}</strong>
                        <span>${grouped.length} salidas programadas / ${buses} buses</span>
                        <small>${availableSeats(grouped)} asientos libres</small>
                    `;
                    button.addEventListener('click', () => chooseDestination(sale.destinoKey));
                    destinationOptionList.appendChild(button);
                });

                if (! options.length) {
                    destinationOptionList.innerHTML = '<p class="sale-empty">No hay destinos que coincidan con la busqueda.</p>';
                }
            }

            function renderBusOptions() {
                const query = normalize(busSearch.value);
                const cooperative = busCooperativeFilter.value;
                const availability = busAvailabilityFilter.value;
                const groupedBuses = [...new Map(destinationSales().map((sale) => [sale.busId, sale])).values()];

                const options = groupedBuses.filter((sale) => {
                    const grouped = destinationSales().filter((item) => item.busId === sale.busId);
                    const freeSeats = availableSeats(grouped);
                    const searchable = normalize([
                        sale.busLabel,
                        sale.cooperativa,
                        sale.destinoLabel,
                        grouped.map((item) => `${item.fecha} ${item.hora}`).join(' '),
                    ].join(' '));

                    return (! query || searchable.includes(query))
                        && (! cooperative || sale.cooperativa === cooperative)
                        && (! availability || (availability === 'available' ? freeSeats > 0 : freeSeats === 0));
                });

                busOptionList.innerHTML = '';

                options.forEach((sale) => {
                    const grouped = destinationSales().filter((item) => item.busId === sale.busId);
                    const firstDeparture = grouped[0];
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = `sale-option-card${String(sale.busId) === String(busSelect.value) ? ' is-selected' : ''}`;
                    button.innerHTML = `
                        <strong>${sale.busLabel}</strong>
                        <span>${sale.cooperativa} / ${grouped.length} salidas</span>
                        <small>${availableSeats(grouped)} asientos libres${firstDeparture ? ` / desde ${firstDeparture.hora}` : ''}</small>
                    `;
                    button.addEventListener('click', () => chooseBus(sale.busId));
                    busOptionList.appendChild(button);
                });

                if (! options.length) {
                    busOptionList.innerHTML = '<p class="sale-empty">No hay buses que coincidan con la busqueda.</p>';
                }
            }

            function renderDestinations() {
                const initial = sales.find((sale) => sale.id === oldSalidaId) || sales[0];
                selectOptions(destinationSelect, uniqueDestinations().map((sale) => ({
                    value: sale.destinoKey,
                    label: sale.destinoLabel,
                })), initial?.destinoKey);
            }

            function renderBuses(preferredBusId = null) {
                const options = [...new Map(destinationSales().map((sale) => [sale.busId, sale])).values()]
                    .map((sale) => ({ value: sale.busId, label: `${sale.busLabel} / ${sale.cooperativa}` }));
                selectOptions(busSelect, options, preferredBusId || options[0]?.value);
                renderBusFilters();
            }

            function renderDepartures(preferredSalidaId = null) {
                const options = busSales().map((sale) => ({
                    value: sale.id,
                    label: `${sale.fecha} ${sale.hora} / Precio ${money(sale.precioBase)}`,
                }));
                selectOptions(departureSelect, options, preferredSalidaId || options[0]?.value);
                currentSale = sales.find((sale) => String(sale.id) === departureSelect.value) || null;
                salidaInput.value = currentSale?.id || '';
                renderPickerButtons();
            }

            function selectedSeatModels() {
                if (! currentSale) return [];
                return currentSale.asientos.filter((seat) => selectedSeats.has(seat.id) && ! seat.ocupado);
            }

            function renderSeats() {
                seatGrid.innerHTML = '';

                if (! currentSale) {
                    seatGrid.innerHTML = '<p class="sale-empty">No hay salidas disponibles para la seleccion.</p>';
                    availableCount.textContent = '0';
                    renderSelectedRows();
                    return;
                }

                const activeSeatIds = new Set(currentSale.asientos.map((seat) => seat.id));
                [...selectedSeats].forEach((id) => {
                    if (! activeSeatIds.has(id)) selectedSeats.delete(id);
                });

                availableCount.textContent = currentSale.asientos.filter((seat) => ! seat.ocupado).length;

                currentSale.asientos.forEach((seat) => {
                    const selected = selectedSeats.has(seat.id);
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.disabled = seat.ocupado;
                    button.dataset.seatId = seat.id;
                    button.className = [
                        'seat-button',
                        seat.ocupado ? 'seat-busy' : selected ? 'seat-selected' : 'seat-available',
                    ].join(' ');
                    button.innerHTML = `
                        <span class="seat-icon"></span>
                        <strong>Asiento ${seat.numero}</strong>
                        <small>${seat.ocupado ? 'Ocupado' : seat.tipo}</small>
                    `;
                    button.addEventListener('click', () => {
                        selectedSeats.has(seat.id) ? selectedSeats.delete(seat.id) : selectedSeats.add(seat.id);
                        renderSeats();
                    });
                    seatGrid.appendChild(button);
                });

                renderSelectedRows();
            }

            function renderSelectedRows() {
                const seats = selectedSeatModels();
                selectedInputs.innerHTML = '';
                selectedTable.innerHTML = '';

                seats.forEach((seat) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'asiento_ids[]';
                    input.value = seat.id;
                    selectedInputs.appendChild(input);

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${currentSale.destinoLabel}</td>
                        <td>${currentSale.busLabel}</td>
                        <td>${currentSale.hora}</td>
                        <td>${currentSale.anden || '-'}</td>
                        <td><strong>Asiento ${seat.numero}</strong></td>
                        <td>${money(seatPrice(seat))}</td>
                        <td><button type="button" class="remove-seat">X</button></td>
                    `;
                    row.querySelector('button').addEventListener('click', () => {
                        selectedSeats.delete(seat.id);
                        renderSeats();
                    });
                    selectedTable.appendChild(row);
                });

                if (! seats.length) {
                    selectedTable.innerHTML = '<tr><td colspan="7" class="selected-table-empty">Seleccione uno o mas asientos para vender.</td></tr>';
                }

                renderTotals();
            }

            function renderTotals() {
                const total = selectedSeatModels().reduce((sum, seat) => sum + seatPrice(seat), 0);
                const subtotal = total / 1.12;
                const iva = total - subtotal;
                subtotalInput.value = money(subtotal);
                ivaInput.value = money(iva);
                totalInput.value = money(total);
                totalDisplayInput.value = money(total);
                const received = Number(cashReceived.value || 0);
                cashChange.value = money(Math.max(received - total, 0));
                submitButton.disabled = total <= 0;
                openPaymentModalButton.disabled = total <= 0;
            }

            function renderPaymentPanel() {
                document.querySelectorAll('[data-method-panel]').forEach((panel) => {
                    panel.classList.toggle('is-hidden', panel.dataset.methodPanel !== paymentMethod.value);
                });
            }

            destinationSelect.addEventListener('change', () => {
                selectedSeats.clear();
                renderBuses();
                renderDepartures();
                renderSeats();
            });

            busSelect.addEventListener('change', () => {
                selectedSeats.clear();
                renderDepartures();
                renderSeats();
            });

            departureSelect.addEventListener('change', () => {
                selectedSeats.clear();
                currentSale = sales.find((sale) => String(sale.id) === departureSelect.value) || null;
                salidaInput.value = currentSale?.id || '';
                renderSeats();
            });

            discountSelect.addEventListener('change', renderSeats);
            paymentMethod.addEventListener('change', renderPaymentPanel);
            cashReceived.addEventListener('input', renderTotals);
            openPaymentModalButton.addEventListener('click', () => {
                if (! openPaymentModalButton.disabled) openModal(paymentModal, paymentMethod);
            });
            destinationPickerButton.addEventListener('click', () => {
                renderDestinationOptions();
                openModal(destinationModal, destinationSearch);
            });
            busPickerButton.addEventListener('click', () => {
                renderBusFilters();
                renderBusOptions();
                openModal(busModal, busSearch);
            });
            destinationSearch.addEventListener('input', renderDestinationOptions);
            destinationOriginFilter.addEventListener('change', renderDestinationOptions);
            destinationEndFilter.addEventListener('change', renderDestinationOptions);
            busSearch.addEventListener('input', renderBusOptions);
            busCooperativeFilter.addEventListener('change', renderBusOptions);
            busAvailabilityFilter.addEventListener('change', renderBusOptions);
            document.querySelectorAll('[data-close-modal]').forEach((button) => {
                button.addEventListener('click', () => closeModal(document.getElementById(button.dataset.closeModal)));
            });
            [destinationModal, busModal, paymentModal].forEach((modal) => {
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) closeModal(modal);
                });
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeModal(destinationModal);
                    closeModal(busModal);
                    closeModal(paymentModal);
                }
            });

            if (! sales.length) {
                seatGrid.innerHTML = '<p class="sale-empty">No hay salidas programadas disponibles.</p>';
                availableCount.textContent = '0';
                submitButton.disabled = true;
                return;
            }

            const initialSale = sales.find((sale) => sale.id === oldSalidaId) || sales[0];
            renderDestinationFilters();
            renderDestinations();
            renderBuses(initialSale.busId);
            renderDepartures(initialSale.id);
            renderPaymentPanel();
            renderPickerButtons();
            renderSeats();

            if (shouldOpenPaymentModal && ! submitButton.disabled) {
                openModal(paymentModal, paymentMethod);
            }
        })();
    </script>
@endpush
