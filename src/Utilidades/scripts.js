function showNotification(message, bgColor) {
    var notificationContainer = $("#notification-container");

    var notificationDiv = $("<div>").addClass("notification");
    notificationDiv.text(message);

    if (bgColor) {
        notificationDiv.css("background-color", bgColor);
    }

    var closeButton = $("<button>").addClass("close-button");
    closeButton.html("&times;");
    closeButton.click(function () {
        notificationDiv.remove();
    });

    notificationDiv.append(closeButton);
    notificationContainer.prepend(notificationDiv);
}

function scrollToBottom() {
    $('html, body').animate({
        scrollTop: $(document).height()
    }, 1200); 
}

function actualizarOpcionesRespuestas() {
    var visibilidadEncuesta = document.getElementById("visibilidad_encuesta").value;
    var visibilidadRespuestas = document.getElementById("visibilidad_respuestas");

    visibilidadRespuestas.innerHTML = '';

    if (visibilidadEncuesta === "oculta") {
                        agregarOpcion(visibilidadRespuestas, "oculta", "Oculta");
                        deshabilitarOpciones(visibilidadRespuestas, ["privada", "publica"]);
    } else if (visibilidadEncuesta === "privada") {
                        agregarOpcion(visibilidadRespuestas, "oculta", "Oculta");
                        agregarOpcion(visibilidadRespuestas, "privada", "Privada");
                        deshabilitarOpciones(visibilidadRespuestas, ["publica"]);
    } else if (visibilidadEncuesta === "publica") {
                        agregarOpcion(visibilidadRespuestas, "oculta", "Oculta");
                        agregarOpcion(visibilidadRespuestas, "privada", "Privada");
                        agregarOpcion(visibilidadRespuestas, "publica", "Pública");
    }
}

function agregarOpcion(selectElement, valor, texto) {
    var opcion = document.createElement("option");
    opcion.value = valor;
    opcion.text = texto;
    selectElement.add(opcion);
}

function deshabilitarOpciones(selectElement, opcionesDeshabilitadas) {
    for (var i = 0; i < selectElement.options.length; i++) {
                        if (opcionesDeshabilitadas.includes(selectElement.options[i].value)) {
                                            selectElement.options[i].disabled = true;
                        } else {
                                            selectElement.options[i].disabled = false;
                        }
    }
}
