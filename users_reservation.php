<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

$veterinari = get_all_vets($pdo);
$selectedVetId = $_GET['vet_id'] ?? '';
$pets = get_pets_by_user($pdo, $user_id);
$selectedPetId = $_GET['pet_id'] ?? '';
$availableSlots = $selectedVetId ? get_available_terms_by_vet($pdo, $selectedVetId) : [];

// Obrada POST zahteva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $appointmentId = (int)($_POST['appointment_id'] ?? 0);
    $newSlotId = (int)($_POST['new_slot_id'] ?? 0);
    $pet_id = isset($_POST['pet_id']) ? (int)$_POST['pet_id'] : null;
    $schedule_id = isset($_POST['reservation'][0]) ? (int)$_POST['reservation'][0] : 0;

    if ($action === 'create' && $schedule_id && $pet_id) {
        $schedule = get_schedule_by_id($pdo, $schedule_id);

        if (!$schedule || is_schedule_slot_taken($pdo, $schedule_id) || pet_has_appointment($pdo, $pet_id, $schedule_id)) {
            echo "<script>alert('❌ Termin nije dostupan ili ljubimac već ima zakazan termin!'); window.location.href='change_reservation.php?vet_id=$selectedVetId';</script>";
            exit;
        }

        create_appointment(
            $pdo,
            $pet_id,
            $schedule['veterinarian_id'],
            1,
            $schedule['start_time'],
            'zakazano',
            '',
            $user_id,
            $schedule['id']
        );

        safeRedirect('change_reservation.php');
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Slobodni termini</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="pet_information.php">Moj ljubimac</a></li>
            <li><a href="pet_treatments.php">Tretman ljubimca</a></li>
            <li><a href="users_reservation.php" class="active">Rezervacije</a></li>
            <li><a href="change_reservation.php" >Promeni rezervaciju</a></li>
            <li><a href="user_information.php">Moj nalog</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<h2>Slobodni termini</h2>

<form method="GET" action="">
    <label for="vet_id">Izaberi veterinara:</label>
    <select name="vet_id" id="vet_id" onchange="this.form.submit()">
        <option value="">-- svi veterinari --</option>
        <?php foreach ($veterinari as $vet): ?>
            <option value="<?= $vet['id'] ?>" <?= $vet['id'] == $selectedVetId ? 'selected' : '' ?>>
                <?= htmlspecialchars($vet['first_name'] . ' ' . $vet['last_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<form method="POST">
    <label for="pet_id">Izaberi ljubimca:</label>
    <select name="pet_id" id="pet_id" required>
        <option value="">-- ljubimac --</option>
        <?php foreach ($pets as $pet): ?>
            <option value="<?= $pet['id'] ?>" <?= $pet['id'] == $selectedPetId ? 'selected' : '' ?>>
                <?= htmlspecialchars($pet['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>Izaberi</th>
            <th>Datum</th>
            <th>Vreme</th>
            <th>Veterinar</th>
            <th>Vlasnik ljubimca</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($availableSlots) > 0): ?>
            <?php foreach ($availableSlots as $slot): ?>
                <tr>
                    <td><input type="radio" name="reservation[]" value="<?= $slot['id'] ?>"></td>
                    <td><?= htmlspecialchars($slot['date']) ?></td>
                    <td><?= htmlspecialchars($slot['start_time']) ?> - <?= htmlspecialchars($slot['end_time']) ?></td>
                    <td><?= htmlspecialchars($slot['vet_name']) ?></td>
                    <td></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">Trenutno nema slobodnih termina.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <br>
    <button type="submit" name="action" value="create">Zakaži</button>
</form>
</body>
</html>
