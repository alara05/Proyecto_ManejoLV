document.addEventListener("DOMContentLoaded", function () {
    const resumen = StorageApp.get("RESUMEN_COMPRA");
    const contenedor = document.getElementById("resumenCompra");

    if (resumen) {
        contenedor.innerHTML = `
            <h2>Resumen de compra</h2>
            <p><strong>Ruta:</strong> ${resumen.ruta || ""}</p>
            <p><strong>Asiento:</strong> ${localStorage.getItem("NUM_ASIENTO_SELECCIONADO") || ""}</p>
            <p><strong>Total:</strong> $${resumen.total || "0.00"}</p>
        `;
    }
});

document.getElementById("formComprobante").addEventListener("submit", async function (e) {
    e.preventDefault();

    const mensaje = document.getElementById("mensajePago");
    const idCompra = localStorage.getItem("ID_COMPRA_ACTUAL");

    const formData = new FormData();
    formData.append("id_compra", idCompra);
    formData.append("metodo_pago", document.getElementById("metodoPago").value);
    formData.append("banco", document.getElementById("banco").value.trim());
    formData.append("numero_comprobante", document.getElementById("numeroComprobante").value.trim());

    const archivo = document.getElementById("archivoComprobante").files[0];

    if (archivo) {
        formData.append("comprobante", archivo);
    }

    try {
        mensaje.textContent = "Enviando comprobante...";

        await apiRequest("/mobile/pago/comprobante", {
            method: "POST",
            body: formData
        });

        alert("Comprobante enviado. Un oficinista validarÃ¡ tu pago.");
        window.location.href = "../boletos/mis-boletos.html";
    } catch (error) {
        mensaje.textContent = error.mensaje || "No se pudo enviar el comprobante.";
    }
});
