<?php //session_start();  ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./Utilidades/styles.css?no-cache=<?php echo time(); ?>">
    <title>Login</title>
    <script src="./Utilidades/scripts.js"></script>
    <?php require('Utilidades/scripts2.php') ?>
</head>
<body class="login">
<?php include("Utilidades/header.php") ?>
<div class='user-info'>Login</div>
<!-- Formulario Login -->
<div class="login-container">
<form method="post">
<label for="username">Correo:</label>
<input type="text" id="username" name="username" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <a href="forgot_password_form.php">¿Contraseña olvidada?</a>
            <button type="submit" class="button button-login">Iniciar Sesión</button>
        </form>
    </div>
    <div id="notification-container"></div>
    <!-- BBDD -->
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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
             $usuario = $_POST["username"];
             $_SESSION['user_form'] = $usuario;

             $contrasenya = hash('sha512', $_POST["password"]);
             $_SESSION['password_form'] = $contrasenya;

             $querystr = "SELECT id_user,nombre,email,token_validado,condiciones_aceptadas FROM users WHERE email=:usuario AND contrasea_cifrada=:contrasenya";
             $query = $pdo->prepare($querystr);
             $query->bindParam(':usuario', $usuario, PDO::PARAM_STR);
             $query->bindParam(':contrasenya', $contrasenya, PDO::PARAM_STR);

             $query->execute();

             $filas = $query->rowCount();

             if ($filas > 0) {
                      $usuario = htmlspecialchars($_POST["username"]);
                      $row = $query->fetch(PDO::FETCH_ASSOC);
                      $_SESSION['email2'] = $row["email"];
                      $_SESSION['usuario2'] = $row['nombre'];
                      $_SESSION['id_user'] = $row["id_user"];
                      $condiciones_aceptadas = $row['condiciones_aceptadas'];
                      $_SESSION['condiciones_aceptadas'] = $condiciones_aceptadas;
                      if ($row['token_validado'] == 0) {
                               echo "<script>showNotification('Token no validado','red')</script>";
                      }
                      if ($condiciones_aceptadas == 0 && $row['token_validado'] == 1) {
                               header("Location: aceptar_condiciones.php");
                               exit();
                      }
                      if ($condiciones_aceptadas == 1 && $row['token_validado'] == 1) {
                               $_SESSION['email'] = $row["email"];
                               $_SESSION['usuario'] = $row['nombre'];

                               //echo "Usuario Correcto: Hola $nombre_usuario";
                               registrarEvento("Inicio de sesión por el usuario: $usuario");
                               //echo console.log($row['token_validado']);
                               header("Location: dashboard.php");
                               exit();
                      }
             } else {
                      $usuarioIntentado = htmlspecialchars($_POST["username"]);

                      echo "<script>showNotification('Usuario o contraseña incorrecto','red')</script>";

                      registrarEvento("Intento de inicio de sesión fallido por el usuario: $usuarioIntentado");
             }

             unset($pdo);
             unset($query);
    }
    ?>

<?php include("Utilidades/footer.php") ?>
</body>
</html>
