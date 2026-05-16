const CONFIG_APP = {
    API_BASE_URL: localStorage.getItem("API_BASE_URL") || "http://192.168.0.103:8086",
    API_PATH: "/api"
};

function obtenerApiBase() {
    return CONFIG_APP.API_BASE_URL + CONFIG_APP.API_PATH;
}

function guardarServidorManual(url) {
    localStorage.setItem("API_BASE_URL", url);
    CONFIG_APP.API_BASE_URL = url;
}
