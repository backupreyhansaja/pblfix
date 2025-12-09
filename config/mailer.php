<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

function sendMailSMTP($to, $subject, $htmlBody, $fromName = "Admin", $fromEmail = "noreply@yourdomain.com") {
    $mail = new PHPMailer(true);

    try {
        // SMTP SETTINGS
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';      // SMTP Server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'rahmattahaluqq@gmail.com';   // Ganti emailmu
        $mail->Password   = 'chuk adcx aqcc skye';        // App Password Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // FROM
        $mail->setFrom($fromEmail, $fromName);

        // TO
        $mail->addAddress($to);

        // CONTENT
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
