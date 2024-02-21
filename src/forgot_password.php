<?php session_start() ?>
<link rel="stylesheet" href="Utilidades/styles.css?no-cache=<?php echo time(); ?>">
<?php require('Utilidades/scripts2.php') ?>
<script src="./Utilidades/scripts.js"></script>
<?php include("Utilidades/header.php") ?>
<body>
<div id="notification-container"></div>
    <div class='user-info'>Cambio de contraseña</div>
    <div class='login-container'>
        <form method='post' onsubmit="return validarFormulario()">
        <h1>Una vez cambiada la reestablecida la contraseña no podras ver tus anteriores votos</h1><br>
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

if (!isset($_GET['token'])) {
    header("HTTP/1.1 403 Forbidden");
    http_response(403);
    exit;
}

$token = $_GET['token'];
$consulta_reset_password = 'SELECT email_reset FROM reset_password WHERE token_password = :token';
$stmt_reset = $pdo->prepare($consulta_reset_password);
$stmt_reset->bindParam(':token', $token, PDO::PARAM_STR);
$stmt_reset->execute();
$result = $stmt_reset->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    header("Location: ../errores/error403.php");
    http_response(403);
    exit;
}

if (isset($_GET['token'])) {
         if (isset($_POST["new_password"])) {
                  $token = $_GET["token"];
                  $consulta_reset_password = 'SELECT email_reset from reset_password  where token_password = :token';
                  $stmt_reset = $pdo->prepare($consulta_reset_password);
                  $stmt_reset->bindParam(':token', $token, PDO::PARAM_STR);
                  $stmt_reset->execute();
                  $result = $stmt_reset->fetch(PDO::FETCH_ASSOC);
                  if ($result) {
                           $email = $result['email_reset'];
                  } else {
                           echo "Token not found or no matching email_reset";
                  }

                  $hashed_new_password = hash('sha512', $_POST['new_password']);
                  $update_password_query = 'UPDATE users SET contrasea_cifrada = :hashed_password WHERE email = :email';
                  $stmt_update_password = $pdo->prepare($update_password_query);
                  $stmt_update_password->bindParam(':hashed_password', $hashed_new_password, PDO::PARAM_STR);
                  $stmt_update_password->bindParam(':email', $email, PDO::PARAM_STR);
                  $stmt_update_password->execute();

                  $fetch_invitation_tokens_query = 'SELECT token FROM invitacion WHERE email = :email';
                  $stmt_fetch_tokens = $pdo->prepare($fetch_invitation_tokens_query);
                  $stmt_fetch_tokens->bindParam(':email', $email, PDO::PARAM_STR);
                  $stmt_fetch_tokens->execute();
                  $invitation_tokens = $stmt_fetch_tokens->fetchAll(PDO::FETCH_COLUMN);

                  foreach ($invitation_tokens as $old_token) {

                           $new_token = uniqid();

                           $update_votaciones_token_query = 'UPDATE votaciones_por_usuario SET token = :new_token WHERE token = :old_token';
                           $stmt_update_votaciones_token = $pdo->prepare($update_votaciones_token_query);
                           $stmt_update_votaciones_token->bindParam(':new_token', $new_token, PDO::PARAM_STR);
                           $stmt_update_votaciones_token->bindParam(':old_token', $old_token, PDO::PARAM_STR);
                           $stmt_update_votaciones_token->execute();

                           $hashed_new_token = hash('sha512', $new_token);
                           $hashed_old_token = hash('sha512', $old_token);
                           $update_hashed_token_query = 'UPDATE votos_encriptados SET token_encriptado = :hashed_new_token WHERE token_encriptado = :hashed_old_token';
                           $stmt_update_hashed_token = $pdo->prepare($update_hashed_token_query);
                           $stmt_update_hashed_token->bindParam(':hashed_new_token', $hashed_new_token, PDO::PARAM_STR);
                           $stmt_update_hashed_token->bindParam(':hashed_old_token', $hashed_old_token, PDO::PARAM_STR);
                           $stmt_update_hashed_token->execute();
                  }
                  $delete_reset_row_query = 'DELETE FROM reset_password WHERE token_password = :token AND email_reset = :email';
                  $stmt_delete_reset_row = $pdo->prepare($delete_reset_row_query);
                  $stmt_delete_reset_row->bindParam(':token', $token, PDO::PARAM_STR);
                  $stmt_delete_reset_row->bindParam(':email', $email, PDO::PARAM_STR);
                  $stmt_delete_reset_row->execute();
                  echo "<script>showNotification('Contraseña cambiada')</script>";
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
