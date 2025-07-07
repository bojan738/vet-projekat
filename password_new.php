<?php
session_start();
require_once 'db_config.php';
require_once 'functions.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$ordinacija = new VeterinarskaOrdinacija();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (!$code || !$new_password || !$confirm_password) {
        $message = "⚠️ Sva polja su obavezna.";
    } elseif ($new_password !== $confirm_password) {
        $message = "❌ Lozinke se ne poklapaju.";
    } else {
        $resetData = $ordinacija->getResetCodeData($code);

        if (!$resetData) {
            $message = "❌ Nevažeći ili istekao kod.";
        } else {
            $created = new DateTime($resetData['created_at']);
            $now = new DateTime();
            $diff = $now->getTimestamp() - $created->getTimestamp();

            if ($diff > 900) {
                $message = "⏰ Kod je istekao.";
            }
            else {

                $hashed = password_hash($new_password, PASSWORD_BCRYPT);
                $ordinacija->updateUserPassword($resetData['user_id'], $hashed);
                $ordinacija->deleteResetCode($code);


                $user = $ordinacija->getUserById($resetData['user_id']);
                if ($user) {
                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = 'sandbox.smtp.mailtrap.io';
                        $mail->SMTPAuth = true;
                        $mail->Username = '1a8d7f596b2e99';
                        $mail->Password = 'ee70af4ea947bc';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 2525;

                        $mail->setFrom('noreply@petcare.com', 'PetCare');
                        $mail->addAddress($user['email'], $user['first_name']);
                        $mail->isHTML(true);
                        $mail->Subject = "Potvrda promene lozinke";
                        $mail->Body = "Poštovani " . htmlspecialchars($user['first_name']) . ",<br><br>Vaša lozinka je uspešno promenjena.";

                        $mail->send();
                    } catch (Exception $e) {

                    }
                }

                $message = "✅ Lozinka je uspešno promenjena. <a href='login.php'>Prijavite se</a>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Reset lozinke</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body class="login-page">

<header>
    <div class="logo">🐾 PetCare</div>
</header>

<main>
    <section class="form-section">
        <h2 class="form-title">Unesite kod i novu lozinku</h2>

        <?php if (!empty($message)): ?>
            <p class="error-message"><?= $message ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="code" class="form-label">Kod:</label>
                <input type="text" id="code" name="code" required class="form-input">
            </div>

            <div class="form-group">
                <label for="new_password" class="form-label">Nova lozinka:</label>
                <input type="password" id="new_password" name="new_password" required class="form-input">
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">Potvrdi lozinku:</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="form-input">
            </div>

            <button type="submit" class="cta-button">Promeni lozinku</button>
        </form>
    </section>
</main>

<footer class="custom-footer">
    <div class="footer-content">
        &copy; 2025 PetCare Ordinacija. Sva prava zadržana.
    </div>
</footer>

</body>
</html>
