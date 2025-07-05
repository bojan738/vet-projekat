<?php
session_start();
require_once 'auth.php';
requireRegularUser();
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user = get_user_profile($pdo, $user_id);
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';

    update_user_profile($pdo, $user_id, $first_name, $last_name, $email, $phone);
    $user = get_user_profile($pdo, $user_id);
    $success_message = "Podaci su uspešno ažurirani.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profil korisnika</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body class="login-page">

<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="pet_information.php">Moj ljubimac</a></li>
            <li><a href="pet_treatments.php">Tretman ljubimca</a></li>
            <li><a href="users_reservation.php">Rezervacije</a></li>
            <li><a href="change_reservation.php">Promeni rezervaciju</a></li>
            <li><a href="user_information.php" class="active">Moj nalog</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="form-section">
        <h2 class="form-title">Podaci o korisniku</h2>

        <?php if ($success_message): ?>
            <script>alert("<?= $success_message ?>");</script>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="first_name" class="form-label">Ime:</label>
                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" class="form-input">
            </div>

            <div class="form-group">
                <label for="last_name" class="form-label">Prezime:</label>
                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" class="form-input">
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-input">
            </div>

            <p class="register-link"><a href="password_reset.php">Promeni lozinku</a></p>

            <div class="form-group">
                <label for="phone" class="form-label">Broj telefona:</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone_number']) ?>" class="form-input">
            </div>

            <button type="submit" class="cta-button">Izmeni</button>
        </form>
    </section>
</main>

</body>
<footer class="custom-footer">
    <div class="footer-content">
        &copy; 2025 PetCare Ordinacija. Sva prava zadržana.
    </div>
</footer>
</html>
