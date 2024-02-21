<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Sitio Web</title>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js"></script>
</head>
<body class="header">
    <header class="header">

        <?php
        session_start();

        if (isset($_SESSION['usuario'])) {
            // Si el usuario esta logueado
            echo "<button class='button button-signup' id='none'>Bienvenido, " . $_SESSION['usuario'] . "</button>";
            //redireccion boton signup
            echo '<script>';
            echo '$(document).ready(function() {';
            echo '    $("#signupButton").on("click", function() {';
            echo '        window.location.href = "register.php";';
            echo '    });';
            echo '});';
            echo '</script>';

            echo '<h1 class="title">VotaPAX</h1>';
            echo '<div class="button-container">';

            echo '<button class="button button-secund" id="homeButton"><img src="./imagenes/home.png"></button>';

            //redireccion boton home
            echo '<script>';
            echo '$(document).ready(function() {';
            echo '    $("#homeButton").on("click", function() {';
            echo '        window.location.href = "index.php";';
            echo '    });';
            echo '});';
            echo '</script>';

            echo '<button class="button button-secund" id="dashboardButton">Mi area</button>';
            //redireccion boton home
            echo '<script>';
            echo '$(document).ready(function() {';
            echo '    $("#dashboardButton").on("click", function() {';
            echo '        window.location.href = "dashboard.php";';
            echo '    });';
            echo '});';
            echo '</script>';
          echo '<button class="button button-login" id="logoutButton"> <a href="logout.php">Cerrar Sesión</a></button>';
            //redireccion boton logout
            echo '<script>';
            echo '$(document).ready(function() {';
            echo '    $("#logoutButton").on("click", function() {';
            echo '        window.location.href = "logout.php";';
            echo '    });';
            echo '});';
            echo '</script>';

            echo '</div>';
        } else {
            // Si el usuario no est   logueado
            echo "<button class='button' id='signupButton'>Crear Cuenta</button>";
            //redireccion boton signup
            echo '<script>';
            echo '$(document).ready(function() {';
            echo '    $("#signupButton").on("click", function() {';
            echo '        window.location.href = "register.php";';
            echo '    });';
            echo '});';
            echo '</script>';

            echo '<h1 class="title_logout">VotaPAX</h1>';
            echo '<div class="button-container">';

            echo '<button class="button button-secund" id="homeButton"><img src="./imagenes/home.png"></button>';
            //redireccion boton home
            echo '<script>';
            echo '$(document).ready(function() {';
            echo '    $("#homeButton").on("click", function() {';
            echo '        window.location.href = "index.php";';
            echo '    });';
            echo '});';
            echo '</script>';

            echo '<button class="button button-login" id="loginButton">Iniciar Sesión</button>';
            //redireccion boton login
            echo '<script>';
            echo '$(document).ready(function() {';
            echo '    $("#loginButton").on("click", function() {';
            echo '        window.location.href = "login.php";';
            echo '    });';
            echo '});';
            echo '</script>';

            echo '</div>';
        }
        ?>

    </header>
</body>
</html>
