document.addEventListener("DOMContentLoaded", async function () {
    try {
        const data = await apiRequest("/mobile/perfil");
        const usuario = data.data;

        document.getElementById("nombre").value = usuario.nombre || "";
        document.getElementById("apellido").value = usuario.apellido || "";
        document.getElementById("cedula").value = usuario.cedula || "";
        document.getElementById("telefono").value = usuario.telefono || "";
        document.getElementById("email").value = usuario.email || "";
    } catch (error) {
        console.log(error);
    }
});

document.getElementById("formPerfil").addEventListener("submit", async function (e) {
    e.preventDefault();

    const mensaje = document.getElementById("mensajePerfil");

    const datos = {
        nombre: document.getElementById("nombre").value.trim(),
        apellido: document.getElementById("apellido").value.trim(),
        cedula: document.getElementById("cedula").value.trim(),
        telefono: document.getElementById("telefono").value.trim(),
        email: document.getElementById("email").value.trim()
    };

    try {
        await apiRequest("/mobile/perfil", {
            method: "POST",
            body: JSON.stringify(datos)
        });

        mensaje.textContent = "Perfil actualizado correctamente.";
    } catch (error) {
        mensaje.textContent = error.mensaje || "No se pudo actualizar el perfil.";
    }
});
