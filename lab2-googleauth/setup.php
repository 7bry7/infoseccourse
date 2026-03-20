<?php
require 'vendor/autoload.php';
$conn = new mysqli("localhost", "root", "", "lab_activity");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$ga = new PHPGangsta_GoogleAuthenticator();
// Fixed: The username is now correctly set to 'bryan'
$user = 'bryan';

$res = $conn->query("SELECT google_auth_secret FROM users WHERE username='$user'");
if (!$res) {
    die("Query failed: " . $conn->error);
}

$row = $res->fetch_assoc();
if (!$row) {
    die("User not found: " . htmlspecialchars($user, ENT_QUOTES, 'UTF-8'));
}

$secret = trim((string)($row['google_auth_secret'] ?? ''));

// Do not regenerate on every page load. Only generate once, or when reset=1 is passed.
if ($secret === '' || isset($_GET['reset'])) {
    $secret = $ga->createSecret();
    if (!$conn->query("UPDATE users SET google_auth_secret='$secret' WHERE username='$user'")) {
        die("Query failed: " . $conn->error);
    }
}

$title = 'InfoSec-Lab';
$otpAuthUri = 'otpauth://totp/' . rawurlencode($user) . '?secret=' . rawurlencode($secret) . '&issuer=' . rawurlencode($title);
$qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?data=' . rawurlencode($otpAuthUri) . '&size=220x220&ecc=M';
$qrCodeFallbackUrl = 'https://chart.googleapis.com/chart?chs=220x220&cht=qr&chl=' . rawurlencode($otpAuthUri);
?>

<!DOCTYPE html>
<html>
<head><title>Setup Google Authenticator</title></head>
<body style="background:#121212; color:white; text-align:center; font-family:sans-serif;">
    <h2>Scan this with Google Authenticator</h2>
    <img src="<?php echo htmlspecialchars($qrCodeUrl, ENT_QUOTES, 'UTF-8'); ?>" onerror="this.onerror=null;this.src='<?php echo htmlspecialchars($qrCodeFallbackUrl, ENT_QUOTES, 'UTF-8'); ?>';" style="border:10px solid white; border-radius:10px;" alt="Google Authenticator QR code">
    <p>Secret Key: <?php echo htmlspecialchars($secret, ENT_QUOTES, 'UTF-8'); ?></p>
    <p style="font-size:0.9em;color:#ccc;">If QR does not load, type the Secret Key manually in your Authenticator app.</p>
    <p style="font-size:0.85em;color:#aaa;">Need a new secret? Open setup.php?reset=1</p>
    <br>
    <a href="index.php" style="color:#28a745;">Go to Login Page</a>
</body>
</html>