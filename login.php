<?php
session_start();
require_once 'db_config.php';
require_once 'functions.php';

$ordinacija = new VeterinarskaOrdinacija();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['username']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Molimo unesite validnu email adresu kao korisničko ime.";
    } else {
        $role = $ordinacija->loginUser($email, $password);

        if ($role === 'not_active') {
            $message = "⚠️ Vaš nalog nije aktiviran. Proverite svoj e-mail i kliknite na aktivacioni link.";
        } elseif ($role === 1) {
            header("Location: manage_users.php");
            exit;
        } elseif ($role === 2 && isset($_SESSION['vet_id'])) {
            header("Location: vet_profile.php");
            exit;
        } elseif ($role === 3) {
            header("Location: pet_information.php");
            exit;
        } else {
            $message = "Pogrešan email ili lozinka ili nedozvoljena uloga.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Prijava - PetCare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/css.css">
</head>
<body>

<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="index.php">Početna</a></li>
            <li><a href="vets.php">Veterinari</a></li>
            <li><a href="login.php" class="active">Prijava</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="form-section">
        <h2 class="form-title">Prijava</h2>
        <form method="post" class="login-form">
            <div class="form-group">
                <label for="username" class="form-label">Korisničko ime</label>
                <input type="text" id="username" name="username" required class="form-input">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Lozinka</label>
                <input type="password" id="password" name="password" required class="form-input">
            </div>

            <button type="submit" class="cta-button">Prijavi se</button>

            <p class="register-link">
                Nemate nalog? <a href="register.php">Registrujte se</a>
            </p>
            <p class="register-link">
                <a href="password_forgot.php">Zaboravljena lozinka</a>
            </p>
        </form>

        <?php if (!empty($message)): ?>
            <script>alert("<?= htmlspecialchars($message) ?>");</script>
        <?php endif; ?>
    </section>
</main>

<footer class="custom-footer">
    <div class="footer-content">
        &copy; 2025 PetCare Ordinacija. Sva prava zadržana.
    </div>
</footer>
<script src="js/login_validation.js"></script>


</body>
</html>

