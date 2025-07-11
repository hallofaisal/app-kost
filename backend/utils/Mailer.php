<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class Mailer {
    private $mail;
    public function __construct() {
        $this->mail = new PHPMailer(true);
        // Konfigurasi SMTP
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.example.com'; // Ganti dengan host SMTP
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'user@example.com'; // Ganti dengan username
        $this->mail->Password = 'password'; // Ganti dengan password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
        $this->mail->setFrom('noreply@kos.com', 'Kos Modern');
    }

    public function send($to, $subject, $body) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Mailer Error: ' . $e->getMessage());
            return false;
        }
    }
} 