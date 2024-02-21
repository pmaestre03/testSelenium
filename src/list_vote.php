<!DOCTYPE html>
<html lang="es">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Listar Votos</title>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <link rel="stylesheet" href="./Utilidades/styles.css?no-cache=<?php echo time(); ?>">
        <script src="Utilidades/scripts.js"></script>
</head>
<?php require('Utilidades/scripts2.php')?>
<?php include("Utilidades/header.php") ?>
<?php include("Utilidades/conexion.php") ?>
<body class="list_vote">
<div id="notification-container"></div>
<?php
    if (isset($_SESSION['id_user'])) {
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

        // Obtener el id_user del usuario actual desde la sesión
        $id_user = $_SESSION['id_user'];

        // Consulta para obtener las encuestas votadas y no votadas por el usuario
        $consulta_encuestas = "SELECT invitacion.id_encuesta, encuestas.titulo_encuesta, invitacion.token_activo
                               FROM invitacion
                               INNER JOIN encuestas ON invitacion.id_encuesta = encuestas.id_encuesta
                               WHERE invitacion.id_user = :id_user";

        $stmt_encuestas = $pdo->prepare($consulta_encuestas);
        $stmt_encuestas->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt_encuestas->execute();

        if ($stmt_encuestas->rowCount() > 0) {
            echo "<div class='user-info'>Encuestas realizadas y pendientes</div>";
            echo "<div class='center'>";
            echo "<table>";
            echo "<tr><th>Título de la Encuesta</th><th>Estado</th><th></th></tr>";

            // Mostrar cada encuesta junto con su estado
            while ($row = $stmt_encuestas->fetch(PDO::FETCH_ASSOC)) {
                $id_encuesta = $row['id_encuesta'];
                $titulo_encuesta = $row['titulo_encuesta'];
                $token_activo = $row['token_activo'];
                echo "<tr>";
                echo "<td>$titulo_encuesta</td>";
                echo "<td>";
                if ($token_activo == 1) {
                    echo "Pendiente";
                } else {
                    echo "Realizada";
                }
                echo "</td>";
                echo "<td><button onclick=\"window.location.href='confirm_password.php?id_encuesta=$id_encuesta'\">Ver Voto</button></td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        } else {
            echo "<div class='user-info'>No hay encuestas disponibles</div>";
            echo "<script>showNotification('No hay encuestas ni realizadas ni pendientes','red')</script>";
        }
    } else {
        header("Location: ../errores/error403.php");
        http_response(403);
        exit;
    }
?>
<?php include("Utilidades/footer.php") ?>
</body>
</html>