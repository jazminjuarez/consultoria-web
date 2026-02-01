<?php
// =====================================================
// PROCESAR FORMULARIO DE CONTACTO
// =====================================================

require_once 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos del formulario
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
$empresa = isset($_POST['empresa']) ? trim($_POST['empresa']) : '';
$servicio = isset($_POST['servicio']) ? trim($_POST['servicio']) : '';
$mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';

// Validaciones básicas
if (empty($nombre) || empty($email) || empty($mensaje)) {
    echo json_encode(['success' => false, 'message' => 'Por favor complete los campos obligatorios']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El correo electrónico no es válido']);
    exit;
}

// Conectar a la base de datos
$conexion = conectarDB();

// Preparar consulta para insertar
$stmt = $conexion->prepare("INSERT INTO contactos (nombre, email, telefono, empresa, servicio_interes, mensaje) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $nombre, $email, $telefono, $empresa, $servicio, $mensaje);

if ($stmt->execute()) {
    // Enviar correo de confirmación al cliente
    $enviado = enviarCorreoConfirmacion($nombre, $email, $servicio);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Gracias por contactarnos. Hemos recibido tu mensaje y te responderemos pronto.',
        'email_enviado' => $enviado
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar el mensaje. Intente nuevamente.']);
}

$stmt->close();
$conexion->close();

// =====================================================
// FUNCIÓN PARA ENVIAR CORREO DE CONFIRMACIÓN
// =====================================================
function enviarCorreoConfirmacion($nombre, $email, $servicio) {
    $asunto = "Gracias por contactar a nuestra Consultoría";
    
    $cuerpo = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>¡Gracias por contactarnos!</h1>
            </div>
            <div class='content'>
                <p>Hola <strong>{$nombre}</strong>,</p>
                <p>Hemos recibido tu solicitud de información" . (!empty($servicio) ? " sobre <strong>{$servicio}</strong>" : "") . ".</p>
                <p>Nuestro equipo revisará tu mensaje y te contactará en las próximas 24-48 horas hábiles.</p>
                <p>Si tienes alguna urgencia, puedes llamarnos directamente.</p>
                <br>
                <p>Saludos cordiales,<br><strong>Equipo de Consultoría</strong></p>
            </div>
            <div class='footer'>
                <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            </div>
        </div>
    </body>
    </html>";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . MAIL_NAME . " <" . MAIL_FROM . ">\r\n";
    
    // Intentar enviar correo con mail() de PHP
    // Nota: En producción se recomienda usar PHPMailer con SMTP
    return @mail($email, $asunto, $cuerpo, $headers);
}
?>
