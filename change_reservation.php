<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Obrada POST zahteva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $appointmentId = (int)($_POST['appointment_id'] ?? 0);
    $newSlotId = (int)($_POST['new_slot_id'] ?? 0);

    if ($action === 'delete' && $appointmentId) {
        free_appointment($pdo, $appointmentId);
        safeRedirect('change_reservation.php');
    }

    if ($action === 'edit' && $appointmentId && $newSlotId) {
        if (is_schedule_slot_taken($pdo, $newSlotId)) {
            echo "<script>alert('Termin je već zauzet.'); window.location.href='change_reservation.php';</script>";
            exit;
        }

        $pet_id = get_pet_id_by_appointment($pdo, $appointmentId);
        if (pet_has_appointment($pdo, $pet_id, $newSlotId)) {
            echo "<script>alert('Ljubimac već ima zakazan termin u ovom terminu.'); window.location.href='change_reservation.php';</script>";
            exit;
        }

        rebook_appointment($pdo, $appointmentId, $newSlotId);
        safeRedirect('change_reservation.php');
    }

    if ($action === 'create') {
        $schedule_id = (int)($_POST['reservation'][0] ?? 0);
        if ($schedule_id && $user_id) {
            $schedule = get_schedule_by_id($pdo, $schedule_id);
            if ($schedule) {
                $pet_id = isset($_POST['pet_id']) ? (int)$_POST['pet_id'] : null;
                if (!$pet_id) {
                    die("❌ Greška: Niste izabrali ljubimca.");
                }

                if (is_schedule_slot_taken($pdo, $schedule_id)) {
                    echo "<script>alert('❌ Termin je već zauzet!'); window.location.href='change_reservation.php';</script>";
                    exit;
                }

                if (pet_has_appointment($pdo, $pet_id, $schedule_id)) {
                    echo "<script>alert('❌ Ovaj ljubimac već ima termin u to vreme!'); window.location.href='change_reservation.php';</script>";
                    exit;
                }

                $service_id = 1;

                create_appointment(
                    $pdo,
                    $pet_id,
                    $schedule['veterinarian_id'],
                    $service_id,
                    $schedule['start_time'],
                    'zakazano',
                    '',
                    $user_id,
                    $schedule['id']
                );

                safeRedirect('change_reservation.php');
            }
        }
    }
}

$pets = get_pets_by_user($pdo, $user_id);
$selectedPetId = $_GET['pet_id'] ?? '';
$petAppointments = $selectedPetId ? get_appointments_for_pet($pdo, $selectedPetId) : [];
$availableSlots = getFreeSlots($pdo);
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Zakazani termini</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="pet_information.php">Moj ljubimac</a></li>
            <li><a href="pet_treatments.php">Tretman ljubimca</a></li>
            <li><a href="users_reservation.php">Rezervacije</a></li>
            <li><a href="change_reservation.php" class="active">Promeni rezervaciju</a></li>
            <li><a href="user_information.php">Moj nalog</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<h2>Zakazani termini</h2>

<form method="GET" action="">
    <label for="pet_id">Izaberi ljubimca:</label>
    <select name="pet_id" id="pet_id" onchange="this.form.submit()">
        <option value="">-- svi ljubimci --</option>
        <?php foreach ($pets as $pet): ?>
            <option value="<?= $pet['id'] ?>" <?= $pet['id'] == $selectedPetId ? 'selected' : '' ?>>
                <?= htmlspecialchars($pet['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($petAppointments): ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>Datum</th>
            <th>Vreme</th>
            <th>Veterinar</th>
            <th>Akcija</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($petAppointments as $appt): ?>
            <tr>
                <form method="POST">
                    <td><?= htmlspecialchars(date('Y-m-d', strtotime($appt['appointment_date']))) ?></td>
                    <td><?= htmlspecialchars($appt['time_slot']) ?></td>
                    <td><?= htmlspecialchars($appt['vet_name']) ?></td>
                    <td>
                        <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                        <button type="submit" name="action" value="delete" onclick="return confirm('Sigurno brišeš termin?')">Obriši</button>

                        <select name="new_slot_id">
                            <option value="">-- novi termin --</option>
                            <?php foreach ($availableSlots as $slot): ?>
                                <option value="<?= $slot['id'] ?>">
                                    <?= date('Y-m-d H:i', strtotime($slot['start_time'])) ?> - <?= date('H:i', strtotime($slot['end_time'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="action" value="edit">Izmeni</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="text-align: center;">Nema zakazanih termina za izabranog ljubimca.</p>
<?php endif; ?>

</body>
</html>
