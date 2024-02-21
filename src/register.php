<?php require('Utilidades/scripts2.php')?>
<?php
// Conectar a la base de datos
$conn = mysqli_connect('localhost', 'userProyecto', 'votacionesAXP24', 'votaciones');

// Verificar la conexión
if (!$conn) {
    die("La conexión a la base de datos falló: " . mysqli_connect_error());
}

// Verificar si el formulario se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        function generateRandomToken() {
                            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                            $token = '';
                            $length = 32;
                            for ($i = 0; $i < $length; $i++) {
                                                $token .= $characters[rand(0, strlen($characters) - 1)];
                            }

                            return $token;
        }

        // Recuperar datos del formulario
        $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
        $prefijo = mysqli_real_escape_string($conn, $_POST['prefijo']);
        $mail = mysqli_real_escape_string($conn, $_POST['mail']);
        $password = hash('sha512', $_POST['password']);
        $pais = mysqli_real_escape_string($conn, $_POST['pais']);
        $telefono = mysqli_real_escape_string($conn, $_POST['telefono']);
        $ciudad = mysqli_real_escape_string($conn, $_POST['ciudad']);
        $codigoPostal = mysqli_real_escape_string($conn, $_POST['codigoPostal']);

        // Consulta SQL para verificar si el correo ya existe
        $checkQuery = "SELECT COUNT(*) as count FROM users WHERE email = '$mail' OR telefono = '$telefono' ";
        $checkResult = mysqli_query($conn, $checkQuery);
        $checkData = mysqli_fetch_assoc($checkResult);

        // Si el correo ya existe, mostrar un mensaje y no realizar la inserción
        if ($checkData['count'] > 0) {
                            $mensaje = "El correo electrónico o el teléfono ya ha sido registrado.";
                            $colorFondo = "red";
                            echo "<script>var mensajeNotificacion = '$mensaje'; var colorFondo = '$colorFondo';</script>";
        } else {
                            // Si el correo no existe, realizar la inserción
                            // Imprimir el contenido del prefijo para depuración
                            echo "Contenido del prefijo: ";
                            var_dump($prefijo);

                            $insertQuery = "INSERT INTO users (nombre, contrasea_cifrada, email, telefono, nombre_pais, rol, pref, nombre_ciudad, codigo_postal) VALUES ('$nombre', '$password', '$mail', '$telefono', '$pais', 'user', '$prefijo', '$ciudad', '$codigoPostal')";
                            
                            // Ejecutar la consulta
                            if (mysqli_query($conn, $insertQuery)) {

                                                // Realizar la autenticación del usuario recién registrado
                                                $usuario = $mail;  // Utilizar el correo electrónico como nombre de usuario
                                                $contrasenya = $password;  // Utilizar la contraseña cifrada
                                                // Establecer la conexión a la base de datos con PDO
                                                try {
                                                                    $pdo = new PDO('mysql:host=localhost;dbname=votaciones', 'userProyecto', 'votacionesAXP24');
                                                                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                                } catch (PDOException $e) {
                                                                    die("Error en la conexión a la base de datos: " . $e->getMessage());
                                                }

                                                $querystr = "SELECT id_user,email,nombre FROM users WHERE email=:usuario AND contrasea_cifrada=:contrasenya";
                                                $query = $pdo->prepare($querystr);
                                                $query->bindParam(':usuario', $usuario, PDO::PARAM_STR);
                                                $query->bindParam(':contrasenya', $contrasenya, PDO::PARAM_STR);

                                                $query->execute();

                                                $filas = $query->rowCount();
                                                if ($filas > 0) {
                                                                    // Obtén el nombre de usuario desde la base de datos
                                                                    $row = $query->fetch(PDO::FETCH_ASSOC);
                                                                    $nombre_usuario = $row['nombre'];
                                                                    $email = $row['email'];
                                                                    $idUser = $row['id_user'];
                                                                    $searchInvite = "SELECT user_email from invitacion where user_email='$email'";
                                                                                if (mysqli_query($conn, $searchInvite)) {
                                                                                                    $updateInvite = "UPDATE invitacion SET email='$email ',id_user=$idUser where user_email='$email '";
                                                                                                    mysqli_query($conn,$updateInvite);
                                                                                }
                                                                    session_start();
                                                                    $_SESSION['redirigido'] = true;         
                                                                    $token = generateRandomToken();
                                                                    $validationLink = "https://aws22.ieti.site/validar-email.php?token=$token";
                                                                    mail($email, 'Validate your token', $validationLink);
                                                                    $tokenQuery = "INSERT INTO tokens_emails(user_id,token) VALUES (:id_user,:token)";
                                                                    $queryToken = $pdo->prepare($tokenQuery);
                                                                    $queryToken->bindParam(':id_user',$idUser,PDO::PARAM_STR);
                                                                    $queryToken->bindParam(':token',$token,PDO::PARAM_STR);
                                                                    $queryToken->execute();
                                                                    registrarEvento("El usuario: $usuario ha sido creado correctamente");
                                                                    echo "Usuario Correcto: Hola $nombre_usuario";
                                                                    //echo "<script>showNotification('Usuario creado correctamente, comprueba tu correo para confirmar la cuenta')</script>";
                                                                    header("Location: index.php");
                                                                    exit();
                                                } else {
                                                                    echo "<script>showNotification('Usuario o contraseña incorrecto','red')</script>";
                                                }

                                                unset($pdo);
                                                unset($query);

                            } else {
                                                echo "Error al insertar datos: " . mysqli_error($conn);
                            }
        }

        // Cerrar la conexión
        mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Formulario Dinámico</title>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<link rel="stylesheet" href="./Utilidades/styles.css?no-cache=<?php echo time(); ?>">
