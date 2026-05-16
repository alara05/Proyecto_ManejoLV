document.addEventListener("DOMContentLoaded", async function () {
    const idViaje = localStorage.getItem("ID_VIAJE_SELECCIONADO");
    const contenedor = document.getElementById("detalleViaje");

    try {
        const data = await apiRequest("/mobile/viajes/" + idViaje);
        const viaje = data.data;

        contenedor.innerHTML = `
            ${viaje.foto_bus ? `<img src="${viaje.foto_bus}" alt="Bus">` : ""}
            <h2>${viaje.origen} - ${viaje.destino}</h2>
            <p><strong>Cooperativa:</strong> ${viaje.cooperativa || ""}</p>
            <p><strong>Bus:</strong> ${viaje.numero_bus || ""}</p>
            <p><strong>Placa:</strong> ${viaje.placa || ""}</p>
            <p><strong>Chasis:</strong> ${viaje.chasis || ""}</p>
            <p><strong>CarrocerÃ­a:</strong> ${viaje.carroceria || ""}</p>
            <p><strong>Hora salida:</strong> ${viaje.hora_salida || ""}</p>
            <p><strong>Precio:</strong> $${viaje.precio || "0.00"}</p>
        `;
    } catch (error) {
        contenedor.innerHTML = "<p>No se pudo cargar el detalle del viaje.</p>";
    }
});

document.getElementById("btnElegirAsiento").addEventListener("click", function () {
    window.location.href = "./asientos.html";
});
