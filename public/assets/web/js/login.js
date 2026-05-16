document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formLoginWeb");
    const mensaje = document.getElementById("mensajeLogin");

    function mostrarMensaje(texto, tipo) {
        mensaje.textContent = texto;
        mensaje.className = "mensaje " + tipo;
    }

    form.addEventListener("submit", async function (event) {
        event.preventDefault();

        const datos = {
            email: document.getElementById("email").value.trim(),
            password: document.getElementById("password").value.trim()
        };

        mostrarMensaje("Validando usuario...", "info");

        try {
            const respuesta = await fetch("/api/web/login", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify(datos)
            });

            const data = await respuesta.json().catch(function () {
                return {};
            });

            if (!respuesta.ok) {
                throw data;
            }

            if (data.token) {
                localStorage.setItem("TOKEN_APP", data.token);
            }

            if (data.usuario) {
                localStorage.setItem("USUARIO_APP", JSON.stringify(data.usuario));
            }

            mostrarMensaje("Acceso correcto. Redirigiendo...", "ok");

            setTimeout(function () {
                window.location.href = data.redirect || "/";
            }, 700);
        } catch (error) {
            mostrarMensaje(
                error.mensaje || "El login visual ya está creado. Falta conectar la API /api/web/login.",
                "error"
            );
        }
    });
});
