document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formLoginMobile");
    const mensaje = document.getElementById("mensajeLoginMobile");
    const btnConfig = document.getElementById("btnConfigServidor");
    const panelServidor = document.getElementById("panelServidor");
    const inputApiUrl = document.getElementById("apiUrl");
    const btnGuardarServidor = document.getElementById("btnGuardarServidor");

    inputApiUrl.value = localStorage.getItem("API_BASE_URL") || CONFIG_APP.API_BASE_URL;

    function mostrarMensaje(texto, tipo) {
        mensaje.textContent = texto;
        mensaje.className = "mensaje " + tipo;
    }

    btnConfig.addEventListener("click", function () {
        panelServidor.classList.toggle("activo");
    });

    btnGuardarServidor.addEventListener("click", function () {
        const url = inputApiUrl.value.trim();

        if (!url) {
            mostrarMensaje("Ingresa la URL del servidor Laravel.", "error");
            return;
        }

        guardarServidorManual(url);
        mostrarMensaje("Servidor guardado correctamente.", "ok");
    });

    form.addEventListener("submit", async function (event) {
        event.preventDefault();

        const datos = {
            email: document.getElementById("email").value.trim(),
            password: document.getElementById("password").value.trim()
        };

        mostrarMensaje("Validando usuario...", "info");

        try {
            const data = await apiRequest("/mobile/login", {
                method: "POST",
                body: JSON.stringify(datos)
            });

            localStorage.setItem("TOKEN_APP", data.token || "");
            localStorage.setItem("USUARIO_APP", JSON.stringify(data.usuario || {}));

            mostrarMensaje("Acceso correcto.", "ok");

            setTimeout(function () {
                window.location.href = "../home/dashboard.html";
            }, 700);
        } catch (error) {
            mostrarMensaje(
                error.mensaje || "El login mobile ya está creado. Falta conectar la API /api/mobile/login.",
                "error"
            );
        }
    });
});
