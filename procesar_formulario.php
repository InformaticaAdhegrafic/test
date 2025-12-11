<?php
header('Content-Type: application/json'); // Indicamos que la respuesta es JSON

// **********************************************
// * 1. CONFIGURACIÓN (REEMPLAZA TU CORREO AQUÍ)
// **********************************************
$destinatario = "adhegrafic@adhegrafic.es"; 
$asunto = "Nueva Solicitud de Presupuesto - Web Adhegrafic";
$remitente = "no-responder@adhegrafic.es";
$dominio_web = "https://www.adhegrafic.es"; // Usado para asegurar CORS si fuera necesario

// **********************************************
// * 2. PROCESAMIENTO
// **********************************************

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2.1. Sanitización de datos
    $nombre             = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $empresa            = filter_input(INPUT_POST, 'empresa', FILTER_SANITIZE_STRING);
    $email              = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telefono           = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
    $servicio_interes   = filter_input(INPUT_POST, 'servicio_interes', FILTER_SANITIZE_STRING);
    $mensaje            = filter_input(INPUT_POST, 'mensaje', FILTER_SANITIZE_STRING);
    
    // Validación mínima
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($nombre)) {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "message" => "Datos incompletos o email inválido."]);
        exit;
    }

    // 2.2. Construcción del cuerpo del mensaje (Formato HTML para una mejor lectura)
    $cuerpo_mensaje = "<html>... (Cuerpo del mensaje como lo definimos antes, omitido por brevedad) ...</html>";
    // Nota: El contenido HTML del correo sigue siendo válido, solo debe ser una cadena de texto.

    $cuerpo_mensaje = "
    <html>
    <head><title>$asunto</title></head>
    <body>
        <p>¡Has recibido un nuevo mensaje desde el formulario de contacto de la web!</p>
        <hr style='border: 1px solid #ccc;'>
        
        <h2>Datos del Cliente:</h2>
        <table cellspacing='0' cellpadding='10' border='1' style='width: 100%; border-collapse: collapse;'>
            <tr><td style='background-color: #f2f2f2;'><strong>Nombre:</strong></td><td>$nombre</td></tr>
            <tr><td style='background-color: #f2f2f2;'><strong>Empresa:</strong></td><td>$empresa</td></tr>
            <tr><td style='background-color: #f2f2f2;'><strong>Email:</strong></td><td>$email</td></tr>
            <tr><td style='background-color: #f2f2f2;'><strong>Teléfono:</strong></td><td>$telefono</td></tr>
            <tr><td style='background-color: #f2f2f2;'><strong>Servicio de Interés:</strong></td><td>$servicio_interes</td></tr>
        </table>
        
        <h2>Detalles del Proyecto:</h2>
        <p style='white-space: pre-wrap; padding: 10px; border: 1px dashed #ddd;'>$mensaje</p>

        <hr style='border: 1px solid #ccc;'>
        <p style='font-size: 10px; color: #888;'>Mensaje enviado desde la web: " . date("d/m/Y H:i:s") . "</p>
    </body>
    </html>";


    // 2.3. Definición de encabezados
    $cabeceras = "MIME-Version: 1.0" . "\r\n";
    $cabeceras .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $cabeceras .= "From: $remitente" . "\r\n";
    $cabeceras .= "Reply-To: $email" . "\r\n"; 

    // 2.4. Envío del correo
    if (mail($destinatario, $asunto, $cuerpo_mensaje, $cabeceras)) {
        // ÉXITO: Responder con JSON
        echo json_encode(["success" => true, "message" => "Mensaje enviado con éxito."]);
    } else {
        // FALLO DE ENVÍO: Responder con JSON
        http_response_code(500); // Internal Server Error
        echo json_encode(["success" => false, "message" => "Error al enviar el correo."]);
    }

} else {
    // Si no es POST, devolver error
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
?>