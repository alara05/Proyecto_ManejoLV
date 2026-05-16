document.getElementById("formBuscarViaje").addEventListener("submit", async function (e) {
    e.preventDefault();

    const resultados = document.getElementById("resultadosViaje");

    const params = new URLSearchParams({
        origen: document.getElementById("origen").value.trim(),
        destino: document.getElementById("destino").value.trim(),
        fecha: document.getElementById("fecha").value,
        cooperativa: document.getElementById("cooperativa").value.trim(),
        tipo_asiento: document.getElementById("tipoAsiento").value,
        tipo_viaje: document.getElementById("tipoViaje").value
    });

    try {
        resultados.innerHTML = "<p>Buscando viajes disponibles...</p>";

        const data = await apiRequest("/mobile/viajes/buscar?" + params.toString());

        resultados.innerHTML = "";

        if (!data.data || data.data.length === 0) {
            resultados.innerHTML = "<p>No se encontraron viajes disponibles.</p>";
            return;
        }

        data.data.forEach(function (viaje) {
            const item = document.createElement("article");
            item.className = "item-card";
            item.innerHTML = `
                <h3>${viaje.origen} - ${viaje.destino}</h3>
                <p>${viaje.cooperativa || ""}</p>
                <p>Salida: ${viaje.hora_salida || ""}</p>
                <p>Tipo: ${viaje.tipo_viaje || ""}</p>
                <p class="precio">$${viaje.precio || "0.00"}</p>
                <button onclick="seleccionarViaje(${viaje.id})">Ver detalle</button>
            `;
            resultados.appendChild(item);
        });
    } catch (error) {
        resultados.innerHTML = "<p>Error al buscar viajes.</p>";
    }
});

function seleccionarViaje(id) {
    localStorage.setItem("ID_VIAJE_SELECCIONADO", id);
    window.location.href = "./detalle.html";
}
