<?php
session_start();
//echo "PHP OK";
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($dest, $object, $text){
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply.pigeu@gmail.com'; // La tua email Gmail
        $mail->Password   = 'ogikylhixedyyrwg';       // La password della tua email
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('noreply.pigeu@gmail.com', 'PiGEU web services');
        $mail->addAddress($dest, 'Utente');

        $mail->isHTML(true);
        $mail->Subject = $object;
        $mail->Body    = $text;

        $mail->send();
        echo 'Email inviata con successo.';
    } catch (Exception $e) {
        echo "Si è verificato un errore durante l'invio dell'email: {$mail->ErrorInfo}";
    }
}


?>
