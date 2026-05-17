document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formRegister");
    const mensaje = document.getElementById("mensajeRegister");

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const datos = {
            nombre: document.getElementById("nombre").value.trim(),
            apellido: document.getElementById("apellido").value.trim(),
            cedula: document.getElementById("cedula").value.trim(),
            telefono: document.getElementById("telefono").value.trim(),
            email: document.getElementById("email").value.trim(),
            password: document.getElementById("password").value.trim()
        };

        try {
            mensaje.textContent = "Creando cuenta...";

            const data = await apiRequest("/mobile/register", {
                method: "POST",
                body: JSON.stringify(datos)
            });

            localStorage.setItem("TOKEN_APP", data.token || "");
            localStorage.setItem("USUARIO_APP", JSON.stringify(data.usuario || {}));

            mensaje.textContent = "Cuenta creada correctamente. Redirigiendo...";

            setTimeout(function () {
                window.location.href = "../home/dashboard.html";
            }, 800);
        } catch (error) {
            mensaje.textContent = error.mensaje || "No se pudo crear la cuenta.";
        }
    });
});
