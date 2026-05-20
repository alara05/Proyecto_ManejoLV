@extends('layouts.app')

@section('content')
    <style>
        .office-sale-pos {
            color: #e5edf8;
        }

        .office-sale-pos .pos-bar,
        .office-sale-pos .pos-card,
        .office-sale-pos .pos-title {
            border: 1px solid rgb(255 255 255 / 0.1);
            background:
                linear-gradient(180deg, rgb(255 255 255 / 0.1), rgb(255 255 255 / 0.04)),
                rgb(15 23 42 / 0.82);
            box-shadow: 0 24px 70px rgb(0 0 0 / 0.24);
            backdrop-filter: blur(18px);
        }

        .office-sale-pos .pos-bar,
        .office-sale-pos .pos-title {
            border-radius: 1rem;
        }

        .office-sale-pos .pos-card {
            border-radius: 1.15rem;
        }

        .office-sale-pos .pos-kicker,
        .office-sale-pos label > span,
        .office-sale-pos .pos-label {
            color: rgb(186 230 253 / 0.9);
            font-size: 0.72rem;
            font-weight: 950;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .office-sale-pos input,
        .office-sale-pos select,
        .office-sale-pos textarea {
            border: 1px solid rgb(255 255 255 / 0.16);
            border-radius: 0.7rem;
            background: rgb(2 6 23 / 0.62);
            color: #ffffff;
            outline: none;
        }

        .office-sale-pos input:focus,
        .office-sale-pos select:focus,
        .office-sale-pos textarea:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgb(56 189 248 / 0.16);
        }

        .office-sale-pos input[readonly] {
            background: rgb(2 6 23 / 0.42);
            color: rgb(226 232 240 / 0.82);
        }

        .office-sale-pos .pos-primary {
            border: 1px solid #38bdf8;
            border-radius: 0.7rem;
            background: #38bdf8;
            color: #020617;
            font-weight: 950;
            transition: transform 0.18s ease, background-color 0.18s ease;
        }

        .office-sale-pos .pos-primary:hover {
            transform: translateY(-1px);
            background: #7dd3fc;
        }

        .office-sale-pos .pos-primary:disabled {
            cursor: not-allowed;
            opacity: 0.52;
            transform: none;
        }

        .office-sale-pos .pos-soft-button {
            border: 1px solid rgb(255 255 255 / 0.12);
            border-radius: 0.7rem;
            background: rgb(255 255 255 / 0.1);
            color: #ffffff;
            font-weight: 900;
        }

        .office-sale-pos .pos-table-wrap {
            overflow: hidden;
            border: 1px solid rgb(255 255 255 / 0.1);
            border-radius: 0.95rem;
            background: rgb(2 6 23 / 0.28);
        }

        .office-sale-pos table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
            color: #e5edf8;
        }

        .office-sale-pos thead {
            background:
                linear-gradient(90deg, rgb(36 168 255 / 0.16), rgb(236 117 25 / 0.1)),
                rgb(15 23 42 / 0.92);
        }

        .office-sale-pos th,
        .office-sale-pos td {
            padding: 0.85rem 1rem;
            text-align: left;
        }

        .office-sale-pos th {
            color: rgb(224 242 254 / 0.96);
            font-size: 0.72rem;
            font-weight: 950;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .office-sale-pos tbody tr + tr {
            border-top: 1px solid rgb(255 255 255 / 0.08);
        }

        .office-sale-pos .pos-total-box {
            border: 1px solid rgb(255 255 255 / 0.1);
            border-radius: 0.95rem;
            background: rgb(2 6 23 / 0.36);
        }

        .office-sale-pos .pos-remove {
            border: 1px solid rgb(248 113 113 / 0.45);
            border-radius: 0.55rem;
            background: rgb(127 29 29 / 0.26);
            color: #fecaca;
            font-weight: 950;
        }

        .office-sale-pos .seat-button {
            min-height: 4.8rem;
            border-radius: 0.65rem;
            padding: 0.65rem;
            text-align: center;
            font-size: 0.82rem;
            transition: transform 0.18s ease, border-color 0.18s ease, background-color 0.18s ease;
        }

        .office-sale-pos .seat-button:not(:disabled):hover {
            transform: translateY(-2px);
        }

        .office-sale-pos .seat-available {
            border: 1px solid rgb(255 255 255 / 0.14);
            background: linear-gradient(180deg, rgb(255 255 255 / 0.12), rgb(255 255 255 / 0.055));
            color: #f8fafc;
        }

        .office-sale-pos .seat-selected {
            border: 1px solid rgb(56 189 248 / 0.72);
            background: linear-gradient(180deg, rgb(56 189 248 / 0.36), rgb(36 168 255 / 0.18));
            color: #ffffff;
            box-shadow: 0 16px 34px rgb(56 189 248 / 0.12);
        }

        .office-sale-pos .seat-busy {
            border: 1px solid rgb(244 63 94 / 0.48);
            background: linear-gradient(180deg, rgb(244 63 94 / 0.35), rgb(127 29 29 / 0.38));
            color: #ffe4e6;
            cursor: not-allowed;
        }
    </style>

    <div class="office-sale-pos">
        <div class="pos-bar mb-4 flex flex-wrap items-center gap-2 px-5 py-4 text-sm font-bold text-slate-300">
            <span>Panel</span>
            <span class="text-slate-500">/</span>
            <span class="text-white">Vender boleto</span>
        </div>

        <div class="pos-title mb-5 flex flex-wrap items-center justify-between gap-3 px-5 py-4">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-sky-400 text-xl font-black text-slate-950">+</span>
                <div>
                    <p class="pos-kicker">Venta interna de oficinista</p>
                    <h1 class="text-2xl font-black text-white">Nuevo registro de una venta</h1>
                </div>
            </div>
            <div class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-black text-white">
                <span id="available-count">0</span> asientos disponibles
            </div>
        </div>

        @include('partials.flash')

        @if ($errors->any())
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <p class="font-bold">Revisa los datos de la venta.</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('boletos.store') }}" id="office-sale-form" class="grid gap-5 xl:grid-cols-[minmax(0,1.45fr)_minmax(430px,0.75fr)]">
            @csrf
            <input type="hidden" name="salida_id" id="salida_id" value="{{ old('salida_id', $salida?->id) }}">
            <input type="hidden" name="tipo_asiento_id" id="tipo_asiento_id" value="{{ old('tipo_asiento_id') }}">
            <div id="selected-seat-inputs"></div>

            <section class="pos-card p-5">
                <div class="mb-5 flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-white/10 text-lg font-black text-sky-100">B</span>
                    <h2 class="text-xl font-black text-white">Detalle de boletos</h2>
                </div>

                <div class="grid gap-4 lg:grid-cols-3">
                    <label>
                        <span>Destino</span>
                        <select id="destination-select" class="mt-2 min-h-11 w-full px-3 text-sm font-bold"></select>
                    </label>

                    <label>
                        <span>Bus</span>
                        <select id="bus-select" class="mt-2 min-h-11 w-full px-3 text-sm font-bold"></select>
                    </label>

                    <label>
                        <span>H.Salida / Precio</span>
                        <select id="departure-select" class="mt-2 min-h-11 w-full px-3 text-sm font-bold"></select>
                    </label>
                </div>

                <div class="mt-6 border-t border-white/10 pt-5">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <p class="pos-label">Seleccione asiento(s)</p>
                        <div class="flex flex-wrap gap-2 text-xs font-black">
                            <span class="rounded-full bg-white/10 px-3 py-1 text-white">Disponible</span>
                            <span class="rounded-full bg-sky-400/25 px-3 py-1 text-sky-100">Seleccionado</span>
                            <span class="rounded-full bg-red-100 px-3 py-1 text-red-700">Ocupado</span>
                        </div>
                    </div>

                    <div id="seat-grid" class="grid gap-3 sm:grid-cols-3 md:grid-cols-5 2xl:grid-cols-6"></div>
                    @error('asiento_ids')
                        <p class="mt-3 text-sm font-bold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pos-table-wrap mt-5 overflow-x-auto">
                    <table>
                        <thead>
                            <tr>
                                <th>Destino</th>
                                <th>Bus</th>
                                <th>H.Salida</th>
                                <th>Asiento</th>
                                <th>Precio</th>
                                <th>Opcion</th>
                            </tr>
                        </thead>
                        <tbody id="selected-seat-table">
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">Seleccione uno o mas asientos para vender.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <aside class="pos-card overflow-hidden">
                <div class="p-5">
                    <label>
                        <span>Cliente</span>
                        <input name="pasajero_nombre" type="text" value="{{ old('pasajero_nombre', 'Consumidor Final') }}" required
                            class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                    </label>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <label>
                            <span>Cedula</span>
                            <input name="pasajero_cedula" type="text" value="{{ old('pasajero_cedula', '9999999999') }}" maxlength="10" required
                                class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                        </label>

                        <label>
                            <span>Descuento</span>
                            <select id="discount-select" name="tipo_descuento" required class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                                @foreach ($descuentos as $value => $label)
                                    <option value="{{ $value }}" data-percent="{{ in_array($value, ['menor_edad', 'discapacidad', 'tercera_edad'], true) ? 50 : 0 }}" @selected(old('tipo_descuento', 'ninguno') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <div class="mt-5 grid gap-4 md:grid-cols-3">
                        <label>
                            <span>Metodo de pagos</span>
                            <select id="payment-method" name="metodo_pago" required class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                                @foreach ($metodosPago as $value => $label)
                                    <option value="{{ $value }}" @selected(old('metodo_pago', 'efectivo') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label>
                            <span>Comprobante</span>
                            <select name="comprobante_tipo" required class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                                <option value="ticket" @selected(old('comprobante_tipo', 'ticket') === 'ticket')>Ticket</option>
                                <option value="factura" @selected(old('comprobante_tipo') === 'factura')>Factura</option>
                            </select>
                        </label>

                        <label>
                            <span>Total a pagar</span>
                            <input id="total-display-input" type="text" value="$0.00" readonly class="mt-2 min-h-11 w-full px-3 text-sm font-black">
                        </label>
                    </div>

                    <div class="payment-panel mt-5" data-method-panel="efectivo">
                        <div class="grid gap-4 md:grid-cols-2">
                            <label>
                                <span>Efectivo</span>
                                <input id="cash-received" name="efectivo_recibido" type="number" step="0.01" min="0" value="{{ old('efectivo_recibido') }}"
                                    class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                            </label>
                            <label>
                                <span>Cambio</span>
                                <input id="cash-change" type="text" value="$0.00" readonly class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                            </label>
                        </div>
                    </div>

                    <div class="payment-panel mt-5 hidden" data-method-panel="tarjeta">
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="md:col-span-2">
                                <span>Titular de la tarjeta</span>
                                <input name="tarjeta_titular" type="text" value="{{ old('tarjeta_titular') }}" class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                            </label>
                            <label>
                                <span>Marca</span>
                                <select name="tarjeta_marca" class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                                    <option value="">Seleccione</option>
                                    <option value="Visa" @selected(old('tarjeta_marca') === 'Visa')>Visa</option>
                                    <option value="Mastercard" @selected(old('tarjeta_marca') === 'Mastercard')>Mastercard</option>
                                    <option value="American Express" @selected(old('tarjeta_marca') === 'American Express')>American Express</option>
                                </select>
                            </label>
                            <label>
                                <span>Ultimos 4 digitos</span>
                                <input name="tarjeta_ultimos4" type="text" inputmode="numeric" maxlength="4" value="{{ old('tarjeta_ultimos4') }}" class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                            </label>
                            <label class="md:col-span-2">
                                <span>Codigo de autorizacion</span>
                                <input name="tarjeta_autorizacion" type="text" value="{{ old('tarjeta_autorizacion') }}" class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                            </label>
                        </div>
                    </div>

                    <div class="payment-panel mt-5 hidden" data-method-panel="transferencia">
                        <div class="grid gap-4">
                            <label>
                                <span>Banco emisor</span>
                                <input name="transferencia_banco" type="text" value="{{ old('transferencia_banco') }}" class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                            </label>
                            <label>
                                <span>Titular de la cuenta</span>
                                <input name="transferencia_titular" type="text" value="{{ old('transferencia_titular') }}" class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                            </label>
                            <label>
                                <span>Referencia bancaria</span>
                                <input name="transferencia_referencia" type="text" value="{{ old('transferencia_referencia') }}" class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                            </label>
                        </div>
                    </div>

                    <div class="payment-panel mt-5 hidden" data-method-panel="deposito">
                        <div class="grid gap-4">
                            <label>
                                <span>Banco receptor</span>
                                <input name="deposito_banco" type="text" value="{{ old('deposito_banco') }}" class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                            </label>
                            <label>
                                <span>Depositante</span>
                                <input name="deposito_titular" type="text" value="{{ old('deposito_titular') }}" class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                            </label>
                            <label>
                                <span>Numero de deposito</span>
                                <input name="deposito_numero" type="text" value="{{ old('deposito_numero') }}" class="mt-2 min-h-11 w-full px-3 text-sm font-bold">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="border-t border-white/10 p-5">
                    <div class="pos-total-box p-4">
                        <div class="grid gap-3 text-sm">
                            <div class="grid grid-cols-[1fr_1.1fr] items-center gap-3">
                                <span class="font-black text-white">SUBTOTAL</span>
                                <input id="subtotal-input" type="text" value="$0.00" readonly class="min-h-10 px-3 text-sm font-bold">
                            </div>
                            <div class="grid grid-cols-[1fr_1.1fr] items-center gap-3">
                                <span class="font-black text-white">IVA %</span>
                                <input type="text" value="12%" readonly class="min-h-10 px-3 text-sm font-bold">
                            </div>
                            <div class="grid grid-cols-[1fr_1.1fr] items-center gap-3">
                                <span class="font-black text-white">IVA</span>
                                <input id="iva-input" type="text" value="$0.00" readonly class="min-h-10 px-3 text-sm font-bold">
                            </div>
                            <div class="grid grid-cols-[1fr_1.1fr] items-center gap-3">
                                <span class="font-black text-white">TOTAL</span>
                                <input id="total-input" type="text" value="$0.00" readonly class="min-h-10 px-3 text-sm font-black">
                            </div>
                        </div>
                    </div>

                    <label class="mt-4 block">
                        <span>Observacion</span>
                        <textarea name="observacion_pago" rows="3" class="mt-2 w-full px-3 py-2 text-sm font-bold">{{ old('observacion_pago') }}</textarea>
                    </label>

                    <button id="submit-sale" type="submit" class="pos-primary mt-5 min-h-12 w-full px-4 py-2" disabled>
                        Confirmar venta
                    </button>
                </div>
            </aside>
        </form>
    </div>

    <script>
        (() => {
            const sales = @json($salesData);
            const oldSalidaId = Number(@json((int) old('salida_id', $salida?->id ?? 0)));
            const oldSeatIds = new Set((@json((array) old('asiento_ids', []))).map((id) => Number(id)));
            const destinationSelect = document.getElementById('destination-select');
            const busSelect = document.getElementById('bus-select');
            const departureSelect = document.getElementById('departure-select');
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

            function uniqueDestinations() {
                return [...new Map(sales.map((sale) => [sale.destinoKey, sale])).values()];
            }

            function destinationSales() {
                return sales.filter((sale) => sale.destinoKey === destinationSelect.value);
            }

            function busSales() {
                return destinationSales().filter((sale) => String(sale.busId) === busSelect.value);
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
            }

            function renderDepartures(preferredSalidaId = null) {
                const options = busSales().map((sale) => ({
                    value: sale.id,
                    label: `${sale.fecha} ${sale.hora} / Precio ${money(sale.precioBase)}`,
                }));
                selectOptions(departureSelect, options, preferredSalidaId || options[0]?.value);
                currentSale = sales.find((sale) => String(sale.id) === departureSelect.value) || null;
                salidaInput.value = currentSale?.id || '';
            }

            function renderSeats() {
                seatGrid.innerHTML = '';

                if (! currentSale) {
                    seatGrid.innerHTML = '<p class="col-span-full rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">No hay salidas disponibles para la seleccion.</p>';
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
                        <span class="mx-auto mb-1 grid h-7 w-7 place-items-center rounded-lg border border-white/20 bg-black/20 font-black">S</span>
                        <strong class="block">Asiento ${seat.numero}</strong>
                        <small class="mt-0.5 block">${seat.ocupado ? 'Ocupado' : seat.tipo}</small>
                    `;
                    button.addEventListener('click', () => {
                        selectedSeats.has(seat.id) ? selectedSeats.delete(seat.id) : selectedSeats.add(seat.id);
                        renderSeats();
                    });
                    seatGrid.appendChild(button);
                });

                renderSelectedRows();
            }

            function selectedSeatModels() {
                if (! currentSale) return [];
                return currentSale.asientos.filter((seat) => selectedSeats.has(seat.id) && ! seat.ocupado);
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
                        <td><strong>Asiento ${seat.numero}</strong></td>
                        <td>${money(seatPrice(seat))}</td>
                        <td><button type="button" class="pos-remove px-2.5 py-1 text-xs">X</button></td>
                    `;
                    row.querySelector('button').addEventListener('click', () => {
                        selectedSeats.delete(seat.id);
                        renderSeats();
                    });
                    selectedTable.appendChild(row);
                });

                if (! seats.length) {
                    selectedTable.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-slate-400">Seleccione uno o mas asientos para vender.</td></tr>';
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
            }

            function renderPaymentPanel() {
                document.querySelectorAll('[data-method-panel]').forEach((panel) => {
                    panel.classList.toggle('hidden', panel.dataset.methodPanel !== paymentMethod.value);
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

            if (! sales.length) {
                seatGrid.innerHTML = '<p class="col-span-full rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">No hay salidas programadas disponibles.</p>';
                submitButton.disabled = true;
                return;
            }

            const initialSale = sales.find((sale) => sale.id === oldSalidaId) || sales[0];
            renderDestinations();
            renderBuses(initialSale.busId);
            renderDepartures(initialSale.id);
            renderPaymentPanel();
            renderSeats();
        })();
    </script>
@endsection
