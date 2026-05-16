const CACHE_NAME = "pasajes-usuario-v1";

const ARCHIVOS_CACHE = [
    "./index.html",
    "./pages/auth/login.html",
    "./pages/auth/register.html",
    "./pages/home/dashboard.html",
    "./pages/viajes/buscar.html",
    "./pages/viajes/detalle.html",
    "./pages/viajes/asientos.html",
    "./pages/compra/datos-pasajero.html",
    "./pages/pago/comprobante.html",
    "./pages/boletos/mis-boletos.html",
    "./css/app.css",
    "./js/config.js"
];

self.addEventListener("install", function (event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function (cache) {
            return cache.addAll(ARCHIVOS_CACHE);
        })
    );
});

self.addEventListener("fetch", function (event) {
    event.respondWith(
        caches.match(event.request).then(function (respuesta) {
            return respuesta || fetch(event.request);
        })
    );
});
