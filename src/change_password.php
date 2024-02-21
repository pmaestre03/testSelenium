<?php session_start() ?>
<link rel="stylesheet" href="Utilidades/styles.css?no-cache=<?php echo time(); ?>">
<?php require('Utilidades/scripts2.php') ?>
<script src="./Utilidades/scripts.js"></script>
<?php include("Utilidades/header.php") ?>
<body>
<div id="notification-container"></div>
    <div class='user-info'>Cambiar contraseña</div>
    <div class='login-container'>
        <form method='post' onsubmit="return validarFormulario()">
            <label for="old_password">Contraseña actual</label>
            <input type="password" name="old_password" id="old_password"><br>
            <br>
            <label for="new_password">Nueva contraseña</label>
            <input type="password" name="new_password" id="new_password">
            <br>
            <label for="repeat_new_password">Repita la nueva contraseña</label>
            <input type="password" name="repeat_new_password" id="repeat_new_password">

            <button type="submit" class="button button-login">Cambiar contraseña</button>
        </form>
    </div>
</body>
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
if (isset($_SESSION['email'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($_POST['new_password'] === $_POST['old_password']) {
            echo "<script>showNotification('La nueva contraseña no debe coincidir con la anterior','red')</script>";
            registrarEvento("Cambio de contraseña incorrecto por el usuario: $email");
        } else{
            $hashed_old_password = hash('sha512', $_POST['old_password']);
            $hashed_new_password = hash('sha512', $_POST['new_password']);
            $hashed_repeat_new_password = hash('sha512', $_POST['repeat_new_password']);
            $email = $_SESSION['email'];
            $querystr = "SELECT * FROM users WHERE email=:email AND contrasea_cifrada=:hashed_old_password";
            $query = $pdo->prepare($querystr);
            $query->bindParam(':email', $email , PDO::PARAM_STR);
            $query->bindParam(':hashed_old_password', $hashed_old_password, PDO::PARAM_STR);
            $query->execute();
    
            $filas = $query->rowCount();
    
            if ($filas > 0) {
                $updateQuery = "UPDATE users SET contrasea_cifrada=:hashed_new_password WHERE email=:email";
                $updateStatement = $pdo->prepare($updateQuery);
                $updateStatement->bindParam(':hashed_new_password', $hashed_new_password, PDO::PARAM_STR);
                $updateStatement->bindParam(':email', $email, PDO::PARAM_STR);
                $updateStatement->execute();
                echo "<script>showNotification('Cambio de contraseña correcto')</script>";
                registrarEvento("Cambio de contraseña realizada por el usuario: $email");
            } else {
                echo "<script>showNotification('Contraseña actual incorrecta','red')</script>";
                registrarEvento("Cambio de contraseña incorrecto por el usuario: $email");
            }
        }
    }   
} else {
    header("Location: ../errores/error403.php");
    http_response(403);
    exit;
}


?>
<script>
    function validarPassword(password) {
    var longitudMinima = 8;
    var tieneNumero = /\d/.test(password);
    var tieneMayuscula = /[A-Z]/.test(password);
    var tieneMinuscula = /[a-z]/.test(password);
    var tieneCaracterEspecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);

    return (
        password.length >= longitudMinima &&
        tieneNumero &&
        tieneMayuscula &&
        tieneMinuscula &&
        tieneCaracterEspecial
    );
}

function validarFormulario() {
    var oldPassword = document.getElementById("old_password").value;
    var newPassword = document.getElementById("new_password").value;
    var repeatNewPassword = document.getElementById("repeat_new_password").value;

    if (!validarPassword(newPassword)) {
        showNotification('La nueva contraseña no cumple con los requisitos de seguridad','red')
        return false;
    }
    if (newPassword !== repeatNewPassword) {
        showNotification('Las contraseñas nuevas no coinciden','red')
        return false;
    }
    
    return true;
}
</script>
<?php include("Utilidades/footer.php") ?>