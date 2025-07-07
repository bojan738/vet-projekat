<?php
session_start();
require_once 'db_config.php';
require_once 'functions.php';

$message = '';

$ordinacija = new VeterinarskaOrdinacija();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Email adresa nije validna.";
    } elseif (!$address) {
        $message = "Adresa je obavezna.";
    } elseif ($phone !== '' && !preg_match('/^[0-9]+$/', $phone)) {
        $message = "Broj telefona može sadržati samo cifre.";
    } elseif ($password !== $confirm) {
        $message = "Lozinke se ne poklapaju.";
    } else {
        $result = $ordinacija->registerUser($first, $last, $email, $phone, $address, $password);
        if ($result === true) {
            header("Location: login.php?registered=true");
            exit;
        } else {
            $message = $result;
        }
    }
}



?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Registracija - PetCare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <h2 class="form-title">Registracija</h2>
        <form method="POST" class="login-form">
            <label class="form-label">Ime</label>
            <input type="text" name="first_name" class="form-input" required>

            <label class="form-label">Prezime</label>
            <input type="text" name="last_name" class="form-input" required>

            <label class="form-label">Email adresa</label>
            <input type="email" name="email" class="form-input" required>

            <label class="form-label">Broj telefona</label>
            <input type="text" name="phone" class="form-input">

            <label class="form-label">Adresa</label>
            <textarea name="address" class="form-input" rows="3"></textarea>

            <label class="form-label">Lozinka</label>
            <input type="password" name="password" class="form-input" required>

            <label class="form-label">Potvrda lozinke</label>
            <input type="password" name="confirm_password" class="form-input" required>
            <br><br>
            <button type="submit" class="cta-button">Registruj se</button>

            <p class="register-link">
                Već imate nalog? <a href="login.php">Prijavite se</a>
            </p>
        </form>
        <?php if (!empty($message)): ?>
            <script>alert("<?= htmlspecialchars($message) ?>");</script>
        <?php endif; ?>
    </section>
</main>

<footer class="custom-footer" style="padding: 10px; text-align: center;">
    <div class="footer-content" style="padding: 10px;">
        &copy; 2025 PetCare Ordinacija. Sva prava zadržana.
    </div>
</footer>
<script src="js/registration_validation.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
