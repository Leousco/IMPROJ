
<?php
require_once __DIR__ . '/../config/database.php'; 

if (!isset($_GET['token'])) {
    die("Invalid verification link.");
}

$token = $_GET['token'];

// This part may be useless
$stmt = $pdo->prepare("SELECT user_id FROM users WHERE verification_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if ($user) {
    // Update user as verified
    $update = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE user_id = ?");
    $update->execute([$user['user_id']]);

    echo "Email verified successfully. You can now log in.";
} else {
    echo "Invalid or expired verification link.";
}
