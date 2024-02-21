<?php
session_start(); // Asegúrate de iniciar la sesión
if ($_SESSION['password_confirmada'] == false) {
    header('Location: list_vote.php');
    exit();
}
//var_dump($_SESSION);
?>

<?php include("Utilidades/conexion.php") ?>
<?php include("Utilidades/header.php") ?>
<link rel="stylesheet" href="./Utilidades/styles.css?no-cache=<?php echo time(); ?>">
<script src="Utilidades/scripts.js"></script>
<?php require('Utilidades/scripts2.php')?>
<body>
<div id="notification-container"></div>
<div class='user-info'>Tu voto</div>
<div class="login-container">
    
<?php
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
//$_SESSION['email']
    $querystr = "SELECT titulo_encuesta FROM encuestas where id_encuesta=:id_encuesta";
    $query = $pdo->prepare($querystr);
    $query->bindParam(':id_encuesta', $_SESSION['id_encuesta'], PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) > 0) {
        foreach ($result as $row) {
            echo '<h1>Encuesta: '.$row['titulo_encuesta'].'</h1>';
        }
    }

    $querystr = "SELECT token FROM invitacion WHERE email=:email AND id_encuesta=:id_encuesta";
    $query = $pdo->prepare($querystr);
    $query->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR);
    $query->bindParam(':id_encuesta', $_SESSION['id_encuesta'], PDO::PARAM_STR);
    $query->execute();

    $filas = $query->rowCount();
    while ($rowToken = $query->fetch(PDO::FETCH_ASSOC)) { 
        $token = $rowToken['token'];
        //echo $token.'<br>';
        $token_encriptado = hash('sha512', $token);
        //echo $token_encriptado;
        $queryEncriptado = "SELECT opciones_encuesta_id FROM votos_encriptados WHERE token_encriptado=:token_encriptado";
        $queryEncriptadoEx = $pdo->prepare($queryEncriptado);
        $queryEncriptadoEx->bindParam(':token_encriptado', $token_encriptado, PDO::PARAM_STR);
        $queryEncriptadoEx->execute();
        $result = $queryEncriptadoEx->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            foreach ($result as $row) {
                $opciones_encuesta_id = $row['opciones_encuesta_id'];
                //echo $opciones_encuesta_id;
                $querystr = "SELECT nombre_opciones,imagen_opciones FROM opciones_encuestas WHERE id_opciones_encuesta=:opciones_encuesta_id";
                $query = $pdo->prepare($querystr);
                $query->bindParam(':opciones_encuesta_id', $opciones_encuesta_id ,PDO::PARAM_STR);
                $query->execute();
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    foreach ($result as $row) {
                                        echo '<br><h2>Tu voto ha sido:<br> '.$row['nombre_opciones'].'</h2><br>';
                                        if ($row['imagen_opciones']!='') {
                                                            echo "<img src='./".$row['imagen_opciones']."' width=500px>";
                                        }
                    }
            }
        }
    }
    else {
        echo '<br><h2>No hay voto</h2><br>';
        echo "<script>showNotification('Todavía no has votado','red')</script>";
    }
}
?>
</div>
</body>
