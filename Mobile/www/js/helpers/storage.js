const StorageApp = {
    set(clave, valor) {
        localStorage.setItem(clave, JSON.stringify(valor));
    },

    get(clave) {
        const valor = localStorage.getItem(clave);

        if (!valor) {
            return null;
        }

        try {
            return JSON.parse(valor);
        } catch (error) {
            return valor;
        }
    },

    remove(clave) {
        localStorage.removeItem(clave);
    },

    clear() {
        localStorage.clear();
    }
};
