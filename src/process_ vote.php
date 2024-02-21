<!-- process_vote.php -->
<?php

// Verificar si se recibieron los datos del formulario de voto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_encuesta']) && isset($_POST['correo_usuario']) && isset($_POST['opcion_voto'])) {
    $id_encuesta = intval($_POST['id_encuesta']);
    $correo_usuario = $_POST['correo_usuario'];
    $opcion_voto = intval($_POST['opcion_voto']);

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

    // Verificar si el usuario ya votó (por si acaso)
    $stmt_verificar_voto = $pdo->prepare("SELECT * FROM votos WHERE id_encuesta = :id_encuesta AND correo_usuario = :correo_usuario");
    $stmt_verificar_voto->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_INT);
    $stmt_verificar_voto->bindParam(':correo_usuario', $correo_usuario, PDO::PARAM_STR);
    $stmt_verificar_voto->execute();

    if ($stmt_verificar_voto->rowCount() > 0) {
        // El usuario ya votó, puedes mostrar un mensaje o redirigir a otra página
        echo "Ya has votado en esta encuesta.";
        exit;
    }

    // Almacenar el voto en la base de datos
    $stmt_insertar_voto = $pdo->prepare("INSERT INTO votos (id_encuesta, correo_usuario, id_opcion) VALUES (:id_encuesta, :correo_usuario, :id_opcion)");
    $stmt_insertar_voto->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_INT);
    $stmt_insertar_voto->bindParam(':correo_usuario', $correo_usuario, PDO::PARAM_STR);
    $stmt_insertar_voto->bindParam(':id_opcion', $opcion_voto, PDO::PARAM_INT);
    $stmt_insertar_voto->execute();

    // Redirigir a una página de confirmación o detalles de la encuesta
    header("Location: vote_confirmation.php?id=$id_encuesta");
    exit;
} else {
    // Si los datos no se recibieron correctamente, redirigir a una página de error
    header("Location: vote_error.php");
    exit;
}
?>