<script src="./Utilidades/scripts.js"></script>

</head>

<body>

<?php include("Utilidades/header.php") ?>

<div id="register"></div>
<script>
$(document).ready(function () {
var titulo = $('<h1>', { class: 'register-info' });
titulo.text('Registro');
$('#register').after(titulo);

var divContenedor = $('<div>', { class: 'register-container' });
$('.register-info').after(divContenedor);

var divNotification = $('<div>', {id: 'notification-container'})
divContenedor.append(divNotification); 
var miFormulario = $('<form>', { id: 'miFormulario', action: '', method: 'post' });
divContenedor.append(miFormulario);  // Utilizar el div contenedor como elemento padre

if (typeof mensajeNotificacion !== 'undefined' && typeof colorFondo !== 'undefined') {
    // Llamar a la función showNotification con el mensaje y el color de fondo
    showNotification(mensajeNotificacion, colorFondo);
}

var pasoActual = 1;

var miFormulario = $('#miFormulario');
var formularioNombreCreado = false;
var formularioMailCreado = false;
var formularioPasswordCreado = false;
var formularioConfirmarPasswordCreado = false;
var formularioPaisesCreado = false;
var formularioTelefonoCreado = false;
var formularioCiudadCreado = false;
var formularioCodigoPostalCreado = false;
var botonSubmitCreado = false;

var nombreValido = false;
var mailValido = false;
var telefonoValido = false;
var ciudadValido = false;
var cpValido = false;
var confirmarPasswordValido = false;

crearSiguienteFormulario();

function crearFormularioNombre() {

    if (!formNombre) {
        var formNombre = $('<div>',{ id: 'formularioNombre' });
        formNombre.append($('<label>', { for: 'nombre', text: 'Nombre:' }));
        formNombre.append($('<input>', { type: 'nombre', id: 'nombre', name: 'nombre' }));
        formNombre.append($('<img>', { src: 'https://static.vecteezy.com/system/resources/previews/018/824/865/original/green-check-mark-button-without-text-free-png.png', class: 'imagen-correcto', alt: 'Correcto' })); // Agrega la imagen
        miFormulario.append(formNombre);
    }

    $('#nombre').on('input', function () {
        var nombre = $(this).val().trim();

        if (nombre !== '' && !/^\d+$/.test(nombre)) {
            nombreValido = true;
        } else {
            eliminarFormularios(['mail','password','confirmarPassword','pais','prefijoTexto','telefono','ciudad','codigoPostal'])
            nombreValido = false;
            pasoActual = 2;
            eliminarBotonEnviar()
        }
    });
    
}

function crearFormularioMail() {
    if (!formularioMailCreado) {
        var formularioMail = $('<div>',{ id: 'formularioMail' });;
        formularioMail.append($('<label>', { for: 'mail', text: 'Correo Electrónico:' }));
        formularioMail.append($('<input>', { type: 'email', id: 'mail', name: 'mail' }));
        formularioMail.append($('<img>', { src: 'https://static.vecteezy.com/system/resources/previews/018/824/865/original/green-check-mark-button-without-text-free-png.png', class: 'imagen-correcto2', alt: 'Correcto' })); // Ajusta la ruta de la imagen
        miFormulario.append(formularioMail);

        formularioMailCreado = true;

        formularioMail.on('submit', function (event) {
            event.preventDefault();
            var correo = $('#mail').val().trim();
            validarCorreoElectronico(correo);
        });

        $('#mail').on('input', function () {
            var correo = $(this).val().trim();
            validarCorreoElectronico(correo);
        });

        return formularioMail;
    }
}


function validarCorreoElectronico(correo) {
    var expresionRegularCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    var correoValido = expresionRegularCorreo.test(correo);

    var imagenCorrecto2 = $('.imagen-correcto2');

    if (correoValido) {
        mailValido = true;
    } else {
        mailValido = false;
        eliminarFormularios(['password','confirmarPassword','pais','prefijoTexto','telefono','ciudad','codigoPostal']);
        eliminarBotonEnviar()
        pasoActual = 3;
    }
}


function crearFormularioPassword() {
    if (!formularioPasswordCreado) {
        var formularioPassword = $('<div>',{ id: 'formularioPassword' });
        formularioPassword.append($('<label>', { for: 'password', text: 'Contraseña:' }));
        formularioPassword.append($('<input>', { type: 'password', id: 'password', name: 'password' }));
        formularioPassword.append($('<img>', { src: 'https://static.vecteezy.com/system/resources/previews/018/824/865/original/green-check-mark-button-without-text-free-png.png', class: 'imagen-correcto3', alt: 'Correcto' }));
        miFormulario.append(formularioPassword);

        formularioPasswordCreado = true;
        
        var imagenCorrecto3 = $('.imagen-correcto3');

        $('#password').on('input', function () {
            var password = $(this).val().trim();

            if (validarPassword(password)) {
                imagenCorrecto3.show(); 
            }else{
                eliminarFormularios(['confirmarPassword','pais','prefijoTexto','telefono','ciudad','codigoPostal'])
                pasoActual = 4;
            }
        });
    }
}

function validarPassword(password) {
        var longitudMinima = 8;
        var tieneNumero = /\d/.test(password);
        var tieneMayuscula = /[A-Z]/.test(password);
        var tieneMinuscula = /[a-z]/.test(password);
        var tieneCaracterEspecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        return password.length >= longitudMinima && tieneNumero && tieneMayuscula && tieneMinuscula && tieneCaracterEspecial;
}

function crearFormularioConfirmarPassword() {
    if (!formularioConfirmarPasswordCreado) {
        var formularioConfirmarPassword = $('<div>',{ id: 'formularioConfirmarPassword' });
        formularioConfirmarPassword.append($('<label>', { for: 'confirmarPassword', text: 'Confirmar Contraseña:' }));
        formularioConfirmarPassword.append($('<input>', { type: 'password', id: 'confirmarPassword', name: 'confirmarPassword' }));
        formularioConfirmarPassword.append($('<img>', { src: 'https://static.vecteezy.com/system/resources/previews/018/824/865/original/green-check-mark-button-without-text-free-png.png', class: 'imagen-correcto4', alt: 'Correcto' }));
        miFormulario.append(formularioConfirmarPassword);

        formularioConfirmarPasswordCreado = true;

        var imagenCorrecto4 = $('.imagen-correcto4');


        $('#confirmarPassword').on('input', function () {
            var confirmarPassword = $(this).val().trim();
            var password = $('#password').val().trim();

            if (confirmarPassword === password) {
                // Añadir clase y atributo readonly a los campos de contraseña
                confirmarPasswordValido = true;
                $('#password').addClass('campo-desabilitado').attr('readonly', true);
                $('#confirmarPassword').addClass('campo-desabilitado').attr('readonly', true);
            }
        });
    }
}

function crearFormularioPaises() {
    if (!formularioPaisesCreado) {
        var formularioPaises = $('<div>',{ id: 'formularioPaises' });
        formularioPaises.append($('<label>', { for: 'pais', text: 'Selecciona un país:' }));
        var selectPais = $('<select>', { id: 'pais', name: 'pais' });

        // Agregar opción en blanco como la primera
        selectPais.append('<option value="" data-pref="">Selecciona un país</option>');

        // Agregar las opciones al select desde la base de datos
        <?php
        $conn = mysqli_connect('localhost', 'userProyecto', 'votacionesAXP24');
        mysqli_select_db($conn, 'votaciones');
        $consulta = "SELECT nombre, pref FROM `votaciones`.`paises`;";
        $resultat = mysqli_query($conn, $consulta);
        $paises = array();
        while ($fila = mysqli_fetch_assoc($resultat)) {
                            $paises[] = $fila;
        }
        mysqli_close($conn);

        foreach ($paises as $pais) {
                            echo 'selectPais.append("<option value=\'" + \'' . $pais['nombre'] . '\' + "\' data-pref=\'" + \'' . $pais['pref'] . '\' + "\'>" + \'' . $pais['nombre'] . '\' + "</option>");';
        }
        ?>

        formularioPaises.append(selectPais);
        miFormulario.append(formularioPaises);

        formularioPaisesCreado = true;

        $('#pais').on('change', function () {
            var selectedPais = $(this).val();
            if (selectedPais !== '') {
                    crearFormularioTelefono(selectedPais);
                // Eliminar la opción "Selecciona un país" después de la selección
                $(this).find('option[value=""]').remove();
            }
        });

        // Crear el campo de teléfono inicialmente si la opción seleccionada no está vacía
        if ($('#pais').val() !== '') {
            crearFormularioTelefono($('#pais').val());
            
            // Eliminar la opción "Selecciona un país" después de la selección inicial
            $('#pais').find('option[value=""]').remove();
        }
    }
}



function crearFormularioTelefono(selectedPais) {
    if (!formularioTelefonoCreado) {
        var formularioTelefono = $('<div>',{ id: 'formularioTelefono' });
        formularioTelefono.append($('<label>', { for: 'telefono', text: 'Teléfono:' }));

        // Input para el prefijo (no editable por el usuario)
        var inputPrefijo = $('<input>', { type: 'text', id: 'prefijoTexto', name: 'prefijo', readonly: true });

        // Input oculto para el prefijo
        var inputPrefijoHidden = $('<input>', { type: 'hidden', id: 'prefijo', name: 'prefijo' });

        // Input para el número de teléfono (editable por el usuario)
        var inputTelefono = $('<input>', { type: 'tel', id: 'telefono', name: 'telefono', placeholder: 'Número de teléfono' });

        formularioTelefono.append(inputPrefijo);
        formularioTelefono.append(inputTelefono);
        formularioTelefono.append(inputPrefijoHidden);  // Agrega el campo oculto
        formularioTelefono.append($('<img>', { src: 'https://static.vecteezy.com/system/resources/previews/018/824/865/original/green-check-mark-button-without-text-free-png.png', class: 'imagen-correcto5', alt: 'Correcto' }));
        miFormulario.append(formularioTelefono);

        formularioTelefonoCreado = true;

        // Actualizar el prefijo al cambiar de país
        $('#pais').on('change', function () {
            var nuevoPrefijo = $('option:selected', this).data('pref');
            $('#prefijoTexto').val(nuevoPrefijo);
            $('#prefijo').val(nuevoPrefijo);  // Actualiza también el campo oculto
        });

        // Actualizar el prefijo inicialmente
        var prefijoInicial = $('option:selected', '#pais').data('pref');
        $('#prefijoTexto').val(prefijoInicial);
        $('#prefijo').val(prefijoInicial);  // Actualiza también el campo oculto


        $('#telefono').on('input', function () {
            var imagenCorrecto5 = $('.imagen-correcto5');
            var telefono = $(this).val().trim();

            // Verificar si el teléfono tiene solo números y está en el rango deseado
            var regexNumeros = /^\d+$/;
            var longitudMinima = 8;
            var longitudMaxima = 15;

            if (regexNumeros.test(telefono) && telefono.length >= longitudMinima && telefono.length <= longitudMaxima) {
                telefonoValido = true;
            }else{
                    telefonoValido =  false
                eliminarFormularios(['ciudad','codigoPostal'])
                pasoActual = 6;
            }
        });
    }
}



function crearFormularioCiudad() {
    if (!formularioCiudadCreado) {
        var formularioCiudad = $('<div>',{ id: 'formularioCiudad' });
        formularioCiudad.append($('<label>', { for: 'ciudad', text: 'Ciudad:' }));
        formularioCiudad.append($('<input>', { type: 'text', id: 'ciudad', name: 'ciudad' }));
        formularioCiudad.append($('<img>', { src: 'https://static.vecteezy.com/system/resources/previews/018/824/865/original/green-check-mark-button-without-text-free-png.png', class: 'imagen-correcto8', alt: 'Correcto' })); // Ajusta la ruta de la imagen
        miFormulario.append(formularioCiudad);
        formularioCiudadCreado = true;

        $('#ciudad').on('input', function () {
            var imagenCorrecto8 = $('.imagen-correcto8');
            var ciudad = $(this).val().trim();

            if (ciudad !== '') {
                ciudadValido = true;
            }else{
                ciudadValido = false;
                eliminarFormularios(['codigoPostal'])
                eliminarBotonEnviar()
                pasoActual = 7;
            }
        });
        }         
}

function crearFormularioCodigoPostal() {
    if (!formularioCodigoPostalCreado) {
        var formularioCodigoPostal = $('<div>', { id: 'formularioCodigoPostal' });
        formularioCodigoPostal.append($('<label>', { for: 'codigoPostal', text: 'Código Postal:' }));
        formularioCodigoPostal.append($('<input>', { type: 'text', id: 'codigoPostal', name: 'codigoPostal' }));
        formularioCodigoPostal.append($('<img>', { src: 'https://static.vecteezy.com/system/resources/previews/018/824/865/original/green-check-mark-button-without-text-free-png.png', class: 'imagen-correcto9', alt: 'Correcto' })); // Ajusta la ruta de la imagen
        miFormulario.append(formularioCodigoPostal);

        // Monitorear cambios en el código postal
        $('#codigoPostal').on('input', function () {
            var imagenCorrecto9 = $('.imagen-correcto9');
            var nuevoCodigoPostal = $(this).val().trim();

            // Verificar si el código postal tiene solo números
            var regexNumeros = /^\d+$/;

        if (nuevoCodigoPostal !== '' && regexNumeros.test(nuevoCodigoPostal)) {
                    cpValido = true;
                    if (!botonSubmitCreado) {
                            var botonSubmit = $('<button>', { type: 'submit', text: 'Enviar', class: 'enviar-registro' ,id: 'enviar-registro'});
                            miFormulario.append(botonSubmit);
                            botonSubmitCreado = true;
                    }
            } else {
                cpValido = false;
                eliminarBotonEnviar()
            }
        });

        formularioCodigoPostalCreado = true;
    }
}




function eliminarBotonEnviar() {
        miFormulario.find('button[type="submit"]').remove();
        botonSubmitCreado = false;
}

function eliminarFormularios(camposAEliminar) {
        for (var i = 0; i < camposAEliminar.length; i++) {
                    var formularioPadre = $('#' + camposAEliminar[i]).closest('div[id^="formulario"]');
                    formularioPadre.remove();
        }

        formularioMailCreado = false;
        formularioPasswordCreado = false;
        formularioConfirmarPasswordCreado = false;
        formularioPaisesCreado = false;
        formularioTelefonoCreado = false;
        formularioCiudadCreado = false;
        formularioCodigoPostalCreado = false;
        botonSubmitCreado = false;

        pasoActual = camposAEliminar.includes('password') ? 3 : pasoActual;

        if (camposAEliminar.includes('codigoPostal')) {
                    $('#miFormulario').find('div[id^="formularioCodigoPostal"]').remove();
                    eliminarBotonEnviar();
        }
}

function eliminarFormularioPrefijo() {
        var formularioPadre = $('#prefijo').closest('form');
        var etiquetaPadre = formularioPadre.find('label[for="prefijo"]');
        formularioPadre.find('#prefijoTexto').remove();
        formularioPadre.find('#prefijo').remove();
        etiquetaPadre.remove();

        formularioPaisesCreado = false;
        formularioTelefonoCreado = false;

        eliminarBotonEnviar();
}

function comprovarLastChild() {
        var lastInput = $("#miFormulario :input:last");
        return lastInput.length > 0 && lastInput.val().trim() !== '';
}

$(document).keypress(function (e) {
        if (e.which === 13) {
                            e.preventDefault();
                            var inputActual = $('input:focus');
                            if (inputActual.val().trim() !== '') {
                                                if (inputActual.attr('id') === 'mail') {
                                                                    validarCorreoElectronico(inputActual.val().trim());
                                                if (mailValido) {
                                                                    crearSiguienteFormulario();
                                                } else {
                                                                    showNotification('Mail no válido', 'red');
                                                }
                                                } else if (inputActual.attr('id') === 'confirmarPassword') {
                                                                    if (confirmarPasswordValido) {
                                                                                        crearSiguienteFormulario();
                                                                    } else {
                                                                                        showNotification('Las contraseñas no coinciden', 'red');
                                                                    }
                                                } else if (inputActual.attr('id') === 'telefono') {
                                                                    if (telefonoValido) {
                                                                                        crearSiguienteFormulario();
                                                                    } else {
                                                                                        showNotification('El telefono no es válido', 'red');
                                                                    }
                                                } else {
                                                                    if (comprovarLastChild()) {
                                                                                        crearSiguienteFormulario();
                                                                    }
                                                }
                            }
        }
        });

function crearSiguienteFormulario() {
        switch (pasoActual) {
                    case 1:
                            crearFormularioNombre();
                            break;
                    case 2:
                            crearFormularioMail();
                            showNotification("Nombre válido");
                            break;
                    case 3:
                            crearFormularioPassword();
                            showNotification("Mail válido");
                            break;
                    case 4:
                            if (validarPassword($('#password').val().trim())) {
                                        crearFormularioConfirmarPassword();
                                        showNotification("Contraseña valida");
                            } else {
                                        showNotification("Contraseña no valida",'red');
                                        eliminarFormularios(['confirmarPassword', 'pais', 'prefijoTexto', 'telefono', 'ciudad', 'codigoPostal']);
                                        pasoActual = 3; // Mantener el paso actual en contraseña si no es válida
                            }
                            break;
                    case 5:
                            crearFormularioPaises();
                            showNotification("Contraseña validada");
                            break;
                    case 6:
                            crearFormularioCiudad();
                            showNotification("Telefono válido");
                            break;
                    case 7:
                            crearFormularioCodigoPostal();
                            showNotification("Ciudad valida");
                            break;
        }
        pasoActual++;
}
});
</script>
</body>
<?php include("Utilidades/footer.php") ?>

</html>
