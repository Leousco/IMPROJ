<?php
session_start();
require_once __DIR__ . '/../config/database.php';

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

// Fetch user by email
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

// User not found or password incorrect
if (!$user || !password_verify($password, $user['password'])) {
    header("Location: /IMPROJ/views/login.php?error=invalid");
    exit;
}

// User exists but not verified
if ($user['is_verified'] == 0) {
    $_SESSION['verify_email'] = $email;
    header("Location: /IMPROJ/views/verify_email.php?error=unverified");
    exit;
}

// Successful login
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['full_name'] = $user['full_name'];

header("Location: /IMPROJ/views/landing_page.php?login=success");
exit;
