document.getElementById("formRegister").addEventListener("submit", async function (e) {
    e.preventDefault();

    const mensaje = document.getElementById("mensajeRegister");

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

        await apiRequest("/mobile/register", {
            method: "POST",
            body: JSON.stringify(datos)
        });

        mensaje.textContent = "Cuenta creada correctamente. Ya puedes iniciar sesiÃ³n.";

        setTimeout(function () {
            window.location.href = "./login.html";
        }, 1200);
    } catch (error) {
        mensaje.textContent = error.mensaje || "No se pudo crear la cuenta.";
    }
});
