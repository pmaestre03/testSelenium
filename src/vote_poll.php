<!DOCTYPE html>
<html lang="es">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Votar Encuesta</title>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <link rel="stylesheet" href="./Utilidades/styles.css?no-cache=<?php echo time(); ?>">
        <script src="Utilidades/scripts.js"></script>
</head>
<?php require('Utilidades/scripts2.php')?>
<?php include("Utilidades/header.php") ?>
<?php include("Utilidades/conexion.php") ?>
<body class="vote_poll">
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['opcion_votada'])) {
        $opcion_votada = $_POST['opcion_votada'];

        // Token de la URL
        $token = $_GET['token'];

        try {
            $hostname = "localhost";
            $dbname = "votaciones";
            $username = "userProyecto";
            $pw = "votacionesAXP24";
            $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $pdo->beginTransaction();

            // Actualizar el estado del token en la tabla invitacion
            $consulta_update_token = 'UPDATE invitacion SET token_activo = 0 WHERE token = :token';
            $stmt_update_token = $pdo->prepare($consulta_update_token);
            $stmt_update_token->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt_update_token->execute();

            // Obtener el ID de la encuesta y la opción seleccionada
            $consulta_encuesta = 'SELECT encuestas.id_encuesta, opciones_encuestas.id_opciones_encuesta
                                  FROM invitacion
                                  INNER JOIN encuestas ON invitacion.id_encuesta = encuestas.id_encuesta
                                  INNER JOIN opciones_encuestas ON encuestas.id_encuesta = opciones_encuestas.id_encuesta
                                  WHERE invitacion.token = :token AND opciones_encuestas.nombre_opciones = :opcion_votada';
            $stmt_encuesta = $pdo->prepare($consulta_encuesta);
            $stmt_encuesta->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt_encuesta->bindParam(':opcion_votada', $opcion_votada, PDO::PARAM_STR);
            $stmt_encuesta->execute();
            $row = $stmt_encuesta->fetch(PDO::FETCH_ASSOC);
            $id_encuesta = $row['id_encuesta'];
            $id_opciones_encuesta = $row['id_opciones_encuesta'];

            // Insertar el voto en la tabla votaciones_por_usuario
            $consulta_insert_voto = 'INSERT INTO votaciones_por_usuario (id_encuesta, registro, token) VALUES (:id_encuesta, true, :token)';
            $stmt_insert_voto = $pdo->prepare($consulta_insert_voto);
            $stmt_insert_voto->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_INT);
            $stmt_insert_voto->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt_insert_voto->execute();

            $consulta_insert_voto_encriptado = 'INSERT INTO votos_encriptados (token_encriptado, opciones_encuesta_id ) VALUES (:token_encriptado, :id_opciones_encuesta)';
            $stmt_insert_voto_encriptado = $pdo->prepare($consulta_insert_voto_encriptado);
            $stmt_insert_voto_encriptado->bindParam(':id_opciones_encuesta', $id_opciones_encuesta, PDO::PARAM_INT);
            $token_encriptado = hash('sha512', $token);
            $stmt_insert_voto_encriptado->bindParam(':token_encriptado', $token_encriptado, PDO::PARAM_STR);
            $stmt_insert_voto_encriptado->execute();
            $pdo->commit(); 
            registrarEvento("Voto registrado");
            echo "¡Tu voto ha sido registrado con éxito!";
            header("Location: index.php");
        } catch (PDOException $e) {
            // Rollback de la transacción en caso de error
            $pdo->rollback();
            echo "Error al procesar tu voto: " . $e->getMessage();
        }
    } else {
        echo "Error: No se ha seleccionado ninguna opción de voto.";
    }
}
?>
<?php
if(isset($_GET['token'])) {
    $token = $_GET['token'];

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
    // Consultar la base de datos para verificar la validez del token
    $consulta_token = 'SELECT * FROM invitacion WHERE token = :token AND token_activo = 1';
    $stmt_token = $pdo->prepare($consulta_token);
    $stmt_token->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt_token->execute();
    
    // Verificar si se encontro un resultado válido
    if($stmt_token->rowCount() > 0) {

         // Obtener la información de la encuesta
         $row_token = $stmt_token->fetch(PDO::FETCH_ASSOC);
         $id_encuesta = $row_token['id_encuesta'];
 
         // Verificar si la encuesta está bloqueada
         $consulta_encuesta_bloqueada = 'SELECT bloqueada FROM encuestas WHERE id_encuesta = :id_encuesta';
         $stmt_encuesta_bloqueada = $pdo->prepare($consulta_encuesta_bloqueada);
         $stmt_encuesta_bloqueada->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_INT);
         $stmt_encuesta_bloqueada->execute();
         $row_encuesta_bloqueada = $stmt_encuesta_bloqueada->fetch(PDO::FETCH_ASSOC);
         $encuesta_bloqueada = $row_encuesta_bloqueada['bloqueada'];
 
         // Verificar si la encuesta está bloqueada
         if ($encuesta_bloqueada == 1) {
            echo "<div class='user-info'>Encuesta bloqueada por el autor</div>";
         }
         else {
            // Consulta SQL para la información de la encuesta y lass opciones
            $consulta_encuesta = 'SELECT encuestas.titulo_encuesta, encuestas.imagen_titulo, opciones_encuestas.nombre_opciones, opciones_encuestas.imagen_opciones
                                FROM invitacion
                                INNER JOIN encuestas ON invitacion.id_encuesta = encuestas.id_encuesta
                                INNER JOIN opciones_encuestas ON encuestas.id_encuesta = opciones_encuestas.id_encuesta
                                WHERE invitacion.token = :token';
            $stmt_encuesta = $pdo->prepare($consulta_encuesta);
            $stmt_encuesta->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt_encuesta->execute();

            // Verificar si se encontró un resultado válido
            if($stmt_encuesta->rowCount() > 0) {
            
                $rows = $stmt_encuesta->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($rows)) {
                    $row = $rows[0];
                
                    $titulo_encuesta = $row['titulo_encuesta'];
                    $imagen_titulo = $row['imagen_titulo'];
                // Mostrar el título de la encuesta y su imageb
                    echo '<div class="user-info">' . $titulo_encuesta . '</div>';
                    if (isset($imagen_titulo)) {
                        echo '<div class="box_img_vote">';
                        echo '<img src="' . $imagen_titulo . '" alt="Imagen de la encuesta">';
                        echo '</div>';
                    }
                }

                // Mostrar las opciones de la encuesta junto con sus imágenes
                echo '<form method="post" action="" id="formVote">';
                foreach ($rows as $row) {
                    $nombre_opcion = $row['nombre_opciones'];
                    $imagen_opcion = $row['imagen_opciones'];
                    echo '<table>';
                    echo '<tr><th><input type="radio" name="opcion_votada" value="' . $nombre_opcion . '">';
                    echo '<label id="labelVote" for="' . $nombre_opcion . '">' . $nombre_opcion . '</label></th></tr>';
                    if (isset($imagen_opcion)) {
                        echo '<div class="box_img_vote2">';
                        echo '<tr><th><img src="' . $imagen_opcion . '" alt="Imagen de la opción"></th></tr>';
                        echo '</div>';
                    }
                    echo '</table>';
                }
                echo '<button type="submit" id="buttonVote">Votar</button>';
                echo '</form>';
            } else {
                // No se encontró ninguna encuesta asociada al token
                echo 'No hay ninguna encuesta asociada a este token.';
            }
        }
    }else {
        // El token no es valido
        echo "<div class='user-info'>Token no válido</div>";
    }
} else {
    // No se proporciono ningun token en la URL
    header("Location: ../errores/error403.php");
    exit;
}
?>
<?php include("Utilidades/footer.php") ?>
<div id="notification-container"></div>
</body>
</html>
