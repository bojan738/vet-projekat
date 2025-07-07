<?php
session_start();
require_once 'auth.php';
requireRegularUser();
require_once 'db_config.php';
require_once 'functions.php';

date_default_timezone_set('Europe/Belgrade');

$ordinacija = new VeterinarskaOrdinacija();
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// CANCEL TERM
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $appointmentId = (int)($_POST['appointment_id'] ?? 0);
    $newSlotId = (int)($_POST['new_slot_id'] ?? 0);

    if ($action === 'delete' && $appointmentId) {
        $appointment = $ordinacija->getUserAppointmentForCancellation($appointmentId, $user_id);

        if ($appointment) {
            $datetimeTermin = strtotime($appointment['appointment_date'] . ' ' . $appointment['start_time']);
            $datetimeNow = time();

            if (($datetimeTermin - $datetimeNow) >= 4 * 3600) {
                $ordinacija->freeAppointment($appointmentId);
            } else {
                echo "<script>alert('❌ Termin se može otkazati samo najmanje 4 sata pre početka.'); window.location.href='change_reservation.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('❌ Nevažeći ili nedozvoljeni termin.'); window.location.href='change_reservation.php';</script>";
            exit;
        }

        header("Location: change_reservation.php");
        exit;
    }

    // EDIT TERM
    if ($action === 'edit' && $appointmentId && $newSlotId) {
        $appt = $ordinacija->getAppointmentById1($appointmentId);
        $date = date('Y-m-d', strtotime($appt['appointment_date']));
        $vet_id = $appt['veterinarian_id'];
        $pet_id = $appt['pet_id'];

        $slot = $ordinacija->getScheduleSlotById($newSlotId);
        if (!$slot) {
            echo "<script>alert('❌ Neispravan termin.'); window.location.href='change_reservation.php';</script>";
            exit;
        }

        if ($ordinacija->isTimeSlotTaken($newSlotId, $date, $vet_id)) {
            echo "<script>alert('❌ Termin je već zauzet.'); window.location.href='change_reservation.php';</script>";
            exit;
        }

        if ($ordinacija->petHasAppointmentForDateAndSlot($pet_id, $newSlotId, $date)) {
            echo "<script>alert('❌ Ljubimac već ima termin u tom vremenu.'); window.location.href='change_reservation.php';</script>";
            exit;
        }

        $ordinacija->updateAppointmentSlot($appointmentId, $newSlotId, $slot['start_time'], $slot['end_time']);
        header("Location: change_reservation.php");
        exit;
    }
}

$pets = $ordinacija->getPetsByUser1($user_id);
$selectedPetId = $_GET['pet_id'] ?? '';
$petAppointments = $selectedPetId ? $ordinacija->getAppointmentsForPet($selectedPetId) : [];
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Promeni rezervaciju</title>
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
<main>
<div style="padding-left: 20px; padding-right: 20px" >
<h2>Zakazani termini</h2>

<form method="GET">
    <label for="pet_id" class="form-label vet-form-label">Izaberi ljubimca:</label>
    <select name="pet_id" id="pet_id" onchange="this.form.submit()" class="form-input vet-form-input" style="width: 200px;">
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
            <th>Obriši</th>
            <th>Izmena termina</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($petAppointments as $appt): ?>
            <tr>
                <form method="POST">
                    <td><?= htmlspecialchars($appt['appointment_date']) ?></td>
                    <td><?= htmlspecialchars($appt['time_slot']) ?></td>
                    <td><?= htmlspecialchars($appt['vet_name']) ?></td>
                    <td>
                        <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                        <button type="submit" name="action" value="delete" onclick="return confirm('Obrisati ovaj termin?')" class="cta-button" style="height: 40px; width: 100px;">Obriši</button>
                    </td>
                </form>
                <form method="POST">
                    <td colspan="2">
                        <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                        <?php
                        $availableSlots = $ordinacija->getAvailableScheduleSlots(
                            $appt['veterinarian_id'],
                            $appt['appointment_date'],
                            $appt['schedule_id']
                        );
                        ?>
                        <select name="new_slot_id" required class="form-input vet-form-input" style=" width: 200px;">
                            <option value="">-- novi termin --</option>
                            <?php foreach ($availableSlots as $slot): ?>
                                <option value="<?= $slot['id'] ?>">
                                    <?= substr($slot['start_time'], 0, 5) ?> - <?= substr($slot['end_time'], 0, 5) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="action" value="edit" class="cta-button" style="height: 40px; width: 100px;">Izmeni</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
    <p style="text-align:center;">Nema termina za izabranog ljubimca.</p>
<?php endif; ?>
</main>
<footer class="custom-footer">
    <div class="footer-content">
        &copy; 2025 PetCare Ordinacija. Sva prava zadržana.
    </div>
</footer>
</body>
</html>

