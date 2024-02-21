<?php include("Utilidades/conexion.php") ?>
<?php include("Utilidades/header.php") ?>
<link rel="stylesheet" href="./Utilidades/styles.css?no-cache=<?php echo time(); ?>">
<script src="Utilidades/scripts.js"></script>
<?php require('Utilidades/scripts2.php')?>
<body>
<div id="notification-container"></div>
<div class="login-container" id="loginContainer">
<form method="post">
    <label for="confirm_password">Confirmar Contraseña</label>
    <input type="password" name="confirm_password" id="confirm_password">
    <button type="submit" class="button button-login">Verificar contraseña</button>
</form?>
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
$_SESSION['id_encuesta'] = $_GET['id_encuesta'];
//$_SESSION['email']
if (isset($_SESSION['email'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") { 
        $password = $_POST['confirm_password'];
        $email = $_SESSION['email'];
        
        $password_cifrada = hash('sha512', $_POST["confirm_password"]);

        $querystr = "SELECT * FROM users WHERE email=:email AND contrasea_cifrada=:password_cifrada";
        $query = $pdo->prepare($querystr);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':password_cifrada', $password_cifrada, PDO::PARAM_STR);

        $query->execute();

        $filas = $query->rowCount();

        if ($filas > 0) {
            registrarEvento("Contraseña confirmada por el usuario: ".$_SESSION['email']);
            $_SESSION['password_confirmada'] = true;
            header("Location: view_vote.php");
        } else {
            
            echo "<script>showNotification('Usuario o contraseña incorrecto','red')</script>"; 
        }
    }
}   else {
    header("Location: ../errores/error403.php");
    http_response(403);
    exit;
}

?>
</div>
<?php include("Utilidades/footer.php") ?>
</body>
