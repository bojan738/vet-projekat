<?php
require_once 'db.php';
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    $stmt = $pdo->prepare("SELECT id, first_name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $message = "Korisnik sa ovom e-mail adresom ne postoji.";
    } else {
        $code = random_int(100000, 999999);

        $stmt = $pdo->prepare("INSERT INTO password_resets_codes (user_id, code, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$user['id'], $code]);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = '1a8d7f596b2e99';
            $mail->Password = 'ee70af4ea947bc';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 2525;

            $mail->setFrom('noreply@petcare.com', 'PetCare');
            $mail->addAddress($email, $user['first_name']);

            $mail->isHTML(true);
            $mail->Subject = "Kod za reset lozinke";
            $mail->Body = "Vas kod za reset lozinke je: <strong>$code</strong>";

            $mail->send();

            $message = "Kod je poslat na vašu e-mail adresu.";
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
        <p class="register-link"><a href="user_information.php">Nazad</a></p>
    </section>
</main>

<footer class="custom-footer">
    <div class="footer-content">
        &copy; 2025 PetCare Ordinacija. Sva prava zadržana.
    </div>
</footer>

</body>
</html>