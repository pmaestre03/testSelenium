<!DOCTYPE html>
<html lang="es">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Crear Encuesta</title>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <link rel="stylesheet" href="./Utilidades/styles.css?no-cache=<?php echo time(); ?>">
        <script src="Utilidades/scripts.js"></script>
</head>
<?php require('Utilidades/scripts2.php')?>
<?php include("Utilidades/header.php") ?>
<?php include("Utilidades/conexion.php") ?>
<body class="create_poll">
<div class='user-info'>Crear encuesta</div>
<div id="notification-container"></div>
<?php
if (isset($_SESSION['usuario'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verificar si se recibieron opciones de encuesta
        if (isset($_POST['titulo']) && isset($_POST['inicio']) && isset($_POST['final']) && isset($_POST['option'])) {

            // Recibir y limpiar los datos del formulario
            $creador = $_SESSION['id_user'];
            $titulo_encuesta = $_POST["titulo"];
            $fecha_inicio = date("Y-m-d H:i:s", strtotime($_POST["inicio"]));
            $fecha_fin = date("Y-m-d H:i:s", strtotime($_POST["final"]));

            try {
                $dsn = "mysql:host=localhost;dbname=votaciones";
                $pdo = new PDO($dsn, 'userProyecto', 'votacionesAXP24');
                $query = $pdo->prepare("INSERT INTO encuestas (fech_inicio, fecha_fin, titulo_encuesta, creador, imagen_titulo) VALUES (:fech_inicio, :fecha_fin, :titulo_encuesta, :creador, :imagen_titulo)");
                $query->bindParam(':fech_inicio', $fecha_inicio, PDO::PARAM_STR);
                $query->bindParam(':fecha_fin', $fecha_fin, PDO::PARAM_STR);
                $query->bindParam(':titulo_encuesta', $titulo_encuesta, PDO::PARAM_STR);
                $query->bindParam(':creador', $creador, PDO::PARAM_INT);

                // Mover el archivo de imagen del título a la carpeta 'uploads'
                if(isset($_FILES["imgTitulo"]) && $_FILES["imgTitulo"]["error"] == 0){
                    $target_dir = "uploads/";
                    $target_file = $target_dir . time() . "_" . basename($_FILES["imgTitulo"]["name"]);

                    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                    if ($_FILES["imgTitulo"]["size"] < 500000) {
                        if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif" ) {
                            if (move_uploaded_file($_FILES["imgTitulo"]["tmp_name"], $target_file)) {
                                $query->bindParam(':imagen_titulo', $target_file, PDO::PARAM_STR);
                            } else {
                                echo "Hubo un error al subir el archivo.";
                                $query->bindValue(':imagen_titulo', NULL, PDO::PARAM_NULL);
                            }
                        } else {
                            echo "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
                            $query->bindValue(':imagen_titulo', NULL, PDO::PARAM_NULL);
                        }
                    } else {
                        echo "El archivo es demasiado grande.";
                        $query->bindValue(':imagen_titulo', NULL, PDO::PARAM_NULL);
                    }
                } else {
                    $query->bindValue(':imagen_titulo', NULL, PDO::PARAM_NULL);
                }
                $query->execute();

                $id_encuesta = $pdo->lastInsertId();
                $options = isset($_POST["option"]) ? $_POST["option"] : [];

                if (!empty($options)) {
                    foreach ($options as $key => $option) {
                        $option_query = $pdo->prepare("INSERT INTO opciones_encuestas (id_encuesta, nombre_opciones, imagen_opciones) VALUES (:id_encuesta, :nombre_opciones, :imagen_opciones)");
                        $option_query->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_INT);
                        $option_query->bindParam(':nombre_opciones', $option, PDO::PARAM_STR);

                        if (!empty($_FILES["imgOpcion" . ($key + 1)]["name"])) {
                            $target_dir = "uploads/";
                            $target_file = $target_dir . time() . "_" . basename($_FILES["imgOpcion" . ($key + 1)]["name"]);

                            if (move_uploaded_file($_FILES["imgOpcion" . ($key + 1)]["tmp_name"], $target_file)) {
                                $option_query->bindParam(':imagen_opciones', $target_file, PDO::PARAM_STR);
                            } else {
                                $option_query->bindValue(':imagen_opciones', NULL, PDO::PARAM_NULL);
                            }
                        } else {
                            $option_query->bindValue(':imagen_opciones', NULL, PDO::PARAM_NULL);
                        }

                        $option_query->execute();

                        if ($option_query->rowCount() > 0) {
                            // La opción se insertó correctamente
                        } else {
                            echo "Error al insertar la opción '$option'<br>";
                        }
                    }
                }

            } catch (PDOException $e) {
                echo "Error en la base de datos: " . $e->getMessage();
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
    var optionNumber = 1;
    $(document).ready(function () {
        localStorage.removeItem('nameInicio');
        localStorage.removeItem('nameFinal');
        localStorage.removeItem('nameTitulo');
        localStorage.removeItem('nameOpciones');
        localStorage.removeItem('imgOpciones');
        
        // Crear Fecha Inicio Encuesta
        var container_poll = $('<div>').addClass('poll-container');
        var box_poll = $('<div>').attr('id', 'box');
        var fecha_inicio = $('<label>').text('Fecha inicio:');
        var inputElement = $('<input>').attr({
            type: 'date',
            name: 'fecha_inicio',
            id: 'fecha_inicio'
        }).on('input', function () {
            $(this).closest('#box').nextAll('#box').remove();
            $('.borrar').hide();
            $('.borrar[data-option="101"]').show();
        }).keypress(function(event) {
            var currentBox = $(this).closest('#box');
            if (event.which == 13 || event.which == 9) {
                if (currentBox.next('#box').length) {
                } else {
                    validatePoll('fecha_inicio');
                    scrollToBottom();
                }
            }
        });
        var buttonElement = $('<button>').attr({id: 'validate', class: 'borrar button-login', 'data-option': '101'}).text('Validar').click(function() {
        });
        box_poll.append(fecha_inicio, inputElement, buttonElement);
        container_poll.append(box_poll);
        $('.user-info').after(container_poll);
    $('#validate').click(function(){
        if (box_poll.next('#box').length === 0) {
        validatePoll($(this).prev("input[name]").attr("name"));  }
    });

    // Crear Fecha Final Encuesta
    function createBoxFinal(){
        var inputElement = $('<div id="box">').append(
            $('<label>').text('Fecha Final:'),
            $('<input>').attr({ type: 'date', name: 'fecha_final', id: 'fecha_final'}).on('input', function () {
                $(this).closest('#box').nextAll('#box').remove();
                $('.borrar').hide();
                $('.borrar[data-option="102"]').show();
            }).keypress(function(event) {
                var currentBox = $(this).closest('#box');
                if (event.which == 13 || event.which == 9) {
                    if (currentBox.next('#box').length) {
                    } else {
                        validatePoll('fecha_final');
                        scrollToBottom();
                    }
                }
            }),
            $('<button>').attr({ id: 'validate', class: 'borrar button-login', 'data-option': '102'}).text('Validar').click(function(){
                if (inputElement.next('#box').length === 0) {
                    validatePoll($(this).prev("input[name]").attr("name"));  }  
            })
        );

        $('.poll-container').append(inputElement);
    }

    // Crear Titulo Encuesta
    function createBoxTitle(){
        var inputElement = $('<div id="box">').append(
            $('<label>').text('Titulo encuesta:'),
            $('<input>').attr({ type: 'file', name: 'imgTitulo', accept:"image/*"}).on('input', function () {
                $(this).closest('#box').nextAll('#box').remove();
                $('.borrar').hide();
                $('.borrar[data-option="103"]').show();
            }),
            $('<input>').attr({ type: 'text', name: 'titulo', id:'titulo', placeholder: 'TITULO'}).on('input', function () {
                $(this).closest('#box').nextAll('#box').remove();
                $('.borrar').hide();
                $('.borrar[data-option="103"]').show();
            }).keypress(function(event) {
                var currentBox = $(this).closest('#box');
                if (event.which == 13 || event.which == 9) {
                    if (currentBox.next('#box').length) {
                    } else {
                        validatePoll('titulo');
                        scrollToBottom();
                    }
                }
            }),
            $('<button>').attr({ id: 'validate', class: 'borrar button-login', 'data-option': '103'}).text('Validar').click(function(){
                if (inputElement.next('#box').length === 0) {
                    validatePoll('titulo');  }  
            })
        );

        $('.poll-container').append(inputElement);
    }

    // Función para crear una nueva opción
    function createBoxOptions(optionNumber) {
        var optionDiv = $('<div id="box">').append(
            $('<label>').text('Opción encuesta ' + optionNumber + ':'),
            $('<input>').attr({ type: 'file', name: 'imgOpcion' + optionNumber, accept:"image/*"}).on('input', function () {
                $(this).closest('#box').nextAll('#box').remove();
                // Habilitar el botón de "Añadir opción" solo en la opción actual
                $('.add-option').hide();
                $('.add-option[data-option="' + optionNumber + '"]').show();
            }),
            $('<input>').attr({ type: 'text', name: 'opcion' + optionNumber, placeholder: 'Opción ' + optionNumber}).on('input', function () {
                $(this).closest('#box').nextAll('#box').remove();
                // Habilitar el botón de "Añadir opción" solo en la opción actual
                $('.add-option').hide();
                $('.add-option[data-option="' + optionNumber + '"]').show();
            }).keypress(function(event) {
                var currentBox = $(this).closest('#box');
                if (event.which == 13 || event.which == 9) {
                    if (currentBox.next('#box').length) {
                    } else {
                        createBoxOptions(optionNumber + 1);
                        scrollToBottom();
                    }
                }
            }),
            $('<button>').attr({ class: 'add-option button-login', 'data-option': optionNumber }).text('Añadir opción').prop('disabled', false).click(function(){
                var currentOptionNumber = $(this).data('option');
                if ($('input[name=opcion' + currentOptionNumber + ']').val().trim() === "") {
                    showNotification("La opción " + currentOptionNumber + " no puede estar vacía", 'red');
                } else {
                    createBoxOptions(optionNumber + 1);
                    scrollToBottom();
                }
            }),

        );
        if (optionNumber >= 2) {
            $('<button>').attr({ id: 'send-poll', class: 'add-option button-login', 'data-option': optionNumber }).text('Enviar encuesta').prop('disabled', false).click(function(){
                var currentOptionNumber = $(this).data('option');
                var nameOpciones = [];
                // Recorrer los inputs de opciones y guardar valores
                for (var i = 1; i <= currentOptionNumber; i++) {
                    var opcionValue = $('input[name=opcion' + i + ']').val().trim();
                    if (opcionValue !== "") {
                        nameOpciones.push(opcionValue);
                    } else {
                        showNotification("La opción " + i + " no puede estar vacía", 'red');
                        return;
                    }
                }
                //Guardar TODAS las opciones en una array
                localStorage.setItem('nameOpciones', JSON.stringify(nameOpciones));

                createBoxBD();
            }).appendTo(optionDiv);
        }
        $('.poll-container').append(optionDiv);

        // Habilitar el botón de "Añadir opción" solo en la opción actual
        $('.add-option').hide();
        $('.add-option[data-option="' + optionNumber + '"]').show();
    }

    function validatePoll(inputType){
        console.log(inputType);
        switch(inputType) {
            case "fecha_inicio":
                var nameInicio = $('input[name=fecha_inicio]').val();
                var dateHoy = new Date();
                var dateInicio = new Date(nameInicio);
                if (nameInicio.trim()===""){
                    showNotification("La fecha inicial no puede estar vacía", 'red');
                }
                else if((dateInicio)=>dateHoy){
                    localStorage.setItem('nameInicio',nameInicio);
                    $('.borrar').hide();
                    $('.borrar[data-option="102"]').show();
                    createBoxFinal();
                }
                else{
                    showNotification("La fecha inicial tiene que ser posterior al dia de hoy", "red");
                }  
                break;

            case "fecha_final":
                var nameFinal = $('input[name=fecha_final]').val();
                var nameInicio = $('input[name=fecha_inicio]').val();
                var dateInicio = new Date(nameInicio);
                var dateFinal = new Date(nameFinal);
                if (nameFinal.trim()===""){
                    showNotification("La fecha final no puede estar vacía", 'red');
                }
                else if(dateFinal<dateInicio){
                    showNotification("La fecha final no puede ser inferior a la fecha inicial", "red");
                } 
                else{
                    localStorage.setItem('nameFinal',nameFinal);
                    $('.borrar').hide();
                    $('.borrar[data-option="103"]').show();
                    createBoxTitle();
                }
                break;

            case "titulo":
                var nameTitulo = $('input[name=titulo]').val();
                if(nameTitulo.trim()===""){
                    showNotification("El titulo no puede estar vacío", 'red');
                }
                else{
                    localStorage.setItem('nameTitulo',nameTitulo);
                    $('.borrar').hide();
                    createBoxOptions(1);
                }
                break;
            }
        }
    }
    );

    function createBoxBD() {
        var inicio = localStorage.getItem('nameInicio');
        var final = localStorage.getItem('nameFinal');
        var titulo = localStorage.getItem('nameTitulo');
        var opciones = localStorage.getItem('nameOpciones');
        var options = JSON.parse(opciones);

        var form = $('<form>').attr({
            action: 'create_poll.php',
            method: 'POST',
            enctype: "multipart/form-data"
        });

        var fechasTitulo = [
            { name: 'titulo', value: titulo },
            { name: 'inicio', value: inicio },
            { name: 'final', value: final }
        ];

        $.each(fechasTitulo, function(i, campos) {
            $('<input>').attr({
                type: 'hidden',
                name: campos.name,
                value: campos.value
            }).appendTo(form);
        });

        $.each(options, function(i, option) {
            $('<input>').attr({
                type: 'hidden',
                name: 'option[]', 
                value: option
            }).appendTo(form);
        });

        var inputImgTitulo = $('input[name="imgTitulo"]');
        inputImgTitulo.css('display', 'none');
        form.append(inputImgTitulo.clone());

        //agregar inputs de imagen al form
        for (var i = 1; i <= options.length; i++) {
            // Obtener el input de tipo file correspondiente
            var inputFile = $('input[name="imgOpcion' + i + '"]');
            inputFile.addClass('hidden');
            inputFile.css('display', 'none');
            // Clonar y agregar el input de tipo file al formulario
            form.append(inputFile.clone());
        }

        $('body').append(form);
        form.submit();
    }
</script>
<?php include("Utilidades/footer.php") ?>
</body>
</html>
