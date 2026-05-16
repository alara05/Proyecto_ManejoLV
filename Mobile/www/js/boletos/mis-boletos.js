document.addEventListener("DOMContentLoaded", async function () {
    const lista = document.getElementById("listaBoletos");

    try {
        lista.innerHTML = "<p>Cargando tus boletos...</p>";

        const data = await apiRequest("/mobile/boletos");

        lista.innerHTML = "";

        if (!data.data || data.data.length === 0) {
            lista.innerHTML = "<p>No tienes boletos registrados.</p>";
            return;
        }

        data.data.forEach(function (boleto) {
            const item = document.createElement("article");
            item.className = "item-card";
            item.innerHTML = `
                <h3>${boleto.codigo}</h3>
                <p>${boleto.origen} - ${boleto.destino}</p>
                <p>Fecha: ${boleto.fecha}</p>
                <span class="estado-boleto">${boleto.estado}</span>
                <button onclick="verBoleto(${boleto.id})">Ver boleto</button>
            `;
            lista.appendChild(item);
        });
    } catch (error) {
        lista.innerHTML = "<p>Error al cargar boletos.</p>";
    }
});

function verBoleto(id) {
    localStorage.setItem("ID_BOLETO_SELECCIONADO", id);
    window.location.href = "./detalle-boleto.html";
}
