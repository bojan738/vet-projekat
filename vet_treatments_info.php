<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

$vetId = $_SESSION['vet_id'];

// Dohvati sve zakazane termine za ovog veterinara
$tretmani = get_appointments_for_vet($pdo, $vetId);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Zakazani tretmani</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="vet_treatments_info.php" class="active">Tretmani</a></li>
            <li><a href="vet_electronic_card.php">Karton</a></li>
            <li><a href="vet_profile.php">Vet profil</a></li>
            <li><a href="vet_schedule.php">Radno vreme</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<h2>Zakazani tretmani</h2>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Datum</th>
        <th>Vreme</th>
        <th>Vlasnik</th>
        <th>Ljubimac</th>
        <th>Detalji</th>
    </tr>
    <?php foreach ($tretmani as $t): ?>
        <tr>
            <td><?= date('Y-m-d', strtotime($t['appointment_date'])) ?></td>
            <td><?= date('H:i', strtotime($t['start_time'])) ?></td>
            <td><?= htmlspecialchars($t['owner_name']) ?></td>
            <td><?= htmlspecialchars($t['pet_name']) ?></td>
            <td>
                <?php echo '<a href="vet_treatments_details.php?appointment_id=' . $t['appointment_id'] . '">Detalji</a>'; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
