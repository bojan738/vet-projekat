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

// Pet info
$pet = get_pet_with_owner($pdo, $pet_id);
if (!$pet) {
    die("Ljubimac nije pronađen.");
}

// Medical history
$records = get_medical_history_for_vet($pdo, $pet_id, $vet_id);

// Slika
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
<html>
<head>
    <title>Karton ljubimca</title>
    <link rel="stylesheet" href="css/css.css">
    <style>
        .container { display: flex; gap: 20px; padding: 20px; }
        .left { width: 25%; }
        .left img { width: 100%; border-radius: 10px; }
        .right { width: 75%; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
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
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<h2>Karton ljubimca: <?= htmlspecialchars($pet['name']) ?></h2>

<div class="container">
    <div class="left">
        <img src="<?= $imgSrc ?>" alt="Slika ljubimca" width="100%">
    </div>
    <div class="right">
        <p><strong>Pol:</strong> <?= htmlspecialchars($pet['gender']) ?></p>
        <p><strong>Datum rođenja:</strong> <?= htmlspecialchars($pet['birth_date']) ?></p>
        <p><strong>Vlasnik:</strong> <?= htmlspecialchars($pet['owner_name']) ?></p>

        <h3>Dodaj novu belešku</h3>
        <form method="post" action="vet_save_note.php">
            <input type="hidden" name="pet_id" value="<?= $pet_id ?>">
            <label>Dijagnoza:</label><br>
            <textarea name="diagnosis" rows="3" required></textarea><br>

            <label>Terapija:</label><br>
            <textarea name="treatment" rows="3" required></textarea><br>

            <label>Cena (RSD):</label><br>
            <input type="number" name="price" step="0.01" required><br><br>

            <button type="submit">Sačuvaj belešku</button>
        </form>

        <h3>Istorija bolesti</h3>
        <?php if ($records): ?>
            <table>
                <thead>
                <tr>
                    <th>Datum</th>
                    <th>Veterinar</th>
                    <th>Dijagnoza</th>
                    <th>Tretman</th>
                    <th>Cena</th>
                    <th>Akcije</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($records as $r): ?>
                    <tr>
                        <td><?= date('Y-m-d H:i', strtotime($r['created_at'])) ?></td>
                        <td><?= htmlspecialchars($r['vet_name']) ?></td>
                        <td><?= htmlspecialchars($r['diagnosis']) ?></td>
                        <td><?= htmlspecialchars($r['treatment']) ?></td>
                        <td><?= number_format($r['price'], 2) ?> RSD</td>
                        <td>
                            <?php if ($r['vet_id'] == $_SESSION['vet_id']): ?>
                                <a href="vet_edit_record.php?id=<?= $r['id'] ?>">Izmeni</a> |
                                <a href="vet_delete_record.php?id=<?= $r['id'] ?>&pet_id=<?= $pet_id ?>" onclick="return confirm('Obrisati ovu belešku?')">Obriši</a>
                            <?php else: ?>
                                <span style="color: gray;">Nije vaša beleška</span>
                            <?php endif; ?>
                        </td>
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
