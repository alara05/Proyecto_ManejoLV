document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("apiUrl");
    const btn = document.getElementById("btnGuardarServidor");
    const mensaje = document.getElementById("mensajeServidor");

    input.value = localStorage.getItem("API_BASE_URL") || "";

    btn.addEventListener("click", function () {
        const url = input.value.trim();

        if (!url) {
            mensaje.textContent = "Ingresa la URL del servidor.";
            return;
        }

        guardarServidorManual(url);
        mensaje.textContent = "Servidor guardado correctamente.";
    });
});
