<!-- vote.php -->
<?php
include("Utilidades/header.php");

// Verificar la sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../errores/error403.php");
    exit;
}

// Obtener el ID de la encuesta y el correo del usuario desde la URL
if (isset($_GET['id_encuesta']) && isset($_GET['correo'])) {
    $id_encuesta = intval($_GET['id_encuesta']);
    $correo_usuario = $_GET['correo'];

    try {
        $hostname = "localhost";
        $dbname = "votaciones";
        $username = "userProyecto";
        $pw = "votacionesAXP24";
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);
    } catch (PDOException $e) {
        echo "Failed to get DB handle: " . $e->getMessage() . "\n";
        exit;
    }

    // Verificar si el usuario ya votó
    $stmt_verificar_voto = $pdo->prepare("SELECT * FROM votos WHERE id_encuesta = :id_encuesta AND correo_usuario = :correo_usuario");
    $stmt_verificar_voto->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_INT);
    $stmt_verificar_voto->bindParam(':correo_usuario', $correo_usuario, PDO::PARAM_STR);
    $stmt_verificar_voto->execute();

    if ($stmt_verificar_voto->rowCount() > 0) {
        // El usuario ya votó, puedes mostrar un mensaje o redirigir a otra página
        echo "Ya has votado en esta encuesta.";
        exit;
    }

    // Obtener la información de la encuesta
    $stmt_encuesta = $pdo->prepare("SELECT * FROM encuestas WHERE id_encuesta = :id_encuesta");
    $stmt_encuesta->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_INT);
    $stmt_encuesta->execute();

    $datos_encuesta = $stmt_encuesta->fetch(PDO::FETCH_ASSOC);

    if ($datos_encuesta) {
        // Mostrar la pregunta de la encuesta y las opciones para votar
?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Votar en la Encuesta</title>
            <link rel="stylesheet" href="./Utilidades/styles.css?no-cache=<?php echo time(); ?>">
        </head>

        <body class="vote">

            <div id="notification-container"></div>

            <form method="post" action="process_vote.php">
                <input type="hidden" name="id_encuesta" value="<?php echo $id_encuesta; ?>">
                <input type="hidden" name="correo_usuario" value="<?php echo $correo_usuario; ?>">

                <h2><?php echo $datos_encuesta['titulo_encuesta']; ?></h2>

                <p><?php echo $datos_encuesta['pregunta_encuesta']; ?></p>

                <?php
                // Obtener opciones de la encuesta
                $stmt_opciones = $pdo->prepare("SELECT * FROM opciones_encuestas WHERE id_encuesta = :id_encuesta");
                $stmt_opciones->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_INT);
                $stmt_opciones->execute();

                while ($opcion = $stmt_opciones->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <input type="radio" name="opcion_voto" value="<?php echo $opcion['id_opcion']; ?>" required>
                    <?php echo $opcion['nombre_opcion']; ?><br>
                <?php
                }
                ?>

                <button type="submit" class="button button-login">Votar</button>
            </form>

            <?php include("Utilidades/footer.php") ?>
        </body>

        </html>
<?php
    } else {
        echo "Error: No se encontró la encuesta con el ID proporcionado.";
    }

    unset($pdo);
    unset($stmt_encuesta);
} else {
    echo "Error: No se proporcionaron los parámetros necesarios para votar.";
}
?>
