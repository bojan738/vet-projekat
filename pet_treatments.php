<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

$pets = get_pets_by_user($pdo, $user_id);
$selectedPetId = $_GET['pet_id'] ?? '';
$treatments = $selectedPetId ? get_treatments_by_pet($pdo, $selectedPetId) : [];
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Informacije o tretmanima ljubimca</title>
    <link rel="stylesheet" href="css/css.css">
    <style>
        .no-treatments {
            margin-top: 20px;
            font-weight: bold;
            text-align: center;
        }
        .add-link {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #4caf50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .add-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="pet_information.php">Moj ljubimac</a></li>
            <li><a href="pet_treatments.php" class="active">Tretman ljubimca</a></li>
            <li><a href="users_reservation.php">Rezervacije</a></li>
            <li><a href="change_reservation.php">Promeni rezervaciju</a></li>
            <li><a href="user_information.php">Moj nalog</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<h2>Informacije o tretmanima ljubimca</h2>

<form method="GET" action="">
    <label for="pet_id">Izaberi ljubimca:</label>
    <select name="pet_id" id="pet_id" onchange="this.form.submit()">
        <option value="">-- Izaberite ljubimca --</option>
        <?php foreach ($pets as $pet): ?>
            <option value="<?= $pet['id'] ?>" <?= $pet['id'] == $selectedPetId ? 'selected' : '' ?>>
                <?= htmlspecialchars($pet['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($selectedPetId && count($treatments) > 0): ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>Datum tretmana</th>
            <th>Vrsta tretmana</th>
            <th>Veterinar</th>
            <th>Status</th>
            <th>Napomene</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($treatments as $t): ?>
            <tr>
                <td><?= htmlspecialchars($t['appointment_date']) ?></td>
                <td><?= htmlspecialchars($t['service_name']) ?></td>
                <td><?= htmlspecialchars($t['vet_name']) ?></td>
                <td><?= htmlspecialchars($t['status']) ?></td>
                <td><?= htmlspecialchars($t['notes']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php elseif ($selectedPetId): ?>
    <div class="no-treatments">
        Ovaj ljubimac još nema nijedan tretman.<br>
        <a href="users_reservation.php" class="add-link">➕ Zakazi termin</a>
    </div>
<?php endif; ?>

</body>
</html>
