<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../services/MailService.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $full_name   = trim($_POST['full_name']);
    $email       = trim($_POST['email']);
    $department  = trim($_POST['department']);
    $employee_id = trim($_POST['employee_id']);
    $password    = $_POST['password'];

    // Basic validation
    if (empty($full_name) || empty($email) || empty($department) || empty($employee_id) || empty($password)) {
        echo json_encode(['success'=>false,'message'=>'All fields are required']);
        exit;
    }

    // Check if email already exists
    $check = $pdo->prepare("SELECT user_id FROM users WHERE email = ? OR employee_id = ?");
    $check->execute([$email, $employee_id]);
    if ($check->rowCount() > 0) {
        echo json_encode(['success'=>false,'message'=>'Email or Employee ID already exists']);
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Generate OTP
    $otp = random_int(100000, 999999);
    $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    // Insert user with OTP
    $stmt = $pdo->prepare("
        INSERT INTO users 
        (full_name, email, department, employee_id, password, otp_code, otp_expires_at)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $full_name,
        $email,
        $department,
        $employee_id,
        $hashed_password,
        $otp,
        $expires
    ]);

    // Send OTP email
    $mailSent = sendOtpEmail($email, $otp);
    if ($mailSent) {
        echo json_encode(['success'=>true,'message'=>'OTP sent! Check your email.']);
    } else {
        echo json_encode(['success'=>false,'message'=>'Failed to send OTP.']);
    }
}

