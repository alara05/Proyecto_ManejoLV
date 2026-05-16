document.addEventListener("DOMContentLoaded", async function () {
    const lista = document.getElementById("listaNotificaciones");

    try {
        lista.innerHTML = "<p>Cargando notificaciones...</p>";

        const data = await apiRequest("/mobile/notificaciones");

        lista.innerHTML = "";

        if (!data.data || data.data.length === 0) {
            lista.innerHTML = "<p>No tienes notificaciones.</p>";
            return;
        }

        data.data.forEach(function (notificacion) {
            const item = document.createElement("article");
            item.className = "item-card";
            item.innerHTML = `
                <h3>${notificacion.titulo}</h3>
                <p>${notificacion.mensaje}</p>
                <small>${notificacion.fecha || ""}</small>
            `;
            lista.appendChild(item);
        });
    } catch (error) {
        lista.innerHTML = "<p>Error al cargar notificaciones.</p>";
    }
});
