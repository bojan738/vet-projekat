<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['pet_id'])) {
    die("Nedostaje ID ljubimca.");
}

$pet_id = (int)$_GET['pet_id'];
$vet_id = $_SESSION['vet_id'];

// Dobavljanje detaljnih podataka o ljubimcu sa vlasnikom
$pet = get_pet_full_info($pdo, $pet_id);
if (!$pet) {
    die("Ljubimac nije pronađen.");
}

// Dobavljanje istorije bolesti (beleški) za ljubimca i veterinara
$records = get_medical_history_for_vet($pdo, $pet_id, $vet_id);

// Slika ljubimca
$photoDir = 'images/pets/';
$defaultPhoto = 'images/default.jpg';
$imgSrc = $defaultPhoto;
if (!empty($pet['photo'])) {
    $relativePath = $photoDir . $pet['photo'];
    $absolutePath = __DIR__ . '/' . $relativePath;
    if (file_exists($absolutePath)) {
        $imgSrc = $relativePath;
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <title>Karton ljubimca</title>
    <link rel="stylesheet" href="css/css.css" />
    <style>
        .container { display: flex; gap: 20px; padding: 20px; }
        .left { width: 25%; }
        .left img { width: 100%; border-radius: 10px; }
        .right { width: 75%; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="vet_treatments_info.php">Tretmani</a></li>
            <li><a href="vet_electronic_card.php" class="active">Karton</a></li>
            <li><a href="vet_profile.php">Vet profil</a></li>
            <li><a href="vet_schedule.php">Radno vreme</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>
<h2>Karton ljubimca: <?= htmlspecialchars($pet['name']) ?></h2>

<div class="container">
    <div class="left">
        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="Slika ljubimca" />
        <p><strong>Pol:</strong> <?= htmlspecialchars($pet['gender']) ?></p>
        <p><strong>Godine:</strong> <?= htmlspecialchars($pet['age']) ?></p>
        <p><strong>Datum rođenja:</strong> <?= htmlspecialchars($pet['birth_date']) ?></p>
        <p><strong>Vrsta:</strong> <?= htmlspecialchars($pet['type_name']) ?></p>
        <p><strong>Rasa:</strong> <?= htmlspecialchars($pet['breed_name']) ?></p>
        <p><strong>Vlasnik:</strong> <?= htmlspecialchars($pet['owner_first_name'] . ' ' . $pet['owner_last_name']) ?></p>
    </div>

    <div class="right">
        <h3>Istorija bolesti</h3>
        <?php if ($records): ?>
            <table>
                <thead>
                <tr>
                    <th>Datum</th>
                    <th>Veterinar</th>
                    <th>Dijagnoza</th>
                    <th>Terapija</th>
                    <th>Cena</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($records as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['created_at']) ?></td>
                        <td><?= htmlspecialchars($r['vet_name']) ?></td>
                        <td><?= htmlspecialchars($r['diagnosis']) ?></td>
                        <td><?= htmlspecialchars($r['treatment']) ?></td>
                        <td><?= number_format($r['price'], 2) ?> RSD</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Još nema beleški za ovog ljubimca.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
