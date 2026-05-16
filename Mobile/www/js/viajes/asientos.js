document.addEventListener("DOMContentLoaded", async function () {
    const idViaje = localStorage.getItem("ID_VIAJE_SELECCIONADO");
    const mapa = document.getElementById("mapaAsientos");

    try {
        mapa.innerHTML = "<p>Cargando asientos...</p>";

        const data = await apiRequest("/mobile/viajes/" + idViaje + "/asientos");

        mapa.innerHTML = "";

        if (!data.data || data.data.length === 0) {
            mapa.innerHTML = "<p>No hay asientos disponibles.</p>";
            return;
        }

        data.data.forEach(function (asiento) {
            const btn = document.createElement("button");
            btn.className = "asiento " + (asiento.estado === "OCUPADO" ? "ocupado" : "");
            btn.textContent = asiento.numero;

            if (asiento.estado !== "OCUPADO") {
                btn.addEventListener("click", function () {
                    document.querySelectorAll(".asiento").forEach(a => a.classList.remove("seleccionado"));
                    btn.classList.add("seleccionado");
                    localStorage.setItem("ID_ASIENTO_SELECCIONADO", asiento.id);
                    localStorage.setItem("NUM_ASIENTO_SELECCIONADO", asiento.numero);
                });
            }

            mapa.appendChild(btn);
        });
    } catch (error) {
        mapa.innerHTML = "<p>Error al cargar asientos.</p>";
    }
});

document.getElementById("btnContinuarCompra").addEventListener("click", function () {
    if (!localStorage.getItem("ID_ASIENTO_SELECCIONADO")) {
        alert("Selecciona un asiento.");
        return;
    }

    window.location.href = "../compra/datos-pasajero.html";
});
