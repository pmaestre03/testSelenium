function redirigir() {

    var urlActual = window.location.href;

    if (urlActual.includes("index.php")) {
        // Redirigir a otra página
        window.location.href = "login.php";
    }
}