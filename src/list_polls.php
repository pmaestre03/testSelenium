<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Encuestas</title>
    <link rel="stylesheet" href="Utilidades/styles.css?no-cache=<?php echo time(); ?>">
    <script src="Utilidades/scripts.js"></script>
</head>
<body class="index">
    <?php include("Utilidades/header.php") ?>
    <div id="notification-container"></div>
    <?php
    if ($_SESSION["encuesta_editada"]) {
             echo "<script>showNotification('Encuesta editada correctamente')</script>";
             $_SESSION["encuesta_editada"] = false;
    }

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

    if (isset($_SESSION['usuario'])) {
             $user = $_SESSION["email"];
             $listar = 'SELECT id_encuesta, titulo_encuesta, fech_inicio, fecha_fin,estado_enunciado,estado_respuestas,bloqueada FROM encuestas WHERE creador = (SELECT id_user FROM users WHERE email=:email);';

             $stmt = $pdo->prepare($listar);
             $stmt->bindParam(':email', $user, PDO::PARAM_STR);
             $stmt->execute();

             $encuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);

             if ($encuestas) {

                      echo "<div class='user-info'>Encuestas creadas</div>";
                      echo "<div class='center'>";
                      echo "<table border='1'>";
                      echo "<tr><th>Título de la Encuesta</th><th>Fecha Inicio</th><th>Fecha Fin</th><th>Estado</th><th>Disponibilidad</th><th>Visibilidad Enunciado</th><th>Visibilidad Respuestas</th><th></th><th></th><th></th>";
                      foreach ($encuestas as $encuesta) {
                               echo "<tr>";
                               echo "<td>{$encuesta['titulo_encuesta']}</td>";
                               echo "<td>{$encuesta['fech_inicio']}</td>";
                               echo "<td>{$encuesta['fecha_fin']}</td>";
                               $fechaActual = strtotime(date("Y-m-d"));
                               $inicioEncuesta = strtotime($encuesta['fech_inicio']);
                               $finEncuesta = strtotime($encuesta['fecha_fin']);

                               if ($fechaActual >= $inicioEncuesta && $fechaActual <= $finEncuesta) {
                                        echo "<td class='publica'>Activa</td>";
                                        $inactiva = false;
                               }
                               if ($fechaActual < $inicioEncuesta) {
                                        echo "<td class='oculta'>No Activa</td>";
                                        $inactiva = true;
                               }
                               if ($fechaActual > $finEncuesta) {
                                        echo "<td class='finalizada'>Finalizada</td>";
                                        $inactiva = true;
                               }

                               $id_encuesta = $encuesta['id_encuesta'];

                               if ($encuesta['bloqueada'] == 1 || ($fechaActual > $finEncuesta) || ($fechaActual < $inicioEncuesta)) {
                                        echo "<td class='finalizada'>Bloqueada</td>";
                                        $query = $pdo->prepare("UPDATE encuestas SET bloqueada = 1  WHERE id_encuesta=:id_encuesta;");
                                        $query->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_STR);
                                        $query->execute();
                               } elseif ($encuesta['bloqueada'] == 0) {
                                        echo "<td class='publica'>Accesible</td>";
                               }

                               if ($encuesta['estado_enunciado'] == 'oculta') {
                                        echo "<td class='oculta'>Oculta</td>";
                               } elseif ($encuesta['estado_enunciado'] == 'privada') {
                                        echo "<td class='privada'>Privada</td>";
                               } elseif ($encuesta['estado_enunciado'] == 'publica') {
                                        echo "<td class='publica'>Publica</td>";
                               }

                               if ($encuesta['estado_respuestas'] == 'oculta') {
                                        echo "<td class='oculta'>Oculta</td>";
                               } elseif ($encuesta['estado_respuestas'] == 'privada') {
                                        echo "<td class='privada'>Privada</td>";
                               } elseif ($encuesta['estado_respuestas'] == 'publica') {
                                        echo "<td class='publica'>Publica</td>";
                               }

                               echo "<td><button onclick=\"window.location.href='graphics.php?id=$id_encuesta'\">Detalles Encuesta</button></td>";
                               if ($encuesta['bloqueada'] == 1 || $inactiva == true) {
                                        echo "<td><button disabled>Enviar Invitaciones</button></td>";
                               } elseif ($encuesta['bloqueada'] == 0 || $inactiva == false) {
                                        echo "<td><button onclick=\"window.location.href='invite.php?id=$id_encuesta'\">Enviar Invitaciones</button></td>";
                               }
                               echo "<td><button onclick=\"window.location.href='edit_poll.php?id_encuesta=$id_encuesta'\">Editar Encuesta</button></td>";
                               echo "</tr>";
                      }
                      echo "</table>";
                      echo "</div>";
             } else {
                    echo "<div class='user-info'>No hay encuestas creadas</div>";
                    echo "<script>showNotification('No hay encuestas creadas','red')</script>";
             }

             unset($pdo);
             unset($stmt);

    } else {
             header("Location: ../errores/error403.php");
             http_response(403);
             exit;
    }

    ?>

    <?php include("Utilidades/footer.php") ?>
</body>
</html>
