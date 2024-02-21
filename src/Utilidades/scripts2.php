<?php
function registrarEvento($mensaje) {
    // Crear el nombre del archivo con el formato "registro_aaaa_mm_dd.txt"
    $nombreArchivo = "Logs/registro_" . date("Y-m-d") . ".txt";

    // Asegurarse de que la carpeta "Logs" exista, si no, crearla
    if (!is_dir("Logs")) {
        mkdir("Logs");
        chmod("Logs", 0755);
        chown("Logs", "www-data");
        chgrp("Logs", "www-data");
    }

    // Escribir en el archivo de registro
    $fechaActual = date("Y-m-d H:i:s");
    $contenidoEvento = "$fechaActual - $mensaje\n";
    file_put_contents($nombreArchivo, $contenidoEvento, FILE_APPEND);
    chmod($nombreArchivo, 0644); 
    chown($nombreArchivo, "www-data");
    chgrp($nombreArchivo, "www-data");
}

?>