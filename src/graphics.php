<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles</title>
    <link rel="stylesheet" href="Utilidades/styles.css?no-cache=<?php echo time(); ?>">
    <script src="Utilidades/scripts.js"></script>
</head>
<body class="graphics">
    <?php include("Utilidades/header.php") ?>
    <?php
    // Obtén el ID del usuario actual desde la sesión
    $id_usuario_actual = $_SESSION['id_user'];
    // Obtén el id_encuesta desde la URL
    if (isset($_GET['id'])) {
        $id_encuesta = intval($_GET['id']);  // Asegúrate de que sea un entero

        // Realiza la conexión a la base de datos
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

        // Consulta para obtener información específica de la encuesta
        $queryEncuesta = 'SELECT * FROM encuestas WHERE id_encuesta = :id_encuesta';
        $stmtEncuesta = $pdo->prepare($queryEncuesta);
        $stmtEncuesta->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_INT);
        $stmtEncuesta->execute();

        // Obtén los resultados de la encuesta
        $encuesta = $stmtEncuesta->fetch(PDO::FETCH_ASSOC);


        // Verifica si el usuario actual es el creador de la encuesta
        if ($encuesta && $encuesta['creador'] == $id_usuario_actual) {
            // Ahora puedes utilizar $encuesta para mostrar la información de la encuesta
            echo "<h1 id='pollName'>Detalles de la Encuesta, {$encuesta['titulo_encuesta']}</h1>";

            // Consulta para obtener votaciones de la encuesta
            // $queryVotaciones = 'SELECT opciones_encuestas.nombre_opciones, COUNT(votaciones_por_usuario.id_voto) as cantidad FROM opciones_encuestas LEFT JOIN votaciones_por_usuario ON opciones_encuestas.id_opciones_encuesta = votaciones_por_usuario.id_opciones_encuesta WHERE opciones_encuestas.id_encuesta = :id_encuesta GROUP BY opciones_encuestas.nombre_opciones;';
            $queryVotaciones = 'SELECT opciones_encuestas.nombre_opciones, COUNT(votos_encriptados.opciones_encuesta_id) AS cantidad FROM opciones_encuestas LEFT JOIN votos_encriptados ON votos_encriptados.opciones_encuesta_id = opciones_encuestas.id_opciones_encuesta WHERE opciones_encuestas.id_encuesta = :id_encuesta GROUP BY opciones_encuestas.id_opciones_encuesta;';
            $stmtVotaciones = $pdo->prepare($queryVotaciones);
            $stmtVotaciones->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_INT);
            $stmtVotaciones->execute();

            // Obtén los resultados de las votaciones
            $votaciones = $stmtVotaciones->fetchAll(PDO::FETCH_ASSOC);

            // Convierte los resultados de votaciones en arrays para usar en los gráficos
            $labels = array_column($votaciones, 'nombre_opciones');
            $data = array_column($votaciones, 'cantidad');

            // Conjunto de colores para ambos gráficos
            $colores = [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)'
            ];

            echo '<div class="container">';

            // Gráfico de barras a la izquierda
            echo '<div class="chart-container"><p>Gráfico de Barras</p><canvas id="graficoBarras"></canvas></div>';

            echo "
            <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Configuración para el gráfico de barras
                    var ctxBar = document.getElementById('graficoBarras').getContext('2d');
                    var chartBar = new Chart(ctxBar, {
                        type: 'bar',
                        data: {
                            labels: " . json_encode($labels) . ",
                            datasets: [{
                                label: 'Cantidad de Votos',
                                data: " . json_encode($data) . ",
                                backgroundColor: " . json_encode($colores) . ",
                                borderColor: 'rgba(255, 255, 255, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    precision: 0,
                                    stepSize: 1,
                                    max: 20
                                }
                            }
                        }
                    });
                });
            </script>";

            // Gráfico de pastel a la derecha
            echo '<div class="chart-container" id="graficoQueso-container"><p>Gráfico de Pastel</p><canvas id="graficoPastel"></canvas></div>';

            echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Configuración para el gráfico de pastel
                    var ctxPie = document.getElementById('graficoPastel').getContext('2d');
                    var chartPie = new Chart(ctxPie, {
                        type: 'pie',
                        data: {
                            labels: " . json_encode($labels) . ",
                            datasets: [{
                                data: " . json_encode($data) . ",
                                backgroundColor: " . json_encode($colores) . ",
                                borderColor: 'rgba(255, 255, 255, 1)',
                                borderWidth: 1
                            }]
                        }
                    });
                });
            </script>";

            echo '</div>';
            // Resto de la lógica para mostrar gráficos o información adicional
        } else {
            // Manejo de error si el usuario actual no es el creador de la encuesta
            echo "<p>Error: No tienes permisos para acceder a esta encuesta.</p>";
            echo "<script>showNotification('No tienes permisos para acceder a esta encuesta','red')</script>";
        }

        unset($pdo);
    } else {
        // Manejo de error si no se proporcionó el parámetro 'id'
        echo "Error: No se proporcionó el parámetro 'id'.";
    }
    ?>

    <?php include("Utilidades/footer.php") ?>
</body>
</html>
