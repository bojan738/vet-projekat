<?php
require_once 'db.php';
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        $message = "Lozinke se ne poklapaju.";
    } else {
        // Provera koda i dohvatanje korisnika
        $stmt = $pdo->prepare("SELECT user_id, created_at FROM password_resets_codes WHERE code = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$code]);
        $reset = $stmt->fetch();

        if (!$reset) {
            $message = "Nevažeći kod.";
        } else {
            $created = new DateTime($reset['created_at']);
            $now = new DateTime();
            $diff = $now->getTimestamp() - $created->getTimestamp();
            if ($diff > 900) { // 15 minuta
                $message = "Kod je istekao.";
            } else {
                // Heširanje nove lozinke i update
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $reset['user_id']]);

                // Brisanje koda iz baze
                $stmt = $pdo->prepare("DELETE FROM password_resets_codes WHERE code = ?");
                $stmt->execute([$code]);

                // Slanje potvrde mejlom
                $stmt = $pdo->prepare("SELECT email, first_name FROM users WHERE id = ?");
                $stmt->execute([$reset['user_id']]);
                $user = $stmt->fetch();

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'sandbox.smtp.mailtrap.io';
                    $mail->SMTPAuth = true;
                    $mail->Username = '1a8d7f596b2e99';
                    $mail->Password = 'ee70af4ea947bc'; // koristi App Password za Gmail!
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 2525;

                    $mail->setFrom('noreply@petcare.com', 'PetCare');
                    $mail->addAddress($user['email'], $user['first_name']);

                    $mail->isHTML(true);
                    $mail->Subject = "Potvrda promene lozinke";
                    $mail->Body = "Vasa lozinka je uspesno promenjena.";

                    $mail->send();

                    $message = "Uspesno ste promenili lozinku. <a href='login.php'>Prijavite se</a>";
                } catch (Exception $e) {
                    $message = "Greška pri slanju potvrde mejla: " . $mail->ErrorInfo;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset lozinke</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body class="login-page">

<header>
    <div class="logo">🐾 PetCare</div>
</header>

<main>
    <section class="form-section">
        <h2 class="form-title">Reset lozinke</h2>

        <?php if (!empty($message)): ?>
            <p class="error-message"><?= $message ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="code" class="form-label">Kod sa mejla:</label>
                <input type="text" id="code" name="code" required class="form-input">
            </div>

            <div class="form-group">
                <label for="new_password" class="form-label">Nova lozinka:</label>
                <input type="password" id="new_password" name="new_password" required class="form-input">
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">Potvrdi novu lozinku:</label>
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