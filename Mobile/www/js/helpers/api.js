async function apiRequest(ruta, opciones = {}) {
    const token = localStorage.getItem("TOKEN_APP");

    const headers = {
        "Accept": "application/json",
        ...(opciones.headers || {})
    };

    if (!(opciones.body instanceof FormData)) {
        headers["Content-Type"] = "application/json";
    }

    if (token) {
        headers["Authorization"] = "Bearer " + token;
    }

    const respuesta = await fetch(obtenerApiBase() + ruta, {
        ...opciones,
        headers
    });

    let data = null;

    try {
        data = await respuesta.json();
    } catch (error) {
        data = {
            mensaje: "Respuesta no válida del servidor"
        };
    }

    if (!respuesta.ok) {
        throw data;
    }

    return data;
}
