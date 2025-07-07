<?php
session_start();
require_once 'auth.php';
requireVeterinarian();
require_once 'db_config.php';
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

$vetId = $_SESSION['vet_id'];

$db = new DBConfig();
$pdo = $db->getConnection();
$ordinacija = new VeterinarskaOrdinacija($pdo);

$vetInfo = $ordinacija->getVetInfo($vetId);
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <title>Profil veterinara</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="vet_treatments_info.php">Tretmani</a></li>
            <li><a href="vet_electronic_card.php" >Karton</a></li>
            <li><a href="vet_profile.php" class="active">Vet profil</a></li>
            <li><a href="vet_schedule.php">Radno vreme</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<?php if (isset($_SESSION['msg'])): ?>
    <div class="msg-box"><?= htmlspecialchars($_SESSION['msg']); unset($_SESSION['msg']); ?></div>
<?php endif; ?>

<main style="max-width:900px; margin:30px auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 6px 15px rgba(0,0,0,0.1);">

    <section style="margin-bottom:40px;">
        <h2 style="text-align:center;">Profil veterinara</h2>
        <div style="display:flex; align-items:flex-start; gap:30px; max-width: 700px; margin: 0 auto;">
            <img src="<?= 'images/veterinarians/' . htmlspecialchars($vetInfo['photo'] ?? 'avatar.jpg') ?>" alt="Veterinar"
                 style="width:180px; height:180px; object-fit:cover; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.15);">

            <div style="display:flex; flex-direction:column; justify-content:flex-start; gap:12px; font-size: 1.1rem; color:#333; text-align:left;">
                <p><strong>Ime:</strong> <?= htmlspecialchars($vetInfo['first_name'] ?? '') ?></p>
                <p><strong>Prezime:</strong> <?= htmlspecialchars($vetInfo['last_name'] ?? '') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($vetInfo['email'] ?? '') ?></p>
                <p><strong>Telefon:</strong> <?= htmlspecialchars($vetInfo['phone_number'] ?? '') ?></p>
                <p><strong>Specijalizacija:</strong> <?= htmlspecialchars($vetInfo['specialization'] ?? 'N/A') ?></p>
                <p><strong>Licenca:</strong> <?= htmlspecialchars($vetInfo['license_number'] ?? 'N/A') ?></p>
            </div>
        </div>
    </section>
</main>
</body>
</html>
