<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="Utilidades/styles.css?no-cache=<?php echo time(); ?>">
</head>
<body class="dashboard">
<?php include("Utilidades/header.php") ?>
    <?php
        //echo $_SESSION['token'];
        if (isset($_SESSION['usuario'])) {
            
            echo "<div class='user-info'>";
            echo "Panel de Administración";
            echo "</div>";

            echo "<div class='dashboard-container'>";
                echo "<div class='dashboard-box' id='createPolls'>";
                    echo "<h2>Crear Encuestas</h2>";
                echo "</div>";

                // redireccion del div crear encuestas
                echo '<script>';
                    echo '$(document).ready(function() {';
                    echo '    $("#createPolls").on("click", function() {';
                    echo '        window.location.href = "create_poll.php";';
                    echo '    });';
                    echo '});';
                echo '</script>';

                /* echo "<div class='dashboard-box' id='invitePolls'>";
                    echo "<h2>Invitaciones</h2>";
                echo "</div>";

                echo '<script>';
                    echo '$(document).ready(function() {';
                    echo '    $("#invitePolls").on("click", function() {';
                    echo '        window.location.href = "invite_poll.php";';
                    echo '    });';
                    echo '});';
                echo '</script>'; */
                // echo "<div class='dashboard-box'>";
                //     echo "<h2>Editar Encuestas</h2>";
                // echo "</div>";

                echo "<div class='dashboard-box' id='listPolls'>";
                    echo "<h2>Listar Encuestas</h2>";
                echo "</div>";

                // redireccion del div crear encuestas
                echo '<script>';
                    echo '$(document).ready(function() {';
                    echo '    $("#listPolls").on("click", function() {';
                    echo '        window.location.href = "list_polls.php";';
                    echo '    });';
                    echo '});';
                echo '</script>';

                /* echo "<div class='dashboard-box' id='votePolls'>";
                    echo "<h2>Votar Encuestas</h2>";
                echo "</div>";

                echo '<script>';
                    echo '$(document).ready(function() {';
                    echo '    $("#votePolls").on("click", function() {';
                    echo '        window.location.href = "vote_poll.php";';
                    echo '    });';
                    echo '});';
                echo '</script>'; */

                echo "<div class='dashboard-box' id='listVote'>";
                    echo "<h2>Listar Votos realizados/pendientes</h2>";
                echo "</div>";

                echo '<script>';
                    echo '$(document).ready(function() {';
                    echo '    $("#listVote").on("click", function() {';
                    echo '        window.location.href = "list_vote.php";';
                    echo '    });';
                    echo '});';
                echo '</script>';

                echo "<div class='dashboard-box' id='votePolls'>";
                    echo "<h2>Cambiar contraseña</h2>";
                echo "</div>";

                echo '<script>';
                    echo '$(document).ready(function() {';
                    echo '    $("#votePolls").on("click", function() {';
                    echo '        window.location.href = "change_password.php";';
                    echo '    });';
                    echo '});';
                echo '</script>';


            echo "</div>";
            
        } else {
            //header("HTTP/1.1 403 Forbidden");
            header("Location: ../errores/error403.php");
            http_response(403);
            exit;
}
        ?>
<?php include("Utilidades/footer.php") ?>
</body>
</html>
