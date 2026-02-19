<?php
session_start();
require_once __DIR__ . '/../config/database.php'; // defines $pdo

$pdo = $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /IMPROJ/views/login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Check for empty fields
if (empty($email) || empty($password)) {
    header("Location: /IMPROJ/views/login.php?error=empty");
    exit;
}

// Fetch user by email (Oracle column names are uppercase)
$stmt = $pdo->prepare("SELECT * FROM USERS WHERE EMAIL = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// User not found or password incorrect
if (!$user || !password_verify($password, $user['PASSWORD'])) {
    header("Location: /IMPROJ/views/login.php?error=invalid");
    exit;
}

// User exists but not verified
if ($user['IS_VERIFIED'] == 0) {
    $_SESSION['verify_email'] = $email;
    header("Location: /IMPROJ/views/verify_email.php?error=unverified");
    exit;
}

// Successful login
$_SESSION['user_id']   = $user['USER_ID'];
$_SESSION['full_name'] = $user['FULL_NAME'];

header("Location: /IMPROJ/views/landing_page.php?login=success");
exit;
