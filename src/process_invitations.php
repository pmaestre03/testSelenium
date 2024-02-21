<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require "vendor/autoload.php";
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

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
    
$query_5 = "SELECT user_email FROM email_invitacion LIMIT 5";
$stmt_email = $pdo->prepare($query_5);
$stmt_email->execute();
$email_array = $stmt_email->fetchAll(PDO::FETCH_ASSOC);

foreach ($email_array as $email_row) {
    $email = trim($email_row['user_email']);

    $query_token = "SELECT token FROM email_invitacion WHERE user_email = :email";
    $stmt_token = $pdo->prepare($query_token);
    $stmt_token->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt_token->execute();
    $token_row = $stmt_token->fetch(PDO::FETCH_ASSOC);
    $token = $token_row['token'];
    $voting_link = "https://aws22.ieti.site/vote_poll.php?token=$token";

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->SMTPDebug  = 0;  
    $mail->SMTPAuth   = TRUE;
    $mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Host       = "smtp.gmail.com";
    $mail->Username   = "mgonzalezramirez.cf@iesesteveterradas.cat";
    $mail->Password   = "PlataNoEs18";
    $mail->SetFrom("mgonzalezramirez.cf@iesesteveterradas.cat", "VotaPAX");

    $mail->AddAddress($email);
    $subjectmail = "Invitado a VotaPAX";
    $mail->Subject = $subjectmail;
    $bodymail = "¡Hola! Has sido invitado a votar en nuestra encuesta. Para votar, haz clic en el siguiente enlace:'<a href='$voting_link'>Vota</a>'";
    $mail->MsgHTML($bodymail);

    if (!$mail->Send()) {
        echo "Error al enviar correo a: $email<br>";
    } else {
        $query_delete = "DELETE FROM email_invitacion WHERE user_email = :email";
        $stmt_delete = $pdo->prepare($query_delete);
        $stmt_delete->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt_delete->execute();
    }
}
header("Location: index.php");
exit;

?>
