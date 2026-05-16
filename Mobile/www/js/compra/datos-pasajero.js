document.getElementById("formPasajero").addEventListener("submit", async function (e) {
    e.preventDefault();

    const mensaje = document.getElementById("mensajePasajero");

    const datosCompra = {
        id_viaje: localStorage.getItem("ID_VIAJE_SELECCIONADO"),
        id_asiento: localStorage.getItem("ID_ASIENTO_SELECCIONADO"),
        pasajero: {
            nombre: document.getElementById("nombrePasajero").value.trim(),
            apellido: document.getElementById("apellidoPasajero").value.trim(),
            cedula: document.getElementById("cedulaPasajero").value.trim(),
            fecha_nacimiento: document.getElementById("fechaNacimiento").value,
            tipo_tarifa: document.getElementById("tipoTarifa").value,
            carnet_discapacidad: document.getElementById("carnetDiscapacidad").value.trim()
        }
    };

    try {
        mensaje.textContent = "Registrando compra...";

        const data = await apiRequest("/mobile/compra", {
            method: "POST",
            body: JSON.stringify(datosCompra)
        });

        localStorage.setItem("ID_COMPRA_ACTUAL", data.id_compra);
        localStorage.setItem("RESUMEN_COMPRA", JSON.stringify(data.resumen || {}));

        window.location.href = "../pago/comprobante.html";
    } catch (error) {
        mensaje.textContent = error.mensaje || "No se pudo registrar la compra.";
    }
});
