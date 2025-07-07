<?php
require_once 'db_config.php';
require_once 'functions.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$ordinacija = new VeterinarskaOrdinacija();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $user = $ordinacija->getUserByEmail($email);

    if (!$user) {
        $message = "❌ Korisnik sa ovom e-mail adresom ne postoji.";
    } else {
        $code = strval(random_int(100000, 999999));
        $ordinacija->storeResetCode($user['id'], $code);

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = 'f9cd07efd4a868';
            $mail->Password = 'f4d0acd5a04c9b';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 2525;

            $mail->setFrom('noreply@petcare.com', 'PetCare');
            $mail->addAddress($email, $user['first_name']);

            $mail->isHTML(true);
            $mail->Subject = "Kod za reset lozinke";
            $mail->Body = "Zdravo " . htmlspecialchars($user['first_name']) . ",<br><br>" .
                "Vas kod za reset lozinke je: <strong>$code</strong><br><br>" .
                "Kod vazi 15 minuta.";

            $mail->send();
            $message = "✅ Kod za reset lozinke je poslat na vašu e-mail adresu.";
        } catch (Exception $e) {
            $message = "Greška pri slanju mejla: " . $mail->ErrorInfo;
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Zaboravljena lozinka</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body class="login-page">

<header>
    <div class="logo">🐾 PetCare</div>
</header>

<main>
    <section class="form-section">
        <h2 class="form-title">Zaboravljena lozinka</h2>

        <?php if (!empty($message)): ?>
            <p class="error-message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email" class="form-label">Unesite vašu e-mail adresu:</label>
                <input type="email" id="email" name="email" required class="form-input">
            </div>

            <button type="submit" class="cta-button">Pošalji kod</button>
        </form>

        <p class="register-link"><a href="password_new.php">Imate kod? Resetujte lozinku</a></p>
        <p class="register-link"><a href="login.php">Nazad na prijavu</a></p>
    </section>
</main>

<footer class="custom-footer">
    <div class="footer-content">
        &copy; 2025 PetCare Ordinacija. Sva prava zadržana.
    </div>
</footer>

</body>
</html>
