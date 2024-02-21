<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Sitio Web</title>
    <link rel="stylesheet" href="Utilidades/styles.css">
</head>
<body class="index">
    <?php include("Utilidades/header.php") ?>
    <img class="papeletas papeleta1" src="imagenes/papeleta.jpg">
    <img class="papeletas papeleta2" src="imagenes/papeleta.jpg">
    <img class="papeletas papeleta3" src="imagenes/papeleta.jpg">
    <img class="papeletas papeleta4" src="imagenes/papeleta.jpg">
    <img class="papeletas papeleta5" src="imagenes/papeleta.jpg">
    <img class="papeletas papeleta6" src="imagenes/papeleta.jpg">
    <div class="contenedor_indice">
    <img src="./imagenes/votaciones.jpeg" alt="">
    <p class="parrafo_indice">Nuestra plataforma se fundamenta en un sistema de votación que brinda la posibilidad de participar en encuestas de manera anónima o mediante la utilización de un usuario previamente registrado.<br>
            En el marco de nuestro programa, se nos concede la opción de iniciar sesión con un usuario existente o de crear uno en caso de no poseerlo aún. <br>Una vez que hemos iniciado sesión, se nos permite acceder a nuestro panel de control, también denominado <i>Dashboard</i>.<br>
            El panel de control, o <i>Dashboard</i>, ofrece la capacidad de crear encuestas y realizar modificaciones en las mismas.<br>
              </p>
    </div>
    <?php
                    if (isset($_SESSION['redirigido']) && $_SESSION['redirigido']) {
                                        echo "<p class='mensaje_informativo'>Para proceder con la validación de su cuenta, le solicitamos amablemente que verifique el mensaje ubicado en la bandeja de entrada de su correo electrónico. <br>En caso de no localizarlo, le recomendamos revisar la carpeta de correo no deseado (Spam). Agradecemos su colaboración.</p>";

                                        unset($_SESSION['redirigido']);
                    }                   
    ?>
    <?php include("Utilidades/footer.php") ?>

</body>
</html>
