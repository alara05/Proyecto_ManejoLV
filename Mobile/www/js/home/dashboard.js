document.addEventListener("DOMContentLoaded", function () {
    const usuario = StorageApp.get("USUARIO_APP");
    const nombreUsuario = document.getElementById("nombreUsuario");
    const btnSalir = document.getElementById("btnSalir");

    if (usuario && usuario.nombre) {
        nombreUsuario.textContent = usuario.nombre;
    }

    btnSalir.addEventListener("click", function () {
        localStorage.removeItem("TOKEN_APP");
        localStorage.removeItem("USUARIO_APP");
        window.location.href = "../auth/login.html";
    });
});
