<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

$vetId = $_SESSION['vet_id'];
$pets = get_vet_pets($pdo, $vetId);

// Podešavanje foldera za slike
$photoDir = 'images/pets/';
$defaultPhoto = 'images/default.jpg';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Elektronski kartoni</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="vet_treatments_info.php">Tretmani</a></li>
            <li><a href="vet_electronic_card.php" class="active">Karton</a></li>
            <li><a href="vet_profile.php">Vet profil</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<h2>Elektronski kartoni ljubimaca</h2>
<section class="vets-section">
    <div class="vet-cards">


        <?php foreach ($pets as $pet): ?>
            <?php
            $photoFile = $pet['photo'] ?? '';
            $imgSrc = (!empty($photoFile) && file_exists(__DIR__ . "/images/pets/" . $photoFile))
                ? "images/pets/" . $photoFile
                : "images/default.jpg";
            ?>
            <div class="vet-card">
                <a href="vet_medical_record.php?pet_id=<?= $pet['pet_id'] ?>">
                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="Slika ljubimca" width="100" height="100">
                </a>
                <h3><?= htmlspecialchars($pet['name']) ?></h3>
                <p><strong>Vlasnik:</strong> <?= htmlspecialchars($pet['owner_name']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>
</body>
</html>
