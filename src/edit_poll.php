<?php include("Utilidades/header.php") ?>
<?php require('Utilidades/scripts2.php')?>
<link rel="stylesheet" href="./Utilidades/styles.css?no-cache=<?php echo time(); ?>">
<div id="notification-container"></div>
<div class="login-container">
                <form method="post" action="process_edit_poll.php">
<?php 
session_start();
try {
    $pdo = new PDO('mysql:host=localhost;dbname=votaciones', 'userProyecto', 'votacionesAXP24');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}
$_SESSION["id_encuesta"] = $_GET['id_encuesta'];
if (isset($_SESSION['id_encuesta'])) {
$id_encuesta = $_SESSION["id_encuesta"];
$find_poll = "SELECT titulo_encuesta,estado_enunciado,estado_respuestas,bloqueada FROM encuestas where id_encuesta=:id_encuesta";
$query = $pdo->prepare($find_poll);
$query->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_STR);
$query->execute();
$filas = $query->rowCount();

if ($filas > 0) {
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $titulo_encuesta = $row['titulo_encuesta'];
    echo "<h1>".$titulo_encuesta."</h1>";
}
} else {
    header("Location: ../errores/error403.php");
    http_response(403);
    exit;
}
?>
<label for="bloqueo">Disponibilidad:</label>
        <select name="bloqueo" id="bloqueo">
            <option value="bloqueada">Bloqueada</option>
            <option value="accesible">Accesible</option>
        </select>
<br>
<label for="visibilidad_encuesta">Visibilidad de la Encuesta:</label>
        <select name="visibilidad_encuesta" id="visibilidad_encuesta" onchange="actualizarOpcionesRespuestas()">
            <option value="oculta">Oculta</option>
            <option value="privada">Privada</option>
            <option value="publica">Pública</option>
        </select>

        <br>

        <label for="visibilidad_respuestas">Visibilidad de las Respuestas:</label>
        <select name="visibilidad_respuestas" id="visibilidad_respuestas">
            <option value="oculta">Oculta</option>
        </select>

        <br>

        <input type="submit" value="Enviar">
    </form>
</div>
<?php

?>
<?php include("Utilidades/footer.php") ?>
<script src="Utilidades/scripts.js"></script>