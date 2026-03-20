<?php
session_start();
require 'vendor/autoload.php';
// use PHPMailer\PHPMailer\PHPMailer;

$conn = new mysqli("localhost", "root", "", "lab_activity");

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

$action = $_POST['action'] ?? '';

// --- LOGIN BLOCK ---
if ($action == 'login') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$user' AND password='$pass'");

    if ($result->num_rows > 0){
        $_SESSION['temp_user'] = $user;
        echo json_encode(['status' => 'success', 'message' => 'Login successful. Please enter your OTP.']);
    }else{
        echo json_encode(['status' => 'error', 'message' => 'Invalid Username or Password!']);
    }

    // if ($result->num_rows > 0) {
    //     $otp = rand(100000, 999999);
    //     $conn->query("UPDATE users SET otp='$otp' WHERE username='$user'");
    //     $_SESSION['temp_user'] = $user;

    //     $mail = new PHPMailer();
    //     $mail->isSMTP();
    //     $mail->Host = 'sandbox.smtp.mailtrap.io'; 
    //     $mail->SMTPAuth = true;
    //     $mail->Port = 2525;
    //     $mail->Username = '54add78d61b142'; 
    //     $mail->Password = 'eb353413d3a51f'; 

    //     $mail->setFrom('system@plp.edu.ph', 'Information Security Lab');
    //     $mail->addAddress('bryanbermudez56@gmail.com');
    //     $mail->Subject = 'Your Security OTP Code';
    //     $mail->Body    = "Your verification code is: $otp";

    //     if($mail->send()) {
    //         echo json_encode(['status' => 'success', 'message' => 'OTP sent to email.']);
    //     } else {
    //         error_log('PHPMailer send failed: ' . $mail->ErrorInfo);
    //         echo json_encode([
    //             'status' => 'error',
    //             'message' => 'We could not send your OTP right now. Please try again in a moment.'
    //         ]);
    //     }
    // } else {
    //     echo json_encode(['status' => 'error', 'message' => 'Invalid Username or Password!']);
    // }
}


// --- VERIFY BLOCK
if ($action == 'verify') {
    $input_otp = preg_replace('/\D/', '', (string)($_POST['otp'] ?? ''));
    $user = $_SESSION['temp_user'] ?? '';

    if (empty($user)) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired. Please login again.']);
        exit;
    }

    $res = $conn -> query("SELECT google_auth_secret FROM users WHERE username='$user'");
    
    if (!$res) {
        echo json_encode(['status' => 'error', 'message' => 'Database query failed: ' . $conn->error]);
        exit;
    }
    
    $row = $res->fetch_assoc();
    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        exit;
    }
    
    $secret = $row['google_auth_secret'];

    $g = new PHPGangsta_GoogleAuthenticator();

    if (strlen($input_otp) !== 6) {
        echo json_encode(['status' => 'error', 'message' => 'OTP must be 6 digits.']);
        exit;
    }

    if($g->verifyCode($secret, $input_otp, 2)){
        $_SESSION['authenticated'] = $user;
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid OTP!']);
    }
    // $result = $conn->query("SELECT * FROM users WHERE username='$user' AND otp='$input_otp'");

    // if ($result->num_rows > 0) {
    //     $_SESSION['authenticated'] = $user;
    //     echo json_encode(['status' => 'success']);
    // } else {
    //     echo json_encode(['status' => 'error', 'message' => 'Invalid OTP!']);
    // }
}
?>
