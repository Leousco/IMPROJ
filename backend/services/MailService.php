
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

require_once __DIR__ . '/../../vendor/autoload.php';

function sendOtpEmail($email, $otp) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '1qcuassetmgm@gmail.com';
        $mail->Password   = 'vtmw dnnc nfxr gsek';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('1qcuassetmgm@gmail.com', 'QCU Asset Management');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for QCU Asset Management';
        $mail->Body    = "
            <h3>OTP Verification</h3>
            <p>Your OTP code is: <strong>$otp</strong></p>
            <p>This OTP is valid for 10 minutes.</p>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Mail error: " . $mail->ErrorInfo);
        return false;
    }
}
