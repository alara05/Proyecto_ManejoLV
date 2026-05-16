(function validarSesionMobile() {
    const token = localStorage.getItem("TOKEN_APP");

    if (!token) {
        window.location.href = "../auth/login.html";
    }
})();
