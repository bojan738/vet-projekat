<?php
session_start();
require_once 'auth.php';
requireAdmin();
require_once 'db_config.php';
require_once 'functions.php';

$ordinacija = new VeterinarskaOrdinacija();


$filter_date = $_GET['date'] ?? null;
$appointments = $filter_date
    ? $ordinacija->getAppointmentsByDate($filter_date)
    : $ordinacija->getAllAppointments1();

$editing = isset($_GET['edit']) ? $ordinacija->getAppointmentById2($_GET['edit']) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Termini</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="manage_services.php">Upravljanje servisa</a></li>
            <li><a href="manage_vets.php"  >Upravljanje veterinarima</a></li>
            <li><a href="manage_term.php" class="active">Upravljanje terminima</a></li>
            <li><a href="manage_users.php">Upravljanje korisnicima</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<div style="padding: 0 20px;">
    <h2>Filter po datumu</h2>
    <form method="get">
        <input type="date" name="date" value="<?= htmlspecialchars($filter_date ?? '') ?>">
        <button type="submit" class="cta-button" style="height: 40px; width: 100px;">Filtriraj</button>
        <a href="manage_term.php"><button type="button" class="cta-button" style="height: 40px; width: 150px;">Poništi filter</button></a>
    </form>

    <?php if ($editing): ?>
        <h2>Izmena termina</h2>
        <form method="post">
            <input type="hidden" name="update_id" value="<?= $editing['id'] ?>">
            <input type="datetime-local" name="appointment_date"
                   value="<?= date('Y-m-d\TH:i', strtotime($editing['appointment_date'])) ?>" required >
            <select name="status">
                <option value="zakazano" <?= $editing['status'] === 'zakazano' ? 'selected' : '' ?>>Zakazano</option>
                <option value="obavljeno" <?= $editing['status'] === 'obavljeno' ? 'selected' : '' ?>>Obavljeno</option>
                <option value="otkazano" <?= $editing['status'] === 'otkazano' ? 'selected' : '' ?>>Otkazano</option>
            </select>
            <input type="text" name="notes" placeholder="Napomena" value="<?= htmlspecialchars($editing['notes'] ?? '') ?>">
            <button type="submit" name="update" >Izmeni</button>
        </form>
    <?php endif; ?>

    <h2><?= $filter_date ? 'Termini za: ' . htmlspecialchars($filter_date) : 'Svi termini' ?></h2>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Datum</th>
            <th>Početak</th>
            <th>Kraj</th>
            <th>Ime vlasnika</th>
            <th>Ljubimac</th>
            <th>Veterinar</th>
        </tr>
        <?php foreach ($appointments as $a): ?>
            <tr>
                <td><?= htmlspecialchars(date('Y-m-d', strtotime($a['appointment_date']))) ?></td>
                <td><?= htmlspecialchars($a['start_time']) ?></td>
                <td><?= htmlspecialchars($a['end_time']) ?></td>
                <td><?= htmlspecialchars($a['owner_name']) ?></td>
                <td><?= htmlspecialchars($a['pet_name']) ?></td>
                <td><?= htmlspecialchars($a['vet_first_name'] . ' ' . $a['vet_last_name']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>


</div>
</body>
</html>
