<?php require('Utilidades/scripts2.php') ?>
<?php
session_start();
$id_encuesta = $_SESSION["id_encuesta"];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recuperar los valores del formulario
    if ($_POST["bloqueo"] == 'bloqueada') {
                    $bloqueo = 1;
    } elseif ($_POST["bloqueo"] == 'accesible') {
                    $bloqueo = 0;
    }
    registrarEvento("Encuesta editada por el usuario: ".$_SESSION['email']);
    $visibilidadEncuesta = $_POST["visibilidad_encuesta"];
    $visibilidadRespuestas = $_POST["visibilidad_respuestas"];

    // Validar y procesar la consulta SQL
    if ($visibilidadRespuestas === "oculta" || $visibilidadRespuestas === "privada" || $visibilidadRespuestas === "publica") {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=votaciones', 'userProyecto', 'votacionesAXP24');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Realizar la consulta SQL aquí
            // UPDATE encuestas SET estado_publicacion = :visibilidadEncuesta, estado_visibilidad = :visibilidadRespuestas  WHERE id_encuesta=:id_encuesta;
            $query = $pdo->prepare("UPDATE encuestas SET estado_enunciado = :visibilidadEncuesta, estado_respuestas = :visibilidadRespuestas,bloqueada = :bloqueo  WHERE id_encuesta=:id_encuesta;");
            $query->bindParam(':visibilidadEncuesta', $visibilidadEncuesta, PDO::PARAM_STR);
            $query->bindParam(':visibilidadRespuestas', $visibilidadRespuestas, PDO::PARAM_STR);
            $query->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_STR);
            $query->bindParam(':bloqueo', $bloqueo, PDO::PARAM_STR);
            $query->execute();

            $_SESSION["encuesta_editada"] = true;
            header("Location: list_polls.php");
            exit();

        } catch (PDOException $e) {
            echo "Error en la conexión a la base de datos: " . $e->getMessage();
        }
    } else {
        echo "Error: Opción no válida para la visibilidad de las respuestas.";
    }
}
?>
