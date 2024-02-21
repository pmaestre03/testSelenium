<?php
session_start();

$nombreUsuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario';

// Limpiar y destruir la sesi  n
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="Utilidades/styles.css?no-cache=<?php echo time(); ?>">
    <script>
        setTimeout(function() {
            window.location.href = "index.php";
        }, 4000); // milisegundos
    </script>
</head>
<body class="logout">
<div id="notification-container"></div>
    <?php include("Utilidades/header.php") ?>
    
    <div class="logout-container">
        <p>Hasta pronto, <?php echo $nombreUsuario; ?></p>
    </div>
    <?php registrarEvento("Sesión cerrada por el usuario: ".$_SESSION['email']); ?>
    <?php include("Utilidades/footer.php") ?>
</body>
</html>
