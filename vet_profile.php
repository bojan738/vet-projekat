<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

$vetId = $_SESSION['vet_id'];
$vetInfo = get_vet_info($pdo, $vetId);
$schedule = get_vet_schedule($pdo, $vetId);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upravljanje radnim vremenom</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="vet_treatments_info.php"> Tretmani</a></li>
            <li><a href="vet_electronic_card.php">Karton</a></li>
            <li><a href="vet_profile.php" class="active"> Vet profil</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<h2>Profil veterinara</h2>
<img src="images/veterinarians/<?= htmlspecialchars($vetInfo['photo']) ?>" width="150">
<p><strong>Ime:</strong> <?= htmlspecialchars($vetInfo['first_name']) ?></p>
<p><strong>Prezime:</strong> <?= htmlspecialchars($vetInfo['last_name']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($vetInfo['email']) ?></p>
<p><strong>Telefon:</strong> <?= htmlspecialchars($vetInfo['phone_number']) ?></p>
<p><strong>Specijalizacija:</strong> <?= htmlspecialchars($vetInfo['specialization']) ?></p>
<p><strong>Licenca:</strong> <?= htmlspecialchars($vetInfo['license_number']) ?></p>

<h2>Upravljanje radnim vremenom</h2>

<form method="POST" action="vet_add_schedule.php">
    <label for="datum">Datum:</label>
    <input type="date" id="datum" name="datum" required>
    <label for="vreme_start">Početak:</label>
    <input type="time" id="vreme_start" name="vreme_start" required>
    <label for="vreme_end">Kraj:</label>
    <input type="time" id="vreme_end" name="vreme_end" required>
    <button type="submit">Dodaj termin</button>
</form>

<h3>Vaši termini:</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
    <tr>
        <th>Početak</th>
        <th>Kraj</th>
        <th>Akcije</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($schedule as $row): ?>
        <tr>
            <td><?= date('d.m.Y. H:i', strtotime($row['start_time'])) ?></td>
            <td><?= date('d.m.Y. H:i', strtotime($row['end_time'])) ?></td>
            <td>
                <form method="POST" action="vet_schedule_edit.php" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit">Izmeni</button>
                </form>
                <form method="POST" action="vet_schedule_delete.php" style="display:inline;" onsubmit="return confirm('Da li ste sigurni da želite da obrišete ovaj termin?');">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit">Obriši</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
