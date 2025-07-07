<?php
session_start();
require_once 'db_config.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PasswordManager {
    private $pdo;

    public function __construct() {
        $db = new DBConfig();
        $this->pdo = $db->getConnection();
    }

    public function requestPasswordChange($userId, $currentPassword, $newPassword, $confirmPassword) {
        if ($newPassword !== $confirmPassword) {
            throw new Exception("Lozinke se ne poklapaju.");
        }

        $stmt = $this->pdo->prepare("SELECT password, email, first_name FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("Korisnik nije pronađen.");
        }

        if (!password_verify($currentPassword, $user['password'])) {
            throw new Exception("Trenutna lozinka nije tačna.");
        }

        $token = bin2hex(random_bytes(16));
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("INSERT INTO password_resets (user_id, new_password, token, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$userId, $hashedPassword, $token]);

        $this->sendConfirmationEmail($user['email'], $user['first_name'], $token);
    }

    private function sendConfirmationEmail($email, $firstName, $token) {
        $activationLink = "http://localhost/VetProjekat/confirm_password_change.php?token=$token";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = 'f9cd07efd4a868';
            $mail->Password = 'f4d0acd5a04c9b';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 2525;

            $mail->setFrom('noreply@petcare.com', 'PetCare');
            $mail->addAddress($email, $firstName);

            $mail->isHTML(true);
            $mail->Subject = "Potvrda promene lozinke";
            $mail->Body = "Zdravo " . htmlspecialchars($firstName) . ",<br><br>" .
                "Kliknite na link da potvrdite promenu lozinke:<br>" .
                "<a href='$activationLink'>$activationLink</a><br><br>" .
                "Ako niste vi zahtevali ovu promenu, zanemarite ovaj mejl.";

            $mail->send();
        } catch (Exception $e) {
            throw new Exception("Greška pri slanju mejla: " . $mail->ErrorInfo);
        }
    }
}

// MAIN
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

try {
    $manager = new PasswordManager();
    $manager->requestPasswordChange(
        $_SESSION['user_id'],
        $_POST['current_password'] ?? '',
        $_POST['new_password'] ?? '',
        $_POST['confirm_password'] ?? ''
    );

    echo <<<HTML
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Obaveštenje</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="alert alert-success text-center" role="alert" style="font-size: 18px;">
        ✅ Poslali smo vam mejl sa linkom za potvrdu promene lozinke. <br>📬 Proverite svoj inbox.
    </div>
</div>
</body>
</html>
HTML;
} catch (Exception $ex) {
    echo '<div class="alert alert-danger text-center mt-4" style="font-size:18px;">❌ ' . $ex->getMessage() . '</div>';
}
?>
