<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    $conn = mysqli_connect('localhost', 'userProyecto', 'votacionesAXP24','votaciones');

    if(!$conn){
        die("no". mysql_Error());
    }
    echo "conexion exitosa";
    mysqli_close($conn);
    ?>
</body>
</html>
