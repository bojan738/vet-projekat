<?php
session_start();
require_once 'auth.php';
requireRegularUser();
require_once 'db_config.php';
require_once 'functions.php';

$ordinacija = new VeterinarskaOrdinacija();

$pet_id = isset($_GET['pet_id']) ? (int)$_GET['pet_id'] : 0;



$records = $ordinacija->getMedicalRecordById1($pet_id);
$editing = isset($_GET['edit']) ? $ordinacija->get_medical_record_by_id($_GET['edit']) : null;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Medicinski kartoni</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="pet_information.php" class="active">Moj ljubimac</a></li>
            <li><a href="pet_treatments.php">Tretman ljubimca</a></li>
            <li><a href="users_reservation.php">Rezervacije</a></li>
            <li><a href="change_reservation.php">Promeni rezervaciju</a></li>
            <li><a href="user_information.php">Moj nalog</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<main class="container">
    <h2><?= $editing ? 'Izmena zapisa' : 'Pregled medicinskih zapisa' ?></h2>

    <?php if ($editing): ?>
        <form method="post" class="form-inline" style="margin-bottom:20px;">
            <input type="hidden" name="update_id" value="<?= $editing['id'] ?>">
            <input type="text" name="diagnosis" placeholder="Dijagnoza" value="<?= htmlspecialchars($editing['diagnosis']) ?>" required>
            <input type="text" name="treatment" placeholder="Tretman" value="<?= htmlspecialchars($editing['treatment']) ?>" required>
            <input type="number" step="0.01" name="price" placeholder="Cena" value="<?= htmlspecialchars($editing['price']) ?>" required>
            <button type="submit">💾 Sačuvaj izmene</button>
        </form>
    <?php endif; ?>

    <h3>Lista medicinskih kartona</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>Ljubimac</th>
            <th>Veterinar</th>
            <th>Dijagnoza</th>
            <th>Tretman</th>
            <th>Cena</th>
            <th>Datum</th>

        </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['pet_name']) ?></td>
                <td><?= htmlspecialchars($r['vet_first_name'] . ' ' . $r['vet_last_name']) ?></td>
                <td><?= htmlspecialchars($r['diagnosis']) ?></td>
                <td><?= htmlspecialchars($r['treatment']) ?></td>
                <td><?= number_format($r['price'], 2) ?> RSD</td>
                <td><?= htmlspecialchars($r['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>
