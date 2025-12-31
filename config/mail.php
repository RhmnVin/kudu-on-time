<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/* =========================================
   LOAD PHPMailer (PATH BENAR & REAL)
========================================= */
require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';

/* =========================================
   FUNCTION SEND EMAIL
========================================= */
function sendEmail($to, $subject, $message)
{
    $mail = new PHPMailer(true);

    try {
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // aktifkan jika debug

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'EMAIL_KAMU@gmail.com';     // GANTI
        $mail->Password   = 'APP_PASSWORD_GMAIL';       // GANTI
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('EMAIL_KAMU@gmail.com', 'Reminder Akademik');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        return $mail->send();

    } catch (Exception $e) {
        error_log('MAIL ERROR: '.$mail->ErrorInfo);
        return false;
    }
}
