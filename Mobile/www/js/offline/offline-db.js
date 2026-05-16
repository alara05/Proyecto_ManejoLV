const OfflineDB = {
    guardar(clave, datos) {
        const lista = JSON.parse(localStorage.getItem(clave) || "[]");
        lista.push(datos);
        localStorage.setItem(clave, JSON.stringify(lista));
    },

    listar(clave) {
        return JSON.parse(localStorage.getItem(clave) || "[]");
    },

    limpiar(clave) {
        localStorage.removeItem(clave);
    }
};
