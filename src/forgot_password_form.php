<?php session_start() ?>
<link rel="stylesheet" href="Utilidades/styles.css?no-cache=<?php echo time(); ?>">
<?php require('Utilidades/scripts2.php') ?>
<script src="./Utilidades/scripts.js"></script>
<?php include("Utilidades/header.php") ?>
<body>
<div id="notification-container"></div>
    <div class='user-info'>Reinicio de contraseña</div>
    <div class='login-container'>
        <form method='post'>
            <label for="mail">Escribe tu correo electronico</label>
            <input type="email" name="mail" id="mail">
            <br>
            <button type="submit" class="button button-login">Reiniciar Contraseña</button>
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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "vendor/autoload.php";
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
if (isset($_POST["mail"])) {
         $email = trim($_POST["mail"]);
         $mail = new PHPMailer();
         $mail->IsSMTP();
         $mail->Mailer = "smtp";
         $mail->SMTPDebug = 0;
         $mail->SMTPAuth = TRUE;
         $mail->SMTPSecure = "tls";
         $mail->Port = 587;
         $mail->Host = "smtp.gmail.com";
         $mail->Username = "pmaestrefernandez.cf@iesesteveterradas.cat";
         $mail->Password = "Paumf26!!11";
         $mail->SetFrom("pmaestrefernandez.cf@iesesteveterradas.cat", "VotaPAX");

         // Generar un token aleatorio
         $token = uniqid();

         // Crear enlace de votación con el token
         $voting_link = "https://aws22.ieti.site/forgot_password.php?token=$token";

         // Agregar destinatario y contenido del mensaje
         $mail->AddAddress($email);
         $subjectmail = "Cambio de contraseña";
         $mail->Subject = $subjectmail;

         $bodymail = "Se ha solicitado un cambio de contraseña:' <a href='$voting_link'>Enlace</a>'";

         $consulta_reset_password = 'INSERT INTO reset_password (token_password,email_reset) VALUES (:token, :email)';
         $stmt_reset = $pdo->prepare($consulta_reset_password);
         $stmt_reset->bindParam(':token', $token, PDO::PARAM_STR);
         $stmt_reset->bindParam(':email',$email , PDO::PARAM_STR);
         $stmt_reset->execute();
         //$mail->Body = $bodymail;
         $mail->MsgHTML($bodymail);
         // Enviar correo electrónico
         if (!$mail->Send()) {
                  echo "Error al enviar correo a: $email<br>";
         } else {
                  echo "<script>showNotification('Email enviado correctamente')</script>";
         }
}
?>

<?php include("Utilidades/footer.php") ?>