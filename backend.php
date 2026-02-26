<?php
session_start();
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

$conn = new mysqli("localhost", "root", "", "lab_activity");
$action = $_POST['action'] ?? '';

// --- LOGIN BLOCK ---
if ($action == 'login') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$user' AND password='$pass'");

    if ($result->num_rows > 0) {
        $otp = rand(100000, 999999);
        $conn->query("UPDATE users SET otp='$otp' WHERE username='$user'");
        $_SESSION['temp_user'] = $user;

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io'; 
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '54add78d61b142'; 
        $mail->Password = 'eb353413d3a51f'; 

        $mail->setFrom('system@plp.edu.ph', 'Information Security Lab');
        $mail->addAddress('bryanbermudez56@gmail.com');
        $mail->Subject = 'Your Security OTP Code';
        $mail->Body    = "Your verification code is: $otp";

        if($mail->send()) {
            echo json_encode(['status' => 'success', 'message' => 'OTP sent to email.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Mail Error: ' . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Username or Password!']);
    }
} // <--- This curly brace closes the Login block correctly

// --- VERIFY BLOCK (Must be outside the Login block) ---
if ($action == 'verify') {
    $input_otp = $_POST['otp'];
    $user = $_SESSION['temp_user'];

    $result = $conn->query("SELECT * FROM users WHERE username='$user' AND otp='$input_otp'");

    if ($result->num_rows > 0) {
        $_SESSION['authenticated'] = $user;
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid OTP!']);
    }
}
?>