<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../services/MailService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /IMPROJ/views/signup.php");
    exit;
}

$action = $_POST['action'] ?? '';

// REGISTER
if ($action === 'register') {

    $full_name   = trim($_POST['full_name']);
    $email       = trim($_POST['email']);
    $department  = trim($_POST['department']);
    $employee_id = trim($_POST['employee_id']);
    $password    = $_POST['password'] ?? '';
    $password_confirmation = $_POST['password_confirmation'] ?? '';

    if (empty($full_name) || empty($email) || empty($department) || empty($employee_id) || empty($password) || empty($password_confirmation)) {
        header("Location: /IMPROJ/views/signup.php?error=empty");
        exit;
    }

    if ($password !== $password_confirmation) {
        header("Location: /IMPROJ/views/signup.php?error=password_mismatch");
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Generate OTP
    $otp = random_int(100000, 999999);
    $otpHashed = password_hash($otp, PASSWORD_DEFAULT);
    $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    // Check if user exists
    $check = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $check->execute([$email]);
    $existingUser = $check->fetch();

    if ($existingUser) {
        if ($existingUser['is_verified'] == 0) {
            $update = $pdo->prepare("
                UPDATE users 
                SET full_name = ?, department = ?, employee_id = ?, password = ?, otp_code = ?, otp_expires_at = ? 
                WHERE email = ?
            ");
            $update->execute([$full_name, $department, $employee_id, $hashed_password, $otpHashed, $expires, $email]);

            $_SESSION['verify_email'] = $email;
            sendOtpEmail($email, $otp);
            header("Location: /IMPROJ/views/verify_email.php");
            exit;
        } else {
            // Email already verified
            header("Location: /IMPROJ/views/signup.php?error=exists");
            exit;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO users
        (full_name, email, department, employee_id, password, otp_code, otp_expires_at, is_verified)
        VALUES (?, ?, ?, ?, ?, ?, ?, 0)
    ");
    $stmt->execute([$full_name, $email, $department, $employee_id, $hashed_password, $otpHashed, $expires]);

    $_SESSION['verify_email'] = $email;
    sendOtpEmail($email, $otp);

    header("Location: /IMPROJ/views/verify_email.php");
    exit;
}


// VERIFY OTP
if ($action === 'verify_otp') {

    if (!isset($_SESSION['verify_email'])) {
        header("Location: /IMPROJ/views/login.php");
        exit;
    }

    $email = $_SESSION['verify_email'];
    $otpInput = trim($_POST['otp']);

    $stmt = $pdo->prepare("SELECT otp_code, otp_expires_at FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();


    if ($user &&
        password_verify($otpInput, $user['otp_code']) &&
        strtotime($user['otp_expires_at']) > time()) {

        $update = $pdo->prepare("
            UPDATE users
            SET is_verified = 1, otp_code = NULL, otp_expires_at = NULL
            WHERE email = ?
        ");
        $update->execute([$email]);

        unset($_SESSION['verify_email']);
        header("Location: /IMPROJ/views/login.php?verified=1");
        exit;
    }

    header("Location: /IMPROJ/views/verify_email.php?error=invalid");
    exit;
}

// RESEND OTP
if ($action === 'resend_otp') {

    if (!isset($_SESSION['verify_email'])) {
        header("Location: /IMPROJ/views/login.php");
        exit;
    }

    $email = $_SESSION['verify_email'];

    $otp = random_int(100000, 999999);
    $otpHashed = password_hash($otp, PASSWORD_DEFAULT);
    $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    $stmt = $pdo->prepare("
        UPDATE users
        SET otp_code = ?, otp_expires_at = ?
        WHERE email = ?
    ");
    $stmt->execute([$otpHashed, $expires, $email]);

    sendOtpEmail($email, $otp);

    header("Location: /IMPROJ/views/verify_email.php?resent=1");
    exit;
}
