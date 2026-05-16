document.addEventListener("DOMContentLoaded", async function () {
    const idBoleto = localStorage.getItem("ID_BOLETO_SELECCIONADO");
    const contenedor = document.getElementById("detalleBoleto");

    try {
        const data = await apiRequest("/mobile/boletos/" + idBoleto);
        const boleto = data.data;

        contenedor.innerHTML = `
            <h2>${boleto.codigo}</h2>
            <p><strong>Pasajero:</strong> ${boleto.pasajero}</p>
            <p><strong>Ruta:</strong> ${boleto.origen} - ${boleto.destino}</p>
            <p><strong>Asiento:</strong> ${boleto.asiento}</p>
            <p><strong>Estado:</strong> ${boleto.estado}</p>
            ${boleto.qr_url ? `<img src="${boleto.qr_url}" alt="QR del boleto">` : ""}
            ${boleto.pdf_url ? `<a href="${boleto.pdf_url}" target="_blank"><button>Ver PDF</button></a>` : ""}
        `;
    } catch (error) {
        contenedor.innerHTML = "<p>No se pudo cargar el boleto.</p>";
    }
});
